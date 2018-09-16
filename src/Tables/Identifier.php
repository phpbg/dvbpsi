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

use MyCLabs\Enum\Enum;

/**
 * Class TableId
 *
 * @see Final draft ETSI EN 300 468 V1.13.1 (2012-04), 5.1.3 Coding of PID and table_id fields
 * @see https://en.wikipedia.org/wiki/Program-specific_information#Table_Identifiers
 */
class Identifier extends Enum
{
    const PROGRAM_ASSOCIATION_SECTION = 0x00;

    const EVENT_INFORMATION_SECTION_ACTUAL_TS_PRESENT_FOLLOWING = 0x4E;

    const EVENT_INFORMATION_SECTION_OTHER_TS_PRESENT_FOLLOWING = 0x4F;

    const EVENT_INFORMATION_SECTION_ACTUAL_TS_PRESENT_SCHEDULE = [0x50, 0x51, 0x52, 0x53, 0x54, 0x55, 0x56, 0x57, 0x58, 0x59, 0x5a, 0x5b, 0x5c, 0x5d, 0x5e, 0x5f];

    const EVENT_INFORMATION_SECTION_OTHER_TS_PRESENT_SCHEDULE = [0x60, 0x61, 0x62, 0x63, 0x64, 0x65, 0x66, 0x67, 0x68, 0x69, 0x6a, 0x6b, 0x6c, 0x6d, 0x6e, 0x6f];

    const TIME_DATE_SECTION = 0x70;

    const TIME_OFFSET_SECTION = 0x73;
}