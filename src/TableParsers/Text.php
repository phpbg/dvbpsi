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

trait Text
{
    /**
     * Convert text encoded as specified in Annex A to UTF-8 text
     * @see https://www.etsi.org/deliver/etsi_en/300400_300499/300468/01.15.01_60/en_300468v011501p.pdf
     *
     * @param $string
     * @return string
     * @throws Exception
     */
    protected function toUtf8String($string): string
    {
        $stringPointer = 0;
        $firstByte = unpack('C', substr($string, $stringPointer, 1))[1];
        $stringPointer++;
        $encoding = null;
        if ($encoding === null && $firstByte >= 0x20) {
            // if the first byte of the text field has a value in the range "0x20" to "0xFF" then this and all subsequent bytes in the
            // text item are coded using the default character coding table (table 00 - Latin alphabet) of figure A.1;
            // https://en.wikipedia.org/wiki/ISO/IEC_6937
            // iconv should work : http://php.net/manual/en/function.iconv.php
            // Else test this: https://github.com/mtojo/camellia
            $encoding = 'ISO6937';

            // In that case the first byte was a visible character,so don't skip it
            $stringPointer--;
        }
        if ($encoding === null && $firstByte >= 0x01 && $firstByte <= 0x0f) {
            // if the first byte of the text field has a value in the range "0x01" to "0x0F" then the remaining bytes in the text
            // item are coded in accordance with the character coding tables which are given in table A.3;
            $tables = [
                0x01 => 'ISO_8859-5',
                0x02 => 'ISO_8859-6',
                0x03 => 'ISO_8859-7',
                0x04 => 'ISO_8859-8',
                0x05 => 'ISO_8859-9',
                0x06 => 'ISO_8859-10',
                0x07 => 'ISO_8859-11',
                0x08 => 'ISO_8859-12',
                0x09 => 'ISO_8859-13',
                0x0a => 'ISO_8859-14',
                0x0b => 'ISO_8859-15',
            ];
            if (!isset($tables[$firstByte])) {
                throw new Exception();
            }
            $encoding = $tables[$firstByte];
        }
        if ($encoding === null && $firstByte == 0x10) {
            // if the first byte of the text field has a value "0x10" then the following two bytes carry a 16-bit value (uimsbf) N to
            // indicate that the remaining data of the text field is coded using the character code table specified by
            // ISO Standard 8859 [5], Parts 1 to 9.
            $nextBytes = unpack('n', substr($string, $stringPointer, 2))[1];
            $stringPointer += 2;
            $tables = [
                0x01 => 'ISO_8859-1',
                0x02 => 'ISO_8859-2',
                0x03 => 'ISO_8859-3',
                0x04 => 'ISO_8859-4',
                0x05 => 'ISO_8859-5',
                0x06 => 'ISO_8859-6',
                0x07 => 'ISO_8859-7',
                0x08 => 'ISO_8859-8',
                0x09 => 'ISO_8859-9',
                0x0a => 'ISO_8859-10',
                0x0b => 'ISO_8859-11',
                0x0d => 'ISO_8859-13',
                0x0e => 'ISO_8859-14',
                0x0f => 'ISO_8859-15',
            ];
            if (!isset($tables[$nextBytes])) {
                throw new Exception();
            }
            $encoding = $tables[$nextBytes];
        }

        if ($encoding === null && $firstByte >= 0x11 && $firstByte <= 15) {
            $tables = [
                0x11 => 'ISO-10646-UCS-2',
                0x12 => 'KSC_5601',
                0x13 => 'HZ-GB-2312',
                0x14 => 'BIG5',
                0x15 => 'UTF-8',
            ];
            if (!isset($tables[$firstByte])) {
                throw new Exception();
            }
            $encoding = $tables[$firstByte];
        }

        if ($encoding === null && $firstByte === 0x1f) {
            //If the first byte of the text field has value "0x1F" then the following byte carries an 8-bit value (uimsbf) containing the
            //encoding_type_id. This value indicates the encoding scheme of
            //the string. Allocations of the value of this field are
            //found in ETSI TS 101 162 [i.1].
            throw new Exception("Not supported");
        }

        if ($encoding === null) {
            throw new Exception("Encoding invalid");
        }

        return @iconv($encoding, 'UTF-8', substr($string, $stringPointer))?:$encoding;
    }
}