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
class EsType
{
    const ES_TYPES = [
        1 =>'ISO/IEC 11172-2 (MPEG-1 video)in a packetized stream',
        2 =>'ITU-T Rec. H.262 and ISO/IEC 13818-2 (MPEG-2 higher rate interlaced video) in a packetized stream',
        3 =>'ISO/IEC 11172-3 (MPEG-1 audio) in a packetized stream',
        4 =>'ISO/IEC 13818-3 (MPEG-2 halved sample rate audio) in a packetized stream',
        5 =>'ITU-T Rec. H.222 and ISO/IEC 13818-1 (MPEG-2 tabled data) privately defined',
        6 =>'ITU-T Rec. H.222 and ISO/IEC 13818-1 (MPEG-2 packetized data) privately defined (i.e., DVB subtitles/VBI and AC-3)',
        7 =>'ISO/IEC 13522 (MHEG) in a packetized stream',
        8 =>'ITU-T Rec. H.222 and ISO/IEC 13818-1 DSM CC in a packetized stream',
        9 =>'ITU-T Rec. H.222 and ISO/IEC 13818-1/11172-1 auxiliary data in a packetized stream',
        10 =>'ISO/IEC 13818-6 DSM CC multiprotocol encapsulation ',
        11 =>'ISO/IEC 13818-6 DSM CC U-N messages',
        12 =>'ISO/IEC 13818-6 DSM CC stream descriptors',
        13 =>'ISO/IEC 13818-6 DSM CC tabled data',
        14 =>'ISO/IEC 13818-1 auxiliary data in a packetized stream',
        15 =>'ISO/IEC 13818-7 ADTS AAC (MPEG-2 lower bit-rate audio) in a packetized stream',
        16 =>'ISO/IEC 14496-2 (MPEG-4 H.263 based video) in a packetized stream',
        17 =>'ISO/IEC 14496-3 (MPEG-4 LOAS multi-format framed audio) in a packetized stream',
        18 =>'ISO/IEC 14496-1 (MPEG-4 FlexMux) in a packetized stream',
        19 =>'ISO/IEC 14496-1 (MPEG-4 FlexMux) in ISO/IEC 14496 tables',
        20 =>'ISO/IEC 13818-6 DSM CC synchronized download protocol',
        21 =>'Packetized metadata',
        22 =>'Sectioned metadata',
        23 =>'ISO/IEC 13818-6 DSM CC Data Carousel metadata',
        24 =>'ISO/IEC 13818-6 DSM CC Object Carousel metadata',
        25 =>'ISO/IEC 13818-6 Synchronized Download Protocol metadata',
        26 =>'ISO/IEC 13818-11 IPMP',
        27 =>'ITU-T Rec. H.264 and ISO/IEC 14496-10 (lower bit-rate video) in a packetized stream',
        28 =>'ISO/IEC 14496-3 (MPEG-4 raw audio) in a packetized stream',
        29 =>'ISO/IEC 14496-17 (MPEG-4 text) in a packetized stream',
        30 =>'ISO/IEC 23002-3 (MPEG-4 auxiliary video) in a packetized stream',
        31 =>'ISO/IEC 14496-10 SVC (MPEG-4 AVC sub-bitstream) in a packetized stream',
        32 =>'ISO/IEC 14496-10 MVC (MPEG-4 AVC sub-bitstream) in a packetized stream',
        33 =>'ITU-T Rec. T.800 and ISO/IEC 15444 (JPEG 2000 video) in a packetized stream',
        36 =>'ITU-T Rec. H.265 and ISO/IEC 23008-2 (Ultra HD video) in a packetized stream',
        66 =>'Chinese Video Standard in a packetized stream',
        127 =>'ISO/IEC 13818-11 IPMP (DRM) in a packetized stream',
        128 =>'ITU-T Rec. H.262 and ISO/IEC 13818-2 with DES-64-CBC encryption for DigiCipher II or PCM audio for Blu-ray in a packetized stream',
        129 =>'Dolby Digital (AC-3) up to six channel audio for ATSC and Blu-ray in a packetized stream',
        130 =>'SCTE subtitle or DTS 6 channel audio for Blu-ray in a packetized stream',
        131 =>'Dolby TrueHD lossless audio for Blu-ray in a packetized stream',
        132 =>'Dolby Digital Plus (enhanced AC-3) up to 16 channel audio for Blu-ray in a packetized stream',
        133 =>'DTS 8 channel audio for Blu-ray in a packetized stream',
        134 =>'SCTE-35[5] digital program insertion cue message or DTS 8 channel lossless audio for Blu-ray in a packetized stream',
        135 =>'Dolby Digital Plus (enhanced AC-3) up to 16 channel audio for ATSC in a packetized stream',
        144 =>'Blu-ray Presentation Graphic Stream (subtitling) in a packetized stream',
        145 =>'ATSC DSM CC Network Resources table',
        192 =>'DigiCipher II text in a packetized stream',
        193 =>'Dolby Digital (AC-3) up to six channel audio with AES-128-CBC data encryption in a packetized stream',
        194 =>'ATSC DSM CC synchronous data or Dolby Digital Plus up to 16 channel audio with AES-128-CBC data encryption in a packetized stream',
        207 =>'ISO/IEC 13818-7 ADTS AAC with AES-128-CBC frame encryption in a packetized stream',
        209 =>'BBC Dirac (Ultra HD video) in a packetized stream',
        210 =>'Audio Video Standard AVS2 (Ultra HD video) in a packetized stream',
        211 =>'Audio Video Standard AVS3 Audio in a packetized stream',
        212 =>'Audio Video Standard AVS3 Video  (Ultra HD video) in a packetized stream',
        219 =>'ITU-T Rec. H.264 and ISO/IEC 14496-10 with AES-128-CBC slice encryption in a packetized stream',
        234 =>'Microsoft Windows Media Video 9 (lower bit-rate video) in a packetized stream',
    ];

    public static function desc ($type) {
        return self::ES_TYPES[$type]??'Unknown';
    }
}