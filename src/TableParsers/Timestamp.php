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

trait Timestamp
{
    /**
     * Parse a 40-bit field that contains the current time and date in UTC and MJD (see annex C).
     * This field is coded as 16 bits giving the 16 LSBs of MJD followed by 24 bits coded as 6 digits in 4-bit BCD.
     * @param $mjdUtcBinaryString
     * @return int Unix timestamp
     */
    protected function getTimestampFromMjdUtc($mjdUtcBinaryString)
    {
        // Modified Julian Day https://en.wikipedia.org/wiki/Julian_day
        $mjdStr = substr($mjdUtcBinaryString, 0, 2);
        $mjd = unpack('n', $mjdStr)[1];

        // Translate mjd to jd
        $jd = $mjd + 2400000.5;

        // Translate to UNIX timestamp
        // Do not use jdtounix() because jdtounix() only accept integers, not floats
        // 2440587.5 is the julian day at 1/1/1970 0:00 UTC
        // 86400 is the number of seconds in a day
        $timestamp = ($jd - 2440587.5) * 86400;

        // Decode hh:ii:ss
        $hhStr = substr($mjdUtcBinaryString, 2, 3);
        $timestamp += $this->getTimestampFromUtcBcd($hhStr);
        return $timestamp;
    }

    /**
     * Parse a 24 bits field coded as 6 digits in 4-bit BCD hours, minutes, seconds
     * EXAMPLE: 01:45:30 is coded as "0x014530".
     * @param $utcBcdBinaryString
     * @return int Unix timestamp
     */
    protected function getTimestampFromUtcBcd($utcBcdBinaryString)
    {
        $hisArray = unpack('C3', $utcBcdBinaryString);
        $hh = $hisArray[1];
        $h = 10 * (($hh & 0xf0) >> 4) + ($hh & 0x0f);
        $ii = $hisArray[2];
        $i = 10 * (($ii & 0xf0) >> 4) + ($ii & 0x0f);
        $ss = $hisArray[3];
        $s = 10 * (($ss & 0xf0) >> 4) + ($ss & 0x0f);

        $timestamp = 3600 * $h + 60 * $i + $s;
        return $timestamp;
    }
}