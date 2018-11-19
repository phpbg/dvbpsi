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
 * Class ExtendedEvent
 * @see Final draft ETSI EN 300 468 V1.13.1 (2012-04), 6.2.15 Extended event descriptor
 */
class ExtendedEvent
{
    use Text;

    public $descriptorNumber;
    public $lastNumber;
    public $language;
    public $items = [];
    public $text;

    public function __construct($data)
    {
        $pointer = 0;
        $tmp = unpack('C', substr($data, $pointer, 1))[1];
        $pointer += 1;
        $this->descriptorNumber = ($tmp >> 4) & 0xf;
        $this->lastNumber = $tmp & 0xf;
        $this->language = substr($data, $pointer, 3);
        $pointer += 3;
        $itemsLen = unpack('C', substr($data, $pointer, 1))[1];
        $pointer += 1;
        $end = $pointer + $itemsLen;
        while ($pointer < $end) {
            $itemDescLen = unpack('C', substr($data, $pointer, 1))[1];
            $pointer += 1;
            $itemDesc = $this->toUtf8String(substr($data, $pointer, $itemDescLen));
            $pointer += $itemDescLen;
            $itemLen = unpack('C', substr($data, $pointer, 1))[1];
            $pointer += 1;
            $this->items[$itemDesc] = $this->toUtf8String(substr($data, $pointer, $itemLen));
            $pointer += $itemLen;
        }
        $textLen = unpack('C', substr($data, $pointer, 1))[1];
        $pointer += 1;
        if ($textLen > 0) {
            $this->text = $this->toUtf8String(substr($data, $pointer, $textLen));
        }
    }

    public function __toString()
    {
        $msg = "Extended event:\n";
        $msg .= sprintf("%d/%d ", $this->descriptorNumber, $this->lastNumber);
        if (!empty($this->language)) {
            $msg .= "({$this->language})";
        }
        $msg .= "\n";
        foreach ($this->items as $name => $value) {
            $msg .= "\t{$name} : {$value}\n";
        }
        if (!empty($this->text)) {
            $msg .= "{$this->text} ";
        }
        return $msg;
    }
}