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

interface TableParserInterface
{
    /**
     * Return the PIDs that will match this table parser
     * @return array
     */
    public function getPids(): array;

    /**
     * Return the table IDs that will match this table parser
     * @return array Array of int
     */
    public function getTableIds(): array;

    /**
     * Return the name of the event that should be used when data will be parsed
     * @return string
     */
    public function getEventName(): string;

    /**
     * Parse a packer
     * @param int $tableId
     * @param string $data Complete PES packet
     * @param int $currentPointer Current pointer position (parsing should start from this pointer)
     * @param int $sectionLength Parsing should stop after parsing that length
     * @return mixed
     * @throws Exception
     */
    public function parse(int $tableId, string $data, int $currentPointer, int $sectionLength);
}