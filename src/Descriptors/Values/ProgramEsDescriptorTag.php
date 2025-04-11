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
 * Class EsType
 * @see https://en.wikipedia.org/wiki/Program-specific_information#Elementary_stream_types
 */
class ProgramEsDescriptorTag
{
    const TAG = [
        2 =>'Video stream header parameters for ITU-T Rec. H.262, ISO/IEC 13818-2 and ISO/IEC 11172-2',
        3 =>'Audio stream header parameters for ISO/IEC 13818-3 and ISO/IEC 11172-3',
        4 =>'Hierarchy for stream selection',
        5 =>'Registration of private formats',
        6 =>'Data stream alignment for packetized video and audio sync point',
        7 =>'Target background grid defines total display area size',
        8 =>'Video Window defines position in display area',
        9 =>'Conditional access system and EMM/ECM PID',
        10 =>'ISO 639 language and audio type',
        11 =>'System clock external reference',
        12 =>'Multiplex buffer utilization bounds',
        13 =>'Copyright identification system and reference',
        14 =>'Maximum bit rate',
        15 =>'Private data indicator',
        16 =>'Smoothing buffer',
        17 =>'STD video buffer leak control',
        18 =>'IBP video I-frame indicator',
        19 =>'ISO/IEC13818-6 DSM CC carousel identifier',
        20 =>'ISO/IEC13818-6 DSM CC association tag',
        21 =>'ISO/IEC13818-6 DSM CC deferred association tag',
        22 =>'ISO/IEC13818-6 DSM CC Reserved.',
        23 =>'DSM CC NPT reference',
        24 =>'DSM CC NPT endpoint',
        25 =>'DSM CC stream mode',
        26 =>'DSM CC stream event',
        27 =>'Video stream header parameters for ISO/IEC 14496-2 (MPEG-4 H.263 based)',
        28 =>'Audio stream header parameters for ISO/IEC 14496-3 (MPEG-4 LOAS multi-format framed)',
        29 =>'IOD parameters for ISO/IEC 14496-1',
        30 =>'SL parameters for ISO/IEC 14496-1',
        31 =>'FMC parameters for ISO/IEC 14496-1',
        32 =>'External ES identifier for ISO/IEC 14496-1',
        33 =>'MuxCode for ISO/IEC 14496-1',
        34 =>'FMX Buffer Size for ISO/IEC 14496-1',
        35 =>'Multiplex Buffer for ISO/IEC 14496-1',
        36 =>'Content labeling for ISO/IEC 14496-1',
        37 =>'Metadata pointer',
        38 =>'Metadata',
        39 =>'Metadata STD',
        40 =>'Video stream header parameters for ITU-T Rec. H.264 and ISO/IEC 14496-10',
        41 =>'ISO/IEC 13818-11 IPMP (DRM)',
        42 =>'Timing and HRD for ITU-T Rec. H.264 and ISO/IEC 14496-10',
        43 =>'Audio stream header parameters for ISO/IEC 13818-7 ADTS AAC',
        44 =>'FlexMux Timing for ISO/IEC 14496-1',
        45 =>'Text stream header parameters for ISO/IEC 14496',
        46 =>'Audio extension stream header parameters for ISO/IEC 14496-3 (MPEG-4 LOAS multi-format framed)',
        47 =>'Video auxiliary stream header parameters',
        48 =>'Video scalable stream header parameters',
        49 =>'Video multi stream header parameters',
        50 =>'Video stream header parameters for ITU-T Rec. T.800 and ISO/IEC 15444 (JPEG 2000)',
        51 =>'Video multi operation point stream header parameters',
        52 =>'Video stereoscopic (3D) stream header parameters for ITU-T Rec. H.262, ISO/IEC 13818-2 and ISO/IEC 11172-2',
        53 =>'Program stereoscopic (3D) information',
        54 =>'Video stereoscopic (3D) information',
        160 =>'VideoLAN FourCC, video size and codec initialization data',
    ];

    public static function desc ($tag) {
        return self::TAG[$tag]??'Unknown';
    }
}