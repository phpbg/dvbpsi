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

namespace PhpBg\DvbPsi\Descriptors;

/**
 * Class Content
 * @see Final draft ETSI EN 300 468 V1.13.1 (2012-04), 6.2.9 Content descriptor
 */
class Content
{

    public $nibbles = [];

    public function __construct($data)
    {
        $len = strlen($data);
        $pointer = 0;
        while ($pointer < $len) {
            $nibbles = unpack('C', $data[$pointer])[1];
            $pointer += 1;
            $userByte = unpack('C', $data[$pointer])[1];
            $pointer += 1;
            $this->nibbles[] = [($nibbles >> 4) & 0xf, $nibbles & 0xf, $userByte];
        }
    }

    public function __toString()
    {
        $msg = "Content:\n";
        foreach ($this->nibbles as $nibble) {
            // TODO human decode levels
            $msg .= sprintf("Level1: 0x%x, Level2: 0x%x, User byte:0x%x\n", $nibble[0], $nibble[1], $nibble[2]);
        }
        return $msg;
    }
}