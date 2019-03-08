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

use PhpBg\DvbPsi\Exception;

/**
 * Class TerrestrialDeliverySystem, DVB-T (not DVB-T2)
 * @see Final draft ETSI EN 300 468 V1.15.1 (2016-03), 6.2.13.4 Terrestrial delivery system descriptor
 */
class TerrestrialDeliverySystem
{
    /**
     * Frequency, in Hz
     * @var int
     */
    public $centreFrequency;

    /**
     * Bandwidth, in Hz
     * @var int
     */
    public $bandwidth;

    /**
     * This 1-bit flag indicates the stream's hierarchical priority
     * @var int 0 => LP, 1 => HP
     */
    public $priority;

    /**
     * This 1-bit field indicates the use of the Time Slicing on the associated transport stream. If the
     * Time_Slicing_indicator is set ("1"), Time Slicing is not used. If the Time_Slicing_indicator is cleared ("0"), at least one
     * elementary stream uses Time Slicing.
     * @var int
     */
    public $timeSlicingIndicator;

    /**
     * This 1-bit field indicates the use of the MPE-FEC on the associated transport stream. If the
     * MPE-FEC_indicator is set ("1"), MPE-FEC is not used. If the MPE-FEC_indicator is cleared ("0"), at least one
     * elementary stream uses MPE-FEC.
     * @var int
     */
    public $mpeFecIndicator;

    /**
     * This is a 2-bit field. It specifies the constellation pattern used on a terrestrial delivery system
     * @see TerrestrialDeliverySystem::CONSTELLATION_MAPPING
     * @var int
     */
    public $constellation;

    /**
     * The hierarchy_information specifies whether the transmission is hierarchical and, if so, what
     * the α value is (see table 46). Also, the use of in-depth interleaver is indicated. When the transmission_mode indicates
     * the use of 8k mode, only the native interleaver shall be signalled.
     * @var int
     */
    public $hierarchyInformation;

    /**
     * The code_rate is a 3-bit field specifying the inner FEC scheme used according to table 47. Non-hierarchical
     * channel coding and modulation requires signalling of one code rate. In this case, 3 bits specifying code_rate according
     * to table 47 are followed by another 3 bits of value "000". Two different code rates may be applied to two different
     * levels of modulation with the aim of achieving hierarchy. Transmission then starts with the code rate for the HP level of
     * the modulation and ends with the one for the LP level.
     * @var int
     */
    public $codeRateHpStream;

    /**
     * @see TerrestrialDeliverySystem::codeRateHpStream
     * @var int
     */
    public $codeRateLpStream;

    /**
     * The guard_interval is a 2-bit field specifying the guard interval
     * @see TerrestrialDeliverySystem::GUARD_INTERVAL_MAPPING
     * @var int
     */
    public $guardInterval;

    /**
     * This 2-bit field indicates the number of carriers in an OFDM frame
     * @var int
     */
    public $transmissionMode;

    /**
     * This 1-bit flag indicates whether other frequencies are in use. The value "0" indicates that no
     * other frequency is in use, "1" indicates that one or more other frequencies are in use.
     * @var int
     */
    public $otherFrequencyFlag;

    const BANDWIDTH_MAPPING = [
        0b000 => 8000000,
        0b001 => 7000000,
        0b010 => 6000000,
        0b011 => 5000000,
    ];

    const CONSTELLATION_MAPPING = [
        0b00 => 'QPSK',
        0b01 => '16-QAM',
        0b10 => '64-QAM',
    ];

    const CODERATE_MAPPING = [
        0b000 => '1/2',
        0b001 => '2/3',
        0b010 => '3/4',
        0b011 => '5/6',
        0b100 => '7/8',
    ];

    const GUARD_INTERVAL_MAPPING = [
        0b00 => '1/32',
        0b01 => '1/16',
        0b10 => '1/8',
        0b11 => '1/4',
    ];

    /**
     * TerrestrialDeliverySystem constructor.
     * @param $data
     * @throws Exception
     */
    public function __construct($data)
    {
        if (strlen($data) !== 11) {
            throw new Exception("Unexpected TerrestrialDeliverySystem descriptor length");
        }
        $this->centreFrequency = 10 * unpack('N', substr($data, 0, 4))[1];

        $tmp = unpack('C', $data[4])[1];
        $binBandwidth = ($tmp >> 5) & 0b111;
        if (isset(static::BANDWIDTH_MAPPING[$binBandwidth])) {
            $this->bandwidth = static::BANDWIDTH_MAPPING[$binBandwidth];
        }
        $this->priority = ($tmp >> 4) & 0b1;
        $this->timeSlicingIndicator = ($tmp >> 3) & 0b1;
        $this->mpeFecIndicator = ($tmp >> 2) & 0b1;

        $tmp = unpack('C', $data[5])[1];
        $this->constellation = ($tmp >> 6) & 0b11;
        $this->hierarchyInformation = ($tmp >> 3) & 0b111;
        $this->codeRateHpStream = $tmp & 0b111;

        $tmp = unpack('C', $data[6])[1];
        $this->codeRateLpStream = ($tmp >> 5) & 0b111;
        $this->guardInterval = ($tmp >> 3) & 0b11;
        $this->transmissionMode = ($tmp >> 1) & 0b11;
        $this->otherFrequencyFlag = $tmp & 0b1;
    }

    public function __toString()
    {
        $msg = "DVB-T delivery system:\n";
        $msg .= "Frequency: {$this->centreFrequency}Hz\n";
        $msg .= "Bandwidth: {$this->bandwidth}Hz\n";

        $msg .= "Priority: {$this->priority}\n";
        $msg .= "Time slicing indicator: {$this->timeSlicingIndicator}\n";
        $msg .= "MPE-FEC indicator: {$this->mpeFecIndicator}\n";

        $constellation = static::CONSTELLATION_MAPPING[$this->constellation] ?? '?';
        $msg .= "Constellation: {$constellation}\n";

        $msg .= sprintf("Hierarchy: 0b%03b\n", $this->hierarchyInformation);

        $coderateHp = static::CODERATE_MAPPING[$this->codeRateHpStream] ?? '?';
        $coderateLp = static::CODERATE_MAPPING[$this->codeRateLpStream] ?? '?';
        $msg .= "Coderate HP - LP: {$coderateHp} - {$coderateLp}\n";

        $guard = static::GUARD_INTERVAL_MAPPING[$this->guardInterval] ?? '?';
        $msg .= "Guard interval: {$guard}\n";

        $msg .= sprintf("Transmission mode: 0b%02b\n", $this->transmissionMode);

        $msg .= "Other frequency: {$this->otherFrequencyFlag}\n";

        return $msg;
    }
}