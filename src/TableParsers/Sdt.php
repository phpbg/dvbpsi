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
use PhpBg\DvbPsi\Tables\SdtService;
use PhpBg\MpegTs\Pid;

class Sdt extends TableParserAbstract
{

    use Timestamp;

    public function getPids(): array
    {
        return [Pid::SDT_BAT_ST];
    }

    public function getTableIds(): array
    {
        return [
            Identifier::SERVICE_DESCRIPTION_SECTION_ACTUAL_TRANSPORT_STREAM,
            Identifier::SERVICE_DESCRIPTION_SECTION_OTHER_TRANSPORT_STREAM
        ];
    }

    public function getEventName(): string
    {
        return 'sdt';
    }

    public function parse(int $tableId, string $data, int $currentPointer, int $sectionLength)
    {
        $sdt = new \PhpBg\DvbPsi\Tables\Sdt();
        $sdt->tableId = $tableId;

        $crcOffset = $currentPointer + $sectionLength - 4;

        $sdt->transportStreamId = unpack('n', substr($data, $currentPointer, 2))[1];
        $currentPointer += 2;

        $tmp = unpack('C', $data[$currentPointer])[1];
        $currentPointer += 1;
        $sdt->versionNumber = ($tmp >> 1) & 0x1f;
        $sdt->currentNextIndicator = $tmp & 0x01;

        $sdt->sectionNumber = unpack('C', $data[$currentPointer])[1];
        $currentPointer += 1;

        $sdt->lastSectionNumber = unpack('C', $data[$currentPointer])[1];
        $currentPointer += 1;

        $sdt->originalNetworkId = unpack('n', substr($data, $currentPointer, 2))[1];
        $currentPointer += 3;
        while ($currentPointer < $crcOffset) {
            $sdtService = new SdtService();

            $sdtService->serviceId = unpack('n', substr($data, $currentPointer, 2))[1];
            $currentPointer += 2;

            $tmp = unpack('C', $data[$currentPointer])[1];
            $currentPointer += 1;
            $sdtService->eitScheduleFlag = ($tmp >> 1) & 0x01;
            $sdtService->eitPresentFollowingFlag = $tmp & 0x01;

            $tmp = unpack('n', substr($data, $currentPointer, 2))[1];
            $currentPointer += 2;
            $sdtService->runningStatus = ($tmp >> 13) & 0x7;
            $sdtService->freeCaMode = ($tmp >> 12) & 0x1;
            $loopLength = $tmp & 0xfff;
            if ($currentPointer + $loopLength > $crcOffset) {
                throw new Exception("Unexpected descriptors loop length");
            }
            $sdtService->descriptors = $this->parseDescriptorsLoop($data, $currentPointer, $loopLength);
            $currentPointer += $loopLength;

            $sdt->services[] = $sdtService;
        }

        //TODO check CRC
        $crc = unpack('N', substr($data, $currentPointer, 4))[1];

        return $sdt;
    }
}