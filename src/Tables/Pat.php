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
 * Class Pat (program association table)
 *
 * @see https://en.wikipedia.org/wiki/MPEG_transport_stream#PAT
 * @see https://en.wikipedia.org/wiki/Program-specific_information
 * @package Lovephp\Dvb\Psi
 */
class Pat
{
    public $transportStreamId;
    public $active;
    public $version;

    /**
     * @var array Array of program numbers => PMT PIDs
     */
    public $programs = [];

    public function __toString()
    {
        $buf = sprintf("TransportStreamId: %d (0x%x)\n", $this->transportStreamId, $this->transportStreamId);
        $buf .= sprintf("Active: %d\n", $this->active);
        $buf .= sprintf("Version: %d (0x%x)\n", $this->version, $this->version);
        $buf .= "Programs:\n";
        $buf .= "\tProgram number > PMT PID\n";
        foreach ($this->programs as $programNumber => $programPid) {
            if ($programNumber === 0) {
                $buf .= sprintf("\t%d (0x%x) (NIT) > %d (0x%x)\n", $programNumber, $programNumber, $programPid, $programPid);

            } else {
                $buf .= sprintf("\t%d (0x%x) > %d (0x%x)\n", $programNumber, $programNumber, $programPid, $programPid);
            }
        }
        return $buf;
    }
}