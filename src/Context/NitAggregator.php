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

use PhpBg\DvbPsi\Exception;
use PhpBg\DvbPsi\Tables\Nit;

/**
 * Class NitAggregator
 * Aggregates all NIT segments
 */
class NitAggregator
{
    public $networkId;

    /**
     * @var Nit[]
     */
    public $segments = [];

    protected $version;

    protected $lastSectionNumber;

    /**
     * Aggregate a new EIT
     *
     * @param Nit $nit
     * @throws Exception
     * @return bool Return true if the EIT was unknown and has been aggregated, false otherwise
     */
    public function add(Nit $nit): bool
    {
        if (!isset($this->networkId)) {
            $this->networkId = $nit->networkId;
        } else {
            if ($this->networkId !== $nit->networkId) {
                throw new Exception("NIT and aggregator mismatch");
            }
        }
        if (!isset($this->version) || $nit->versionNumber > $this->version || ($nit->versionNumber === 0 && $this->version === 31)) {
            // Version update (or initial collection of nit)
            $this->version = $nit->versionNumber;
            $this->lastSectionNumber = $nit->lastSectionNumber;
            $this->segments = [];
        }
        if (!isset($this->segments[$nit->sectionNumber])) {
            $this->segments[$nit->sectionNumber] = $nit;
        }
        return $this->isComplete();
    }

    /**
     * Return true if all NIT segments have been aggregated
     * @return bool
     */
    public function isComplete(): bool
    {
        if (!isset($this->lastSectionNumber)) {
            return false;
        }
        if (count($this->segments) >= $this->lastSectionNumber + 1) {
            // NIT aggregation is complete
            return true;
        }
        return false;
    }

    public function __toString()
    {
        $str = '';
        foreach ($this->segments as $segment) {
            echo "{$segment}\n";
        }
        return $str;
    }
}