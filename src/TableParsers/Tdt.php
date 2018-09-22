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
use PhpBg\DvbPsi\Tables\Identifier;
use PhpBg\MpegTs\Pid;

class Tdt implements TableParserInterface
{

    use Timestamp;

    public function getPids(): array
    {
        return [Pid::TDT_TOT_ST];
    }

    public function getTableIds(): array
    {
        return [Identifier::TIME_DATE_SECTION];
    }

    public function getEventName(): string
    {
        return 'tdt';
    }

    public function parse(int $tableId, string $data, int $currentPointer, int $sectionLength)
    {
        if ($sectionLength !== 5) {
            throw new Exception("Invalid TDT section length: $sectionLength");
        }

        // header is 3 bytes, payload 40bits = 5bytes
        $mjdUtcBinaryString = substr($data, $currentPointer, 5);
        return $this->getTimestampFromMjdUtc($mjdUtcBinaryString);
    }
}