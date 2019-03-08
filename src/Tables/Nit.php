<?php

/**
 * MIT License
 *
 * Copyright (c) 2019 Samuel CHEMLA
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
 * Class Nit (Network information table)
 *
 * @see Final draft ETSI EN 300 468 V1.15.1 (2016-03), 5.2.1 Network Information Table (NIT)
 */
class Nit
{
    /**
     * This is a 16-bit field which serves as a label to identify the delivery system, about which the NIT informs, from any other delivery system.
     * @var int
     */
    public $networkId;

    /**
     * This 5-bit field is the version number of the sub_table. The version_number shall be incremented by
     * 1 when a change in the information carried within the sub_table occurs. When it reaches value 31, it wraps around to 0.
     * When the current_next_indicator is set to "1", then the version_number shall be that of the currently applicable
     * sub_table defined by the table_id and network_id. When the current_next_indicator is set to "0", then the
     * version_number shall be that of the next applicable sub_table defined by the table_id and network_id.
     * @var int
     */
    public $versionNumber;

    /**
     * This 1-bit indicator, when set to "1" indicates that the sub_table is the currently applicable
     * sub_table. When the bit is set to "0", it indicates that the sub_table sent is not yet applicable and shall be the next
     * sub_table to be valid.
     * @var boolean
     */
    public $currentNextIndicator;

    /**
     * This 8-bit field gives the number of the section. The section_number of the first section in the
     * sub_table shall be "0x00". The section_number shall be incremented by 1 with each additional section with the same
     * table_id and network_id.
     * @var int
     */
    public $sectionNumber;

    /**
     * This 8-bit field specifies the number of the last section (that is, the section with the highest
     * section_number) of the sub_table of which this section is part.
     * @var int
     */
    public $lastSectionNumber;

    /**
     * List of NIT descriptors
     * @var array
     */
    public $descriptors = [];

    /**
     * List of transport streams
     * @var NitTs[]
     */
    public $transportStreams = [];

    public function __toString()
    {
        $buf = sprintf("NetworkId: %d (0x%x)\n", $this->networkId, $this->networkId);
        $buf .= sprintf("Version: %d (0x%x)\n", $this->versionNumber, $this->versionNumber);
        $buf .= sprintf("Current next indicator: %d\n", $this->currentNextIndicator);
        $buf .= sprintf("Section: %d/%d\n", $this->sectionNumber, $this->lastSectionNumber);

        foreach ($this->descriptors as $descriptor) {
            $buf .= "{$descriptor}\n";
        }

        foreach ($this->transportStreams as $transportStream) {
            $buf .= "TS:\n{$transportStream}\n";
        }

        return $buf;
    }
}