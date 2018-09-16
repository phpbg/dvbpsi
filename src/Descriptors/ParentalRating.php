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

class ParentalRating
{
    public $ratings = [];

    public function __construct($data)
    {
        $pointer = 0;
        $len = strlen($data);
        while ($pointer < $len) {
            $country = substr($data, $pointer, 3);
            $pointer += 3;
            $rating = unpack('C', substr($data, $pointer, 1))[1];
            $pointer += 1;
            if ($rating !== 0 && $rating < 0x10) {
                $this->ratings[$country] = $rating + 3;
            } else {
                $this->ratings[$country] = $rating;
            }
        }
    }

    public function __toString()
    {
        $msg = "Parental ratings:\n";
        foreach ($this->ratings as $country => $rating) {
            $msg .= "\t{$country} : {$rating}+\n";
        }
        return $msg;
    }
}