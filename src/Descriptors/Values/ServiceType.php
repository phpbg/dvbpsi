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

namespace PhpBg\DvbPsi\Descriptors\Values;

use MyCLabs\Enum\Enum;
use PhpBg\DvbPsi\Exception;

/**
 * Class ServiceType
 * @see Final draft ETSI EN 300 468 V1.15.1 (2016-03), Table 87: Service type coding
 */
class ServiceType extends Enum
{
    const DIGITAL_TELEVISION_SERVICE = 0x01;
    const DIGITAL_RADIO_SOUND_SERVICE = 0x02;
    const TELETEXT_SERVICE = 0x03;
    const NVOD_REFERENCE_SERVICE = 0x04;
    const NVOD_TIME_SHIFTED_SERVICE = 0x05;
    const MOSAIC_SERVICE = 0x06;
    const FM_RADIO_SERVICE = 0x07;
    const DVB_SRM_SERVICE = 0x08;
    const ADVANCED_CODEC_DIGITAL_RADIO_SOUND_SERVICE = 0x0a;
    const H264_MOSAIC_SERVICE = 0x0b;
    const DATA_BROADCAST_SERVICE = 0x0c;
    const RESERVED_FOR_COMMON_INTERFACE_USAGE = 0x0d;
    const RCS_MAP = 0x0e;
    const RCS_FLS = 0x0f;
    const DVB_MPH = 0x10;
    const MPEG2_HD_DIGITAL_TELEVISION_SERVICE = 0x11;
    const H264_SD_DIGITAL_TELEVISION_SERVICE = 0x16;
    const H264_SD_NVOD_TIME_SHIFTED_SERVICE = 0x17;
    const H264_SD_NVOD_REFERENCE_SERVICE = 0x18;
    const H264_HD_DIGITAL_TELEVISION_SERVICE = 0x19;
    const H264_HD_NVOD_TIME_SHIFTED_SERVICE = 0x1A;
    const H264_HD_NVOD_REFERENCE_SERVICE = 0x1B;
    const H264_FCPS_DIGITAL_TELEVISION_SERVICE = 0x1C;
    const H264_FCPS_NVOD_TIME_SHIFTED_SERVICE = 0x1D;
    const H264_FCPS_NVOD_REFERENCE_SERVICE = 0x1E;
    const HEVC_DIGITAL_TELEVISION_SERVICE = 0x1E;

    /**
     * Allow service type with any value
     * @param int $value
     * @throws Exception
     */
    public function __construct(int $value)
    {
        if ($value < 0 || $value > 0xff) {
            throw new Exception("Invalid service type value: $value");
        }
        $this->value = $value;
    }
}