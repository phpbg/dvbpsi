<?php

/**
 * MIT License
 *
 * Copyright (c) 2018 Samuel CHEMLA
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */

namespace PhpBg\DvbPsi\Context;

use PhpBg\DvbPsi\Exception;
use PhpBg\DvbPsi\Tables\Eit;
use PhpBg\DvbPsi\Tables\EitEvent;
use PhpBg\DvbPsi\Tables\Identifier;

/**
 * Class EitServiceAggregator
 * Aggregates all EIT events on a given service/stream/network
 *
 * update event:
 *     The `update` event will be emitted when new EIT that is not already known is received
 *     The event will receive a single argument: PhpBg\DvbPsi\Tables\Eit instance
 */
class EitServiceAggregator
{
    public $originalNetworkId;
    public $transportStreamId;
    public $serviceId;

    protected $followingVersion = null;
    protected $scheduledVersions = [];
    protected $followingEvents = [];
    protected $scheduledEvents = [];
    protected $followingSections = [];
    protected $scheduledSections = [];

    protected $firstTableId;
    protected $lastTableId;
    protected $tablesLastSectionNumber = [];

    /**
     * Aggregate a new EIT
     *
     * @param Eit $eit
     * @throws Exception
     * @return bool Return true if the EIT was unknown and has been aggregated, false otherwise
     */
    public function add(Eit $eit): bool
    {
        if (!isset($this->originalNetworkId)) {
            $this->originalNetworkId = $eit->originalNetworkId;
            $this->transportStreamId = $eit->transportStreamId;
            $this->serviceId = $eit->serviceId;
        } else {
            if ($this->originalNetworkId !== $eit->originalNetworkId) {
                throw new Exception("Event and aggregator mismatch");
            }
            if ($this->transportStreamId !== $eit->transportStreamId) {
                throw new Exception("Event and aggregator mismatch");
            }
            if ($this->serviceId !== $eit->serviceId) {
                throw new Exception("Event and aggregator mismatch");
            }
        }
        if ($eit->tableId === Identifier::EVENT_INFORMATION_SECTION_ACTUAL_TS_PRESENT_FOLLOWING || $eit->tableId === Identifier::EVENT_INFORMATION_SECTION_OTHER_TS_PRESENT_FOLLOWING) {
            if (!isset($this->followingVersion) || $this->followingVersion < $eit->versionNumber || ($this->followingVersion !== 0 && $eit->versionNumber === 0)) {
                //New following version
                $this->followingVersion = $eit->versionNumber;
                $this->followingEvents = [];
                $this->followingSections = [];
            }

            if (!isset($this->followingSections[$eit->sectionNumber])) {
                $this->followingSections[$eit->sectionNumber] = true;
                $this->followingEvents = array_merge($this->followingEvents, $eit->events);
                return true;
            }
        } else {
            // version are valid per table id
            if (!isset($this->scheduledVersions[$eit->tableId]) || $this->scheduledVersions[$eit->tableId] < $eit->versionNumber || ($this->scheduledVersions[$eit->tableId] !== 0 && $eit->versionNumber === 0)) {
                //New scheduled version
                $this->scheduledVersions[$eit->tableId] = $eit->versionNumber;
                $this->scheduledEvents[$eit->tableId] = [];
                $this->scheduledSections[$eit->tableId] = [];

                if (in_array($eit->tableId, Identifier::EVENT_INFORMATION_SECTION_ACTUAL_TS_PRESENT_SCHEDULE)) {
                    $this->firstTableId = 0x50;
                } else {
                    $this->firstTableId = 0x60;
                }
                $this->lastTableId = $eit->lastTableId;
                $this->tablesLastSectionNumber = [];
            }

            if (!isset($this->tablesLastSectionNumber[$eit->tableId])) {
                $this->tablesLastSectionNumber[$eit->tableId] = $eit->lastSectionNumber;
            }

            if (!isset($this->scheduledSections[$eit->tableId][$eit->sectionNumber])) {
                $this->scheduledSections[$eit->tableId][$eit->sectionNumber] = true;
                $this->scheduledEvents[$eit->tableId] = array_merge($this->scheduledEvents[$eit->tableId], $eit->events);
                return true;
            }
        }
        return false;
    }

    /**
     * Return running event at a specified timestamp, which defaults to now
     *
     * @return EitEvent|null
     * @param int $timestamp
     */
    public function getRunningEvent(int $timestamp = null)
    {
        if (!isset($timestamp)) {
            $timestamp = time();
        }
        foreach ($this->getAllEvents() as $event) {
            if ($event->startTimestamp > $timestamp) {
                // Future event
                continue;
            }
            if ($event->startTimestamp + $event->duration < $timestamp) {
                // Past event
                continue;
            }
            return $event;
        }
        return null;
    }

    /**
     * Return all following events
     * Note that there is generally 2 events per channel, one running, and the other one coming
     *
     * @return EitEvent[]
     */
    public function getFollowingEvents(): array
    {
        return $this->followingEvents;
    }

    /**
     * Return all scheduled events
     *
     * @return EitEvent[]
     */
    public function getScheduledEvents(): array
    {
        $events = [];
        foreach ($this->scheduledEvents as $eitEvents) {
            $events = array_merge($events, $eitEvents);
        }
        return $events;
    }

    /**
     * Return all collected events (following and scheduled)
     *
     * @return EitEvent[]
     */
    public function getAllEvents(): array {
        $scheduledEvents = $this->getScheduledEvents();
        foreach ($this->followingEvents as $eitEvent) {
            if (! in_array($eitEvent, $scheduledEvents)) {
                $scheduledEvents[] = $eitEvent;
            }
        }
        return $scheduledEvents;
    }

    /**
     * Return the percent of aggregated events
     * NB: this number may be inaccurate because the spec allows for gap in numbering.
     * "the sub_table may be structured as a number of segments. Within each segment the section_number shall increment by 1 with each additional section, but a gap in numbering is permitted between the last section of a segment and the first section of the adjacent segment."
     *
     * @return int
     */
    public function getStat()
    {
        if (!isset($this->firstTableId)) {
            return 0;
        }
        $required = 0;
        $have = 0;
        for ($tableId = $this->firstTableId; $tableId <= $this->lastTableId; $tableId++) {
            if (!isset($this->tablesLastSectionNumber[$tableId])) {
                // if last section number is unknown we fake the maximum value
                // this is inaccurate but there's nothing better we can do
                $required += 256;
                continue;
            }
            // Required is the last section number plus 1 because numbering starts at zero
            $required += $this->tablesLastSectionNumber[$tableId] + 1;
            $have += count($this->scheduledSections[$tableId]);
        }
        if ($required == 0) {
            return 0;
        }
        return round(100.0 * $have / $required);
    }

    public function __toString()
    {
        $str = '';

        if (!empty($this->followingEvents)) {
            $str .= "Following events:\n";
            foreach ($this->followingEvents as $eventId => $event) {
                $str .= sprintf("Event: %d (0x%x)\n", $eventId, $eventId);
                $str .= (string)$event;
            }
        }

        if (!empty($this->scheduledEvents)) {
            $str .= "Scheduled events:\n";
            foreach ($this->scheduledEvents as $tableId => $events) {
                $str .= sprintf("From table: %d (0x%x)\n", $tableId, $tableId);
                foreach ($events as $eventId => $event) {
                    $str .= sprintf("Event: %d (0x%x)\n", $eventId, $eventId);
                    $str .= (string)$event;
                }
            }
        }

        $str .= "Summary:\n";
        $str .= sprintf("\tTransport stream ID: %d (0x%x)\n", $this->transportStreamId, $this->transportStreamId);
        $str .= sprintf("\tNetwork ID: %d (0x%x)\n", $this->originalNetworkId, $this->originalNetworkId);
        $str .= sprintf("\tService id: %d (0x%x)\n", $this->serviceId, $this->serviceId);
        $stat = $this->getStat();
        $str .= "\t{$stat}% of the events have been collected\n";

        $currentEvent = $this->getRunningEvent();
        if (!isset($currentEvent)) {
            $str .= "\tNo current running event collected\n";
        } else {
            $text = $currentEvent->getShortEventText();
            $str .= "\tCurrent running event: {$text}\n";
            if (empty($text)) {
                $str .= (string)$currentEvent;
            }
        }

        return $str;
    }
}