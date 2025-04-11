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

/**
 * Class Pmt (program map table)
 *
 * @see https://en.wikipedia.org/wiki/MPEG_transport_stream#PMT
 * @see https://en.wikipedia.org/wiki/Program-specific_information
 */
class Pmt
{
    /**
     * Program number
     * @var int
     */
    public $programNumber;

    /**
     * current/next indicator. Indicates if data is current in effect or is for future use. If the bit is flagged on, then the data is to be used at the present moment.
     * @var boolean
     */
    public $current;

    /**
     * Syntax version number. Incremented when data is changed and wrapped around on overflow for values greater than 32.
     * @var int
     */
    public $version;

    /**
     * The PID that will hold the program clock reference (may be null if not used)
     * @var int|null
     */
    public $pcrPid;

    /**
     * TODO find what are those program descriptors...
     * @var array
     */
    public $programDescriptors = [];

    /**
     * Elementary streams
     * @var array Array of PID => elementary stream descriptor
     */
    public $streams = [];

    public function __toString()
    {
        $buf = sprintf("Program number: %d (0x%x)\n", $this->programNumber, $this->programNumber);
        $buf .= sprintf("Current: %d\n", $this->current);
        $buf .= sprintf("Version: %d (0x%x)\n", $this->version, $this->version);
        if ($this->pcrPid === null){
            $buf .= sprintf("No PCR for this program\n");
        } else {
            $buf .= sprintf("PCR PID: %d (0x%x)\n", $this->pcrPid, $this->pcrPid);
        }

        $buf .= "Streams:\n";
        $buf .= "\tPID > Elementary stream descriptor\n";
        foreach ($this->streams as $pid => $esDescriptor) {
            $buf .= sprintf("\t%d (0x%x) > %s\n", $pid, $pid, $esDescriptor);
        }

        return $buf;
    }
}