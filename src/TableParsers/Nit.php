<?php

/**
 * MIT License
 *
 * Copyright (c) 2019 Samuel CHEMLA
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
use PhpBg\DvbPsi\Tables\Identifier;
use PhpBg\DvbPsi\Tables\NitTs;
use PhpBg\MpegTs\Pid;

/**
 * NIT parser
 * @see \PhpBg\DvbPsi\Tables\
 */
class Nit extends TableParserAbstract
{
    public function getPids(): array
    {
        return [Pid::NIT_ST];
    }

    public function getTableIds(): array
    {
        return [Identifier::NETWORK_INFORMATION_SECTION_ACTUAL_NETWORK, Identifier::NETWORK_INFORMATION_SECTION_OTHER_NETWORK];
    }

    public function getEventName(): string
    {
        return 'nit';
    }

    public function parse(int $tableId, string $data, int $currentPointer, int $sectionLength)
    {
        $crcOffset = $currentPointer + $sectionLength - 4;

        $nit = new \PhpBg\DvbPsi\Tables\Nit();
        $nit->networkId = unpack('n', substr($data, $currentPointer, 2))[1];
        $currentPointer += 2;

        $tmp = unpack('C', $data[$currentPointer])[1];
        $nit->versionNumber = ($tmp >> 1) & 0x1f;
        $nit->currentNextIndicator = $tmp & 0x01;
        $currentPointer += 1;

        $nit->sectionNumber = unpack('C', $data[$currentPointer])[1];
        $currentPointer += 1;

        $nit->lastSectionNumber = unpack('C', $data[$currentPointer])[1];
        $currentPointer += 1;

        // Descriptors
        $tmp = unpack('n', substr($data, $currentPointer, 2))[1];
        $currentPointer += 2;
        $networkDescriptorsLength = $tmp & 0xfff;
        $nit->descriptors = $this->parseDescriptorsLoop($data, $currentPointer, $networkDescriptorsLength);
        $currentPointer += $networkDescriptorsLength;

        // Transport streams
        $tmp = unpack('n', substr($data, $currentPointer, 2))[1];
        $currentPointer += 2;
        $tsDescriptorsLength = $tmp & 0xfff;
        $end = $currentPointer + $tsDescriptorsLength;
        if ($end !== $crcOffset) {
            throw new Exception("Unexpected TS descriptors length");
        }
        while ($currentPointer < $end) {
            $nitTs = new NitTs();

            $nitTs->transportStreamId = unpack('n', substr($data, $currentPointer, 2))[1];
            $currentPointer += 2;

            $nitTs->networkId = unpack('n', substr($data, $currentPointer, 2))[1];
            $currentPointer += 2;

            $tmp = unpack('n', substr($data, $currentPointer, 2))[1];
            $currentPointer += 2;
            $loopLength = $tmp & 0xfff;
            if ($currentPointer + $loopLength > $crcOffset) {
                throw new Exception("Unexpected descriptors loop length");
            }
            $nitTs->descriptors = $this->parseDescriptorsLoop($data, $currentPointer, $loopLength);
            $currentPointer += $loopLength;

            $nit->transportStreams[] = $nitTs;
        }

        //TODO check CRC
        $crc = unpack('N', substr($data, $currentPointer, 4))[1];

        return $nit;
    }
}