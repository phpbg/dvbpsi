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

use PhpBg\DvbPsi\Descriptors\Values\ServiceType;

/**
 * Class ServiceList
 * @see Final draft ETSI EN 300 468 V1.15.1 (2016-03), 6.2.35 Service list descriptor
 */
class ServiceList
{
    /**
     * @var array <service id> => <service type>
     */
    public $services;

    /**
     * ServiceList constructor.
     * @param $data
     * @throws \PhpBg\DvbPsi\Exception
     */
    public function __construct($data)
    {
        $pointer = 0;
        $len = strlen($data);
        while ($pointer < $len) {
            $serviceId = unpack('n', substr($data, $pointer, 2))[1];
            $pointer += 2;
            $serviceType = unpack('C', $data[$pointer])[1];
            $pointer += 1;
            $this->services[$serviceId] = new ServiceType($serviceType);
        }
    }

    public function __toString()
    {
        $msg = "Services:\n";
        foreach ($this->services as $serviceId => $serviceType) {
            $msg .= sprintf("Service ID: %d (0x%x) => %s\n", $serviceId, $serviceId, $serviceType->getKey());
        }
        return $msg;
    }
}