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

use PhpBg\DvbPsi\Tables\Eit;
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
    protected $followingVersion;
    protected $scheduledVersion;
    protected $followingEvents;
    protected $scheduledEvents;
    protected $followingSections;
    protected $scheduledSections;

    /**
     * Aggregate a new EIT
     * Return true if the EIT was unknown and has been aggregated, false otherwise
     *
     * @param Eit $eit
     */
    public function add(Eit $eit): bool
    {
        if ($eit->tableId === Identifier::EVENT_INFORMATION_SECTION_ACTUAL_TS_PRESENT_FOLLOWING || $eit->tableId === Identifier::EVENT_INFORMATION_SECTION_OTHER_TS_PRESENT_FOLLOWING) {
            if (!isset($this->followingVersion) || $this->followingVersion < $eit->versionNumber || ($this->followingVersion !== 0 && $eit->versionNumber === 0)) {
                //New following version
                $this->followingVersion = $eit->versionNumber;
                $this->followingEvents = [];
            }

            if (!isset($this->followingSections[$eit->sectionNumber])) {
                $this->followingSections[$eit->sectionNumber] = true;
                $this->followingEvents = array_merge($this->followingEvents, $eit->events);
                return true;
            }
        } else {
            if (!isset($this->scheduledVersion) || $this->scheduledVersion < $eit->versionNumber || ($this->scheduledVersion !== 0 && $eit->versionNumber === 0)) {
                //New scheduled version
                $this->scheduledVersion = $eit->versionNumber;
                $this->scheduledEvents = [];
            }

            if (!isset($this->scheduledSections[$eit->sectionNumber])) {
                $this->scheduledSections[$eit->sectionNumber] = true;
                $this->scheduledEvents = array_merge($this->scheduledEvents, $eit->events);
                return true;
            }
        }
        return false;
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
            foreach ($this->scheduledEvents as $eventId => $event) {
                $str .= sprintf("Event: %d (0x%x)\n", $eventId, $eventId);
                $str .= (string)$event;
            }
        }

        return $str;
    }
}