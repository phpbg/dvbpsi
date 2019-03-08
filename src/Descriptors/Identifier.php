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

use MyCLabs\Enum\Enum;

/**
 * Class Descriptor
 * @see Final draft ETSI EN 300 468 V1.13.1 (2012-04), 6 Descriptors
 */
class Identifier extends Enum
{
    const NETWORK_NAME_DESCRIPTOR = 0x40;
    const SERVICE_LIST_DESCRIPTOR = 0x41;
    const STUFFING_DESCRIPTOR = 0x42;
    const LINKAGE_DESCRIPTOR = 0x4a;
    const SHORT_EVENT_DESCRIPTOR = 0x4d;
    const EXTENDED_EVENT_DESCRIPTOR = 0x4e;
    const TIME_SHIFTED_EVENT_DESCRIPTOR = 0x4f;
    const COMPONENT_DESCRIPTOR = 0x50;
    const TERRESTRIAL_DELIVERY_SYSTEM_DESCRIPTOR = 0x5a;
    const CA_IDENTIFIER_DESCRIPTOR = 0x53;
    const CONTENT_DESCRIPTOR = 0x54;
    const PARENTAL_RATING_DESCRIPTOR = 0x55;
    const TELEPHONE_DESCRIPTOR = 0x57;
    const MULTILINGUAL_COMPONENT_DESCRIPTOR = 0x5e;
    const PRIVATE_DATA_SPECIFIER_DESCRIPTOR = 0x5f;
    const SHORT_SMOOTHING_BUFFER_DESCRIPTOR = 0x61;
    const DATA_BROADCAST_DESCRIPTOR = 0x64;
    const PDC_DESCRIPTOR = 0x69;
    const TVA_ID_DESCRIPTOR = 0x75;
    const CONTENT_IDENTIFIER_DESCRIPTOR = 0x76;
    const XAIT_LOCATION_DESCRIPTOR = 0x7d;
    const FTA_CONTENT_MANAGEMENT_DESCRIPTOR = 0x7e;
    const EXTENSION_DESCRIPTOR = 0x7f;
}