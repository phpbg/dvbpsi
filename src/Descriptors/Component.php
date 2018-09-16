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
 * Class Component
 * @see Final draft ETSI EN 300 468 V1.13.1 (2012-04), 6.2.8 Component descriptor
 */
class Component
{

    public $streamContent;
    public $componentType;
    public $componentTag;
    public $language;
    public $text;

    public function __construct($data)
    {
        $tmp = unpack('C', $data[0])[1];
        $this->streamContent = $tmp & 0xf;
        $this->componentType = unpack('C', $data[1])[1];
        $this->componentTag = unpack('C', $data[2])[1];
        $this->language = substr($data, 3, 3);
        $this->text = substr($data, 6);
    }

    public function __toString()
    {
        $msg = "Component:\n";
        // TODO human decode components
        $msg .= sprintf("Stream content: 0x%x, Component type: 0x%x, Component tag: 0x%x\n", $this->streamContent, $this->componentType, $this->componentTag);
        $msg .= sprintf("(%s) %s\n", $this->language, $this->text);
        return $msg;
    }
}