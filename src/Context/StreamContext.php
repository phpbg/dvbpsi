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

namespace PhpBg\DvbPsi\Context;


use Evenement\EventEmitter;
use PhpBg\DvbPsi\Tables\Pat;
use PhpBg\DvbPsi\Tables\Pmt;
use PhpBg\DvbPsi\Tables\Sdt;

class StreamContext extends EventEmitter
{
    /**
     * @var Pat
     */
    public $pat;

    /**
     * @var int
     */
    public $tdtTimestamp;

    /**
     * Array of PMTs, by program number
     * @var Pmt[]
     */
    public $pmts;

    /**
     * @var Sdt[]
     */
    public $sdts;


    public function addPat(Pat $pat)
    {
        if (!isset($this->pat) || $this->pat->version < $pat->version || ($this->pat->version !== 0 && $pat->version === 0)) {
            $oldPat = $this->pat;
            $this->pat = $pat;
            $this->pmts = [];
            $this->emit('pat-update', [$this->pat, $oldPat]);
        }
    }

    public function addSdt(Sdt $sdt)
    {
        if (!isset($this->sdts[$sdt->transportStreamId])
            || $this->sdts[$sdt->transportStreamId]->versionNumber < $sdt->versionNumber
            || ($this->sdts[$sdt->transportStreamId]->versionNumber !== 0 && $sdt->versionNumber === 0)
        ) {
            $this->sdts[$sdt->transportStreamId] = $sdt;
            $this->emit('update');
        }
    }

    public function addPmt(Pmt $pmt)
    {
        if (!isset($this->pmts[$pmt->programNumber]) || $this->pmts[$pmt->programNumber]->version < $pmt->version || ($this->pmts[$pmt->programNumber]->version !== 0 && $pmt->version === 0)) {
            $this->pmts[$pmt->programNumber] = $pmt;
            $this->emit('pmt-update');

            $programsCount = count($this->pat->programs);
            if (isset($this->pat->programs[0])) {
                // Programm 0 is NIT, not a real program
                $programsCount--;
            }
            if (count($this->pmts) === $programsCount) {
                $this->emit('update');
            }
        }
    }

    public function setTdtTimestamp(int $tdtTimestamp)
    {
        $this->tdtTimestamp = $tdtTimestamp;
        $this->emit('time-update');
    }

    public function __toString()
    {
        $str = '';
        if (isset($this->pat)) {
            $str .= "PAT\n";
            $str .= "$this->pat\n";

            if (!empty($this->pmts)) {
                $str .= "PMTs\n";
                foreach ($this->pmts as $pmt) {
                    $str .= "$pmt\n";
                }
            }
        }

        if (isset($this->tdtTimestamp)) {
            $str .= "Last time: " . date('Y-m-d H:i:s', $this->tdtTimestamp) . "\n";
        }

        return $str;
    }
}