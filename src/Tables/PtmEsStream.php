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

use PhpBg\DvbPsi\Descriptors\Values\EsType;
use PhpBg\DvbPsi\Descriptors\Values\ProgramEsDescriptorTag;

class PtmEsStream
{
    /**
     * @var int
     */
    public $streamType;

    /**
     * @var int
     */
    public $descriptorTag;

    public $descriptors = [];

    public function __toString()
    {
        $str = sprintf("%d %s\n", $this->streamType, EsType::desc($this->streamType));
        $str .= sprintf("%d %s\n", $this->descriptorTag, ProgramEsDescriptorTag::desc($this->descriptorTag));
        foreach ($this->descriptors as $descriptor) {
            $str .= "{$descriptor}\n";
        }
        return $str;
    }
}