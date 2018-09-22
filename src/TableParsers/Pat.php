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
 * PAT parser
 * @see \PhpBg\DvbPsi\Tables\Pat
 */
class Pat implements TableParserInterface
{

    use Timestamp;

    public function getPids(): array
    {
        return [Pid::PAT];
    }

    public function getTableIds(): array
    {
        return [Identifier::PROGRAM_ASSOCIATION_SECTION];
    }

    public function getEventName(): string
    {
        return 'pat';
    }

    public function parse(int $tableId, string $data, int $currentPointer, int $sectionLength)
    {
        $crcOffset = $currentPointer + $sectionLength - 4;
        $syntaxSectionHeadersBin = substr($data, $currentPointer, 5);
        $currentPointer += 5;
        $syntaxSectionHeadersArray = unpack('n1tsid/C3headers', $syntaxSectionHeadersBin);

        $pat = new \PhpBg\DvbPsi\Tables\Pat();
        $pat->transportStreamId = $syntaxSectionHeadersArray['tsid'];
        $pat->current = ($syntaxSectionHeadersArray['headers1'] & 0b1) === 0b1;
        $pat->version = ($syntaxSectionHeadersArray['headers1'] >> 1) & 0b11111;

        while ($currentPointer < $crcOffset) {
            $patDataArray = unpack('n2', substr($data, $currentPointer, 4));
            $currentPointer += 4;
            $programNum = $patDataArray[1];
            $programPid = $patDataArray[2] & 0x1fff;
            $pat->programs[$programNum] = $programPid;
        }

        //TODO check CRC
        $crc = unpack('N', substr($data, $currentPointer, 4))[1];

        return $pat;
    }
}