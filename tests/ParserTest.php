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

namespace PhpBg\DvbPsi\Tests;

use PhpBg\DvbPsi\ParserFactory;
use PhpBg\DvbPsi\Tables\Eit;
use PhpBg\DvbPsi\Tables\Pat;

class ParserTest extends TestCase
{

    public function testParsePat()
    {
        $data = $this->getTestFileContent('2_mpegts_pat_packets.ts');

        $mpegTsParser = new \PhpBg\MpegTs\Parser();
        $mpegTsParser->filterAllPids = true;

        $dvbPsiParser = ParserFactory::create();

        $dvbPsiParser->on('error', function ($e) {
            $this->assertTrue(false);
        });
        $incomingPat = null;
        $dvbPsiParser->on('pat', function ($pat) use (&$incomingPat) {
            if ($incomingPat !== null) {
                throw new \Exception('Only one pat is expected');
            }
            $incomingPat = $pat;
        });
        $mpegTsParser->on('error', function ($e) {
            $this->assertTrue(false);
        });
        $mpegTsParser->on('pes', function ($pid, $data) use ($dvbPsiParser) {
            $dvbPsiParser->write($pid, $data);
        });

        $mpegTsParser->write($data);

        $this->assertNotNull($incomingPat);
        $this->assertInstanceOf(Pat::class, $incomingPat);
        $this->assertSame(7, count($incomingPat->programs));
        $this->assertSame(16, $incomingPat->version);
    }

    public function testParseTdt()
    {
        $data = $this->getTestFileContent('2_mpegts_tdt_packets.ts');

        $mpegTsParser = new \PhpBg\MpegTs\Parser();
        $mpegTsParser->filterAllPids = true;

        $dvbPsiParser = ParserFactory::create();

        $dvbPsiParser->on('error', function ($e) {
            $this->assertTrue(false);
        });
        $incomingTdt = null;
        $dvbPsiParser->on('tdt', function ($tdt) use (&$incomingTdt) {
            if ($incomingTdt !== null) {
                throw new \Exception('Only one pat is expected');
            }
            $incomingTdt = $tdt;
        });
        $mpegTsParser->on('error', function ($e) {
            $this->assertTrue(false);
        });
        $mpegTsParser->on('pes', function ($pid, $data) use ($dvbPsiParser) {
            $dvbPsiParser->write($pid, $data);
        });

        $mpegTsParser->write($data);

        $this->assertSame(1525979334, $incomingTdt);
    }

    public function testParseEit()
    {
        $data = $this->getTestFileContent('16_mpegts_eit_packets.ts');

        $mpegTsParser = new \PhpBg\MpegTs\Parser();
        $mpegTsParser->filterAllPids = true;

        $dvbPsiParser = ParserFactory::create();

        $dvbPsiParser->on('error', function ($e) {
            $this->assertTrue(false);
        });
        $incomingEit = null;
        $dvbPsiParser->on('eit', function ($eit) use (&$incomingEit) {
            if ($incomingEit !== null) {
                throw new \Exception('Only one eit is expected');
            }
            $incomingEit = $eit;
        });
        $mpegTsParser->on('error', function ($e) {
            $this->assertTrue(false);
        });
        $mpegTsParser->on('pes', function ($pid, $data) use ($dvbPsiParser) {
            $dvbPsiParser->write($pid, $data);
        });

        $mpegTsParser->write($data);

        $this->assertInstanceOf(Eit::class, $incomingEit);
        $this->assertSame(260, $incomingEit->serviceId);
        $this->assertSame(31, $incomingEit->versionNumber);
        $this->assertSame(1, $incomingEit->currentNextIndicator);
        $this->assertSame(16, $incomingEit->sectionNumber);
        $this->assertSame(120, $incomingEit->lastSectionNumber);
        $this->assertSame(1, $incomingEit->transportStreamId);
        $this->assertSame(8442, $incomingEit->originalNetworkId);
        $this->assertSame(16, $incomingEit->segmentLastSectionNumber);
        $this->assertSame(80, $incomingEit->lastTableId);
        $this->assertSame(9, count($incomingEit->events));
    }
}