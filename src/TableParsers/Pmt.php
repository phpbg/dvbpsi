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

use PhpBg\DvbPsi\Tables\Identifier;
use PhpBg\MpegTs\Pid;

/**
 * PMT parser
 * https://en.wikipedia.org/wiki/Program-specific_information
 */
class Pmt implements TableParserInterface
{

    protected $pids = [];

    /**
     * Set PIDs to parse
     * Should be obtained from PAT
     *
     * @param array $pids
     */
    public function setPids(array $pids)
    {
        // PAT programs generally come with NIT PID
        // Don't register NIT PID as a PMT PID
        $nitPidIndex = array_search(Pid::NIT_ST, $pids);
        if ($nitPidIndex !== false) {
            unset($pids[$nitPidIndex]);
        }
        $this->pids = $pids;

    }

    public function getPids(): array
    {
        // PIDs are dynamically obtained when parsing PAT
        return $this->pids;
    }

    public function getTableIds(): array
    {
        return [Identifier::PROGRAM_MAP_SECTION];
    }

    public function getEventName(): string
    {
        return 'pmt';
    }

    public function parse(int $tableId, string $data, int $currentPointer, int $sectionLength)
    {
        $crcOffset = $currentPointer + $sectionLength - 4;
        $syntaxSectionHeadersBin = substr($data, $currentPointer, 5);
        $currentPointer += 5;
        $syntaxSectionHeadersArray = unpack('n1pn/C3headers', $syntaxSectionHeadersBin);

        $pmt = new \PhpBg\DvbPsi\Tables\Pmt();
        $pmt->programNumber = $syntaxSectionHeadersArray['pn'];
        $pmt->current = $syntaxSectionHeadersArray['headers1'] & 0x01;
        $pmt->version = ($syntaxSectionHeadersArray['headers1'] & 0x3e) >> 1;

        while ($currentPointer < $crcOffset) {
            $pmtDataArray = unpack('N', substr($data, $currentPointer, 4));
            $currentPointer += 4;

            $pcrPid = ($pmtDataArray[1] >> 16) & 0x1FFF;
            if ($pcrPid !== 0x1FFF) {
                $pmt->pcrPid = $pcrPid;
            }

            $programInfoLength = $pmtDataArray[1] & 0x3FF;
            // could not find info on program descriptors so it is completely ignored (yet)
            $currentPointer += $programInfoLength;

            // Elementary stream info data
            while ($currentPointer < $crcOffset) {
                $elementaryStreamHeaderBin = substr($data, $currentPointer, 5);
                $currentPointer += 5;
                $elementaryStreamHeaderArray = unpack('C1st/n2headers', $elementaryStreamHeaderBin);

                $streamType = $elementaryStreamHeaderArray['st'];
                $pid = $elementaryStreamHeaderArray['headers1'] & 0x1FFF;
                $descriptorsLen = $elementaryStreamHeaderArray['headers2'] & 0x3FF;

                //TODO handle $streamType and descriptors
                $currentPointer += $descriptorsLen;

                $pmt->streams[$pid] = null;

            }
        }

        //TODO check CRC
        $crc = unpack('N', substr($data, $currentPointer, 4))[1];

        return $pmt;
    }
}