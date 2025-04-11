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
use PhpBg\DvbPsi\Tables\Values\SdtRunningStatus;

class SdtService
{
    /**
     * This is a 16-bit field which serves as a label to identify this service from any other service within the TS.
     * The service_id is the same as the program_number in the corresponding program_map_section.
     * @var int
     */
    public $serviceId;

    /**
     * This is a 1-bit field which when set to "1" indicates that EIT schedule information for the service
     * is present in the current TS, see ETSI TS 101 211 [i.1] for information on maximum time interval between occurrences
     * of an EIT schedule sub_table). If the flag is set to 0 then the EIT schedule information for the service should not be
     * present in the TS.
     * @var int
     */
    public $eitScheduleFlag;

    /**
     * This is a 1-bit field which when set to "1" indicates that EIT_present_following
     * information for the service is present in the current TS, see ETSI TS 101 211 [i.1] for information on maximum time
     * interval between occurrences of an EIT present/following sub_table. If the flag is set to 0 then the EIT present/following
     * information for the service should not be present in the TS.
     * @var int
     */
    public $eitPresentFollowingFlag;

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

    public function getRunningStatus(): SdtRunningStatus {
        if (! isset($this->runningStatus)) {
            return SdtRunningStatus::UNDEFINED();
        }
        return new SdtRunningStatus($this->runningStatus);
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
        $str = sprintf("Service id: %d (0x%x)\n", $this->serviceId, $this->serviceId);
        $runningStatus = $this->getRunningStatus();
        $str .= "Running status: " . $runningStatus->getKey() . "\n";
        $str .= sprintf("Scrambled: %s\n", $this->freeCaMode === 1 ? 'yes' : 'no');
        foreach ($this->descriptors as $descriptor) {
            $str .= "{$descriptor}\n";
        }
        return $str;
    }
}