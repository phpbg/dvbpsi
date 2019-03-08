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

/**
 * Class PrivateDataSpecifier
 * @see Final draft ETSI EN 300 468 V1.15.1 (2016-03), 6.2.31 Private data specifier descriptor
 */
class PrivateDataSpecifier
{
    /**
     * Value of the private data specifier
     * Name of the organisation which is responsible for delivering private data in SI streams, e.g “ACME, Inc.”
     * @see https://www.dvbservices.com/identifiers/private_data_spec_id
     * @var int
     */
    public $private_data_specifier;

    public function __construct($data)
    {
        $this->private_data_specifier = unpack('N', $data)[1];
    }

    public function __toString()
    {
        return sprintf("PrivateDataSpecifier: 0x%x\n", $this->private_data_specifier);
    }
}