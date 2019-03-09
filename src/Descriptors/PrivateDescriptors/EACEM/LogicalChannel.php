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

namespace PhpBg\DvbPsi\Descriptors\PrivateDescriptors\EACEM;

/**
 * Class LogicalChannel
 * @see EACEM IEC/CENELEC 62 216: « Baseline Digital Terrestrial TV Receiver Specification »
 */
class LogicalChannel
{
    /**
     * Array of <service id> => <logical channel number>
     * @var array
     */
    public $services = [];

    /**
     * NetworkName constructor.
     * @param $data
     * @throws \PhpBg\DvbPsi\Exception
     */
    public function __construct($data)
    {
        $len = strlen($data);
        $currentPointer = 0;
        while ($currentPointer < $len) {
            $serviceId = unpack('n', substr($data, $currentPointer, 2))[1];
            $currentPointer += 2;

            $tmp = unpack('n', substr($data, $currentPointer, 2))[1];
            $currentPointer += 2;
            $lcn = $tmp & 0b1111111111;

            $this->services[$serviceId] = $lcn;
        }
    }

    public function __toString()
    {
        $msg = "Logical channels numbers:\n";
        $msg .= "\tService ID > channel number\n";
        foreach ($this->services as $serviceId => $lcn) {
            $msg .= sprintf("\t%d (0x%x) > %d\n", $serviceId, $serviceId, $lcn);
        }
        return $msg;
    }
}