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
 * @see Final draft ETSI EN 300 468 V1.15.1 (2016-03), 6.2.33 Service descriptor
 */
class ServiceDescriptor
{

    /**
     * This is an 8-bit field specifying the type of the service. The assignment of service_type value for a
     * service is described in annex I.
     */
    public $serviceType;
    public $serviceProviderName;
    public $serviceName;

    /**
     * ServiceDescriptor constructor.
     * @param $data
     * @throws \PhpBg\DvbPsi\Exception
     */
    public function __construct($data)
    {
        $pointer = 0;
        $serviceType = unpack('C', $data[ $pointer])[1];
        $this->serviceType = new ServiceType($serviceType);

        $pointer += 1;
        $serviceProviderNameLength = unpack('C', $data[$pointer])[1];

        $pointer += 1;
        $this->serviceProviderName = substr($data, $pointer, $serviceProviderNameLength);

        $pointer += $serviceProviderNameLength;
        $serviceNameLength = unpack('C', $data[$pointer])[1];

        $pointer += 1;
        $this->serviceName = substr($data, $pointer, $serviceNameLength);
    }

    public function __toString()
    {
        $msg = sprintf("Service type: %s (0x%x)\n", $this->serviceType->getKey(), $this->serviceType->getValue() );
        $msg .= "Service provider name: $this->serviceProviderName\n";
        $msg .= "Service name: $this->serviceName\n";

        return $msg;
    }
}