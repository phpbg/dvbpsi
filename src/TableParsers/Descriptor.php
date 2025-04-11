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

use PhpBg\DvbPsi\Tables\PtmEsDescriptor;

trait Descriptor
{
    /**
     * @param $tag
     * @param $data
     * @return PtmEsDescriptor[]
     */
    protected function parseDescriptors($data) {
        $result = [];
        $pointer = 0;
        $dataLen = strlen($data);

        while ($pointer < $dataLen) {
            $esDescriptor = new PtmEsDescriptor();
            $esDescriptor->descriptorTag = unpack('C', substr($data, $pointer, 1))[1];
            $pointer += 1;

            $descriptorLen = unpack('C', substr($data, $pointer, 1))[1];
            $pointer += 1;

            $tmp = substr($data, $pointer, $descriptorLen);
            $pointer += $descriptorLen;
            switch ($esDescriptor->descriptorTag) {
                // 0x0a ISO 639 language and audio type
                case 10:
                    $esDescriptor->properties = $this->languageDescriptor($tmp);
                    break;
                // 0x0E Maximum bit rate
                case 14:
                    $esDescriptor->properties = $this->bitrateDescriptor($tmp);
                    break;
            }
            $result[] = $esDescriptor;
        }
        return $result;
    }

    protected function languageDescriptor($data) {
        $tmp = unpack('N', $data)[1];
        $result = [
            'language_code' => hex2bin(dechex(($tmp >> 8) & 0xffffff)),
            'audio_type' => $tmp & 0xff
        ];
        return $result;
    }

    protected function bitrateDescriptor($data) {
        $tmp = unpack('C1a/n1b', $data);
        $result = [
            'maximum_bitrate' => ($tmp['a'] << 16 | $tmp['b']) & 0x3fffff,
        ];
        return $result;
    }

}