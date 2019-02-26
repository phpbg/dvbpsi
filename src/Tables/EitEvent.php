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

namespace PhpBg\DvbPsi\Tables;

use PhpBg\DvbPsi\Descriptors\ShortEvent;
use PhpBg\DvbPsi\Tables\Values\EitRunningStatus;

class EitEvent
{
    /**
     * This 16-bit field contains the identification number of the described event (uniquely allocated within a
     * service definition).
     * @var int
     */
    public $eventId;

    /**
     * Unix timestamp derived from start_time : This 40-bit field contains the start time of the event in Universal Time, Co-ordinated (UTC) and Modified
     * Julian Date (MJD) (see annex C). If the start time is undefined (e.g. for an event in a NVOD reference
     * service) all bits of the field are set to "1".
     * @var int
     */
    public $startTimestamp;

    /**
     * Unix timestamp derived from duration: A 24-bit field containing the duration of the event in hours, minutes, seconds. format: 6 digits,
     * 4-bit BCD = 24 bit.
     * @var int
     */
    public $duration;

    /**
     * This is a 3-bit field indicating the status of the event as defined in table 6. For an NVOD reference
     * event the value of the running_status shall be set to "0".
     * @var
     */
    public $runningStatus;

    /**
     * This 1-bit field, when set to "0" indicates that all the component streams of the event are not
     * scrambled. When set to "1" it indicates that access to one or more streams is controlled by a CA system.
     * @var boolean
     */
    public $freeCaMode;

    public $descriptors = [];

    public function getRunningStatus(): EitRunningStatus {
        if (! isset($this->runningStatus)) {
            return EitRunningStatus::UNDEFINED();
        }
        return new EitRunningStatus($this->runningStatus);
    }

    /**
     * This is a convenient method to get text from short event descriptor (if any)
     * @return null|string
     */
    public function getShortEventText() {
        foreach ($this->descriptors as $descriptor) {
            if (! $descriptor instanceof ShortEvent) {
                continue;
            }
            return $descriptor->eventName . ' ' . $descriptor->text;
        }
        return null;
    }

    public function __toString()
    {
        $str = sprintf("Event id: %d (0x%x)\n", $this->eventId, $this->eventId);
        $str .= sprintf("Start %d (%s)\n", $this->startTimestamp, date('Y-m-d H:i:s', $this->startTimestamp));
        $str .= sprintf("Duration %d (until %s)\n", $this->duration, date('Y-m-d H:i:s', $this->startTimestamp + $this->duration));
        $runningStatus = $this->getRunningStatus();
        $str .= "Running status: " . $runningStatus->getKey() . "\n";
        $str .= sprintf("Scrambled: %s\n", $this->freeCaMode === 1 ? 'yes' : 'no');
        foreach ($this->descriptors as $descriptor) {
            $str .= "{$descriptor}\n";
        }
        return $str;
    }
}