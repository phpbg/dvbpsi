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

namespace PhpBg\DvbPsi\Tests\TableParsers;

use PhpBg\DvbPsi\TableParsers\Timestamp;
use PHPUnit\Framework\TestCase;

class TimestampTest extends TestCase
{
    public function testGetTimestampFromMjd()
    {
        $timestampTrait = $this->getObjectForTrait(Timestamp::class);
        /**
         * @var Timestamp $timestampTrait
         */
        $object = new \ReflectionObject($timestampTrait);
        $method = $object->getMethod('getTimestampFromMjdUtc');
        $method->setAccessible(true);

        // This is the example given in the DVB PSI spec
        // 93/10/13 12:45:00 is coded as "0xC079124500".
        $mjdUtcBinaryString = pack('n', 0xC079) . pack('C3', 0x12, 0x45, 0x00);
        $result = $method->invoke($timestampTrait, $mjdUtcBinaryString);

        date_default_timezone_set('UTC');
        $this->assertSame("1993-10-13 12:45:00", date('Y-m-d H:i:s', $result));
    }

}