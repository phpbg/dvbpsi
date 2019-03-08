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

use PhpBg\DvbPsi\TableParsers\Text;

/**
 * Class ShortEvent
 * @see Final draft ETSI EN 300 468 V1.13.1 (2012-04), 6.2.37 Short event descriptor
 */
class ShortEvent
{
    use Text;

    public $language;
    public $eventName;
    public $text;

    /**
     * ShortEvent constructor.
     * @param $data
     * @throws \PhpBg\DvbPsi\Exception
     */
    public function __construct($data)
    {
        $pointer = 0;
        $this->language = substr($data, 0, 3);
        $pointer += 3;
        $nameLen = unpack('C', substr($data, $pointer, 1))[1];
        $pointer += 1;
        if ($nameLen > 0) {
            $this->eventName = $this->toUtf8String(substr($data, $pointer, $nameLen));
        }
        $pointer += $nameLen;
        $textLen = unpack('C', substr($data, $pointer, 1))[1];
        $pointer += 1;
        if ($textLen > 0) {
            $this->text = $this->toUtf8String(substr($data, $pointer, $textLen));
        }
    }

    public function __toString()
    {
        $msg = "Short event:\n";
        if (!empty($this->language)) {
            $msg .= "({$this->language}) ";
        }
        if (!empty($this->eventName)) {
            $msg .= "{$this->eventName} : ";
        }
        if (!empty($this->text)) {
            $msg .= "{$this->text} ";
        }
        return $msg;
    }
}