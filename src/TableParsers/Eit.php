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

namespace PhpBg\DvbPsi\TableParsers;

use PhpBg\DvbPsi\Exception;
use PhpBg\DvbPsi\Tables\EitEvent;
use PhpBg\DvbPsi\Tables\Identifier;
use PhpBg\MpegTs\Pid;

class Eit extends TableParserAbstract
{

    use Timestamp;

    public function getPids(): array
    {
        return [Pid::EIT_ST_CIT];
    }

    public function getTableIds(): array
    {
        return array_merge(
            [
                Identifier::EVENT_INFORMATION_SECTION_ACTUAL_TS_PRESENT_FOLLOWING,
                Identifier::EVENT_INFORMATION_SECTION_OTHER_TS_PRESENT_FOLLOWING
            ],
            Identifier::EVENT_INFORMATION_SECTION_ACTUAL_TS_PRESENT_SCHEDULE,
            Identifier::EVENT_INFORMATION_SECTION_OTHER_TS_PRESENT_SCHEDULE
        );
    }

    public function getEventName(): string
    {
        return 'eit';
    }

    public function parse(int $tableId, string $data, int $currentPointer, int $sectionLength)
    {
        $eit = new \PhpBg\DvbPsi\Tables\Eit();
        $eit->tableId = $tableId;

        $crcOffset = $currentPointer + $sectionLength - 4;

        $eit->serviceId = unpack('n', substr($data, $currentPointer, 2))[1];
        $currentPointer += 2;

        $tmp = unpack('C', $data[$currentPointer])[1];
        $eit->versionNumber = ($tmp >> 1) & 0x1f;
        $eit->currentNextIndicator = $tmp & 0x01;
        $currentPointer += 1;

        $eit->sectionNumber = unpack('C', $data[$currentPointer])[1];
        $currentPointer += 1;

        $eit->lastSectionNumber = unpack('C', $data[$currentPointer])[1];
        $currentPointer += 1;

        $eit->transportStreamId = unpack('n', substr($data, $currentPointer, 2))[1];
        $currentPointer += 2;

        $eit->originalNetworkId = unpack('n', substr($data, $currentPointer, 2))[1];
        $currentPointer += 2;

        $eit->segmentLastSectionNumber = unpack('C', $data[$currentPointer])[1];
        $currentPointer += 1;

        $eit->lastTableId = unpack('C', $data[$currentPointer])[1];
        $currentPointer += 1;

        while ($currentPointer < $crcOffset) {
            $eitEvent = new EitEvent();

            $eitEvent->eventId = unpack('n', substr($data, $currentPointer, 2))[1];
            $currentPointer += 2;

            $startTime = substr($data, $currentPointer, 5);
            $currentPointer += 5;
            $eitEvent->startTimestamp = $this->getTimestampFromMjdUtc($startTime);

            $duration = substr($data, $currentPointer, 3);
            $currentPointer += 3;
            $eitEvent->duration = $this->getTimestampFromUtcBcd($duration);

            $tmp = unpack('n', substr($data, $currentPointer, 2))[1];
            $currentPointer += 2;
            $eitEvent->runningStatus = ($tmp >> 13) & 0x7;
            $eitEvent->freeCaMode = ($tmp >> 12) & 0x1;
            $loopLength = $tmp & 0xfff;
            if ($currentPointer + $loopLength > $crcOffset) {
                throw new Exception("Unexpected descriptors loop length");
            }
            $eitEvent->descriptors = $this->parseDescriptorsLoop($data, $currentPointer, $loopLength);
            $currentPointer += $loopLength;

            $eit->events[] = $eitEvent;
        }

        //TODO check CRC
        $crc = unpack('N', substr($data, $currentPointer, 4))[1];

        return $eit;
    }
}