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

use PhpBg\DvbPsi\Descriptors\NetworkName;
use PhpBg\DvbPsi\Descriptors\PrivateDescriptors\EACEM\LogicalChannel;
use PhpBg\DvbPsi\Descriptors\TerrestrialDeliverySystem;
use PhpBg\DvbPsi\Parser;
use PhpBg\DvbPsi\ParserFactory;
use PhpBg\DvbPsi\TableParsers\Pmt;
use PhpBg\DvbPsi\TableParsers;
use PhpBg\DvbPsi\TableParsers\TableParserInterface;
use PhpBg\DvbPsi\Tables\Eit;
use PhpBg\DvbPsi\Tables\Nit;
use PhpBg\DvbPsi\Tables\NitTs;
use PhpBg\DvbPsi\Tables\Pat;
use PhpBg\DvbPsi\Tables\Sdt;
/**
 * For all tests use wireshark to inspect data samples and check expected values
 */
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

    public function testParsePmt()
    {
        /**
         * This file contains a PMT at PID 0xd2 with:
         *   program number 0x111
         *   pcr 0xdc
         *   6 elementary streams descriptors
         */
        $data = $this->getTestFileContent('2_mpegts_pmt_packets.ts');

        $mpegTsParser = new \PhpBg\MpegTs\Parser();
        $mpegTsParser->filterAllPids = true;

        $dvbPsiParser = ParserFactory::create();
        $pmtParser = new Pmt();
        $pmtParser->setPids([0xd2]);
        $dvbPsiParser->registerTableParser($pmtParser);

        $dvbPsiParser->on('error', function ($e) {
            $this->assertTrue(false);
        });
        $incomingPmt = null;
        $dvbPsiParser->on('pmt', function ($pmt) use (&$incomingPmt) {
            if ($incomingPmt !== null) {
                throw new \Exception('Only one pmt is expected');
            }
            $incomingPmt = $pmt;
        });
        $mpegTsParser->on('error', function ($e) {
            $this->assertTrue(false);
        });
        $mpegTsParser->on('pes', function ($pid, $data) use ($dvbPsiParser) {
            $dvbPsiParser->write($pid, $data);
        });

        $mpegTsParser->write($data);

        $this->assertNotNull($incomingPmt);
        $this->assertInstanceOf(\PhpBg\DvbPsi\Tables\Pmt::class, $incomingPmt);
        $this->assertSame(6, count($incomingPmt->streams));
        $this->assertSame(0, $incomingPmt->version);
        $this->assertSame(0xdc, $incomingPmt->pcrPid);
        $this->assertSame(0x111, $incomingPmt->programNumber);
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

        $this->assertSame(1526065734, $incomingTdt);
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

    /**
     * @dataProvider getRegisteredPidsDatasource
     */
    public function testGetRegisteredPids(TableParserInterface $tableParser)
    {
        $parser = new Parser();
        $this->assertEmpty($parser->getRegisteredPids());
        $parser->registerTableParser($tableParser);
        $this->assertSame(count($tableParser->getPids()), count($parser->getRegisteredPids()));
        $this->assertEquals($tableParser->getPids(), $parser->getRegisteredPids());

        $parser->unregisterTableParser($tableParser);
        $this->assertEmpty($parser->getRegisteredPids());
    }

    public function getRegisteredPidsDatasource()
    {
        return [
            [new \PhpBg\DvbPsi\TableParsers\Pat()],
            [new \PhpBg\DvbPsi\TableParsers\Eit()],
            [new \PhpBg\DvbPsi\TableParsers\Tdt()]
        ];
    }

    public function testParseNit()
    {
        $data = $this->getTestFileContent('8_mpegts_nit_packets.ts');

        $mpegTsParser = new \PhpBg\MpegTs\Parser();
        $mpegTsParser->filterAllPids = true;

        $dvbPsiParser = ParserFactory::create();

        $dvbPsiParser->on('error', function ($e) {
            $this->assertTrue(false);
        });
        $incomingNit = null;
        $dvbPsiParser->on('nit', function ($eit) use (&$incomingNit) {
            if ($incomingNit !== null) {
                throw new \Exception('Only one nit is expected');
            }
            $incomingNit = $eit;
        });
        $mpegTsParser->on('error', function ($e) {
            $this->assertTrue(false);
        });
        $mpegTsParser->on('pes', function ($pid, $data) use ($dvbPsiParser) {
            $dvbPsiParser->write($pid, $data);
        });

        $mpegTsParser->write($data);

        $this->assertInstanceOf(Nit::class, $incomingNit);
        /**
         * @var Nit $incomingNit
         */
        $this->assertSame(0x20fa, $incomingNit->networkId);
        $this->assertSame(0x1e, $incomingNit->versionNumber);
        $this->assertSame(0x1, $incomingNit->currentNextIndicator);
        $this->assertSame(1, count($incomingNit->descriptors));
        $nnd = current($incomingNit->descriptors);
        $this->assertInstanceOf(NetworkName::class, $nnd);
        /**
         * @var NetworkName $nnd
         */
        $this->assertSame('F', $nnd->networkName);

        $this->assertSame(7, count($incomingNit->transportStreams));
        $stream004 = null;
        foreach ($incomingNit->transportStreams as $ts) {
            if ($ts->transportStreamId === 0x4) {
                $stream004 = $ts;
            }
        }
        $this->assertInstanceOf(NitTs::class, $stream004);
        $this->assertSame(0x20fa, $stream004->networkId);

        $this->assertSame(4, count($stream004->descriptors));

        $tds = null;
        $lcn = null;
        foreach ($stream004->descriptors as $descriptor) {
            if ($descriptor instanceof TerrestrialDeliverySystem) {
                $tds = $descriptor;
            }
            if ($descriptor instanceof LogicalChannel) {
                $lcn = $descriptor;
            }
        }
        $this->assertInstanceOf(TerrestrialDeliverySystem::class, $tds);
        $this->assertSame(42949672950, $tds->centreFrequency);
        $this->assertSame(8000000, $tds->bandwidth);
        $this->assertSame('64-QAM', TerrestrialDeliverySystem::CONSTELLATION_MAPPING[$tds->constellation]);
        $this->assertSame('1/8', TerrestrialDeliverySystem::GUARD_INTERVAL_MAPPING[$tds->guardInterval]);

        $this->assertInstanceOf(LogicalChannel::class, $lcn);
        $this->assertSame(5, count($lcn->services));
    }

    public function testParseSdt()
    {
        $data = $this->getTestFileContent('2_mpegts_sdt_packets.ts');

        $mpegTsParser = new \PhpBg\MpegTs\Parser();
        $mpegTsParser->filterAllPids = true;

        $dvbPsiParser = ParserFactory::create();
        $sdtParser = new TableParsers\Sdt();
        $dvbPsiParser->registerTableParser($sdtParser);

        $dvbPsiParser->on('error', function ($e) {
            $this->assertTrue(false);
        });
        $incomingSdt = null;
        $dvbPsiParser->on('sdt', function ($sdt) use (&$incomingSdt) {
            if ($incomingSdt !== null) {
                throw new \Exception('Only one sdt is expected');
            }
            $incomingSdt = $sdt;
        });
        $mpegTsParser->on('error', function ($e) {
            $this->assertTrue(false);
        });
        $mpegTsParser->on('pes', function ($pid, $data) use ($dvbPsiParser) {
            $dvbPsiParser->write($pid, $data);
        });

        $mpegTsParser->write($data);

        $this->assertInstanceOf(Sdt::class, $incomingSdt);
        $this->assertSame(34, $incomingSdt->transportStreamId);
        $this->assertSame(65280, $incomingSdt->originalNetworkId);
        $this->assertSame(2, $incomingSdt->versionNumber);
        $this->assertSame(1, $incomingSdt->currentNextIndicator);
        $this->assertSame(0, $incomingSdt->sectionNumber);
        $this->assertSame(0, $incomingSdt->lastSectionNumber);
        $this->assertSame(1115, $incomingSdt->services[0]->serviceId);
        $this->assertSame(0, $incomingSdt->services[0]->eitScheduleFlag);
        $this->assertSame(0, $incomingSdt->services[0]->eitPresentFollowingFlag);
        $this->assertSame(4, $incomingSdt->services[0]->runningStatus);
        $this->assertSame(0, $incomingSdt->services[0]->freeCaMode);
        $this->assertSame(0x19, $incomingSdt->services[0]->descriptors[0]->serviceType->getValue());
        $this->assertSame('EDMR', $incomingSdt->services[0]->descriptors[0]->serviceProviderName);
        $this->assertSame('VEXEHD', $incomingSdt->services[0]->descriptors[0]->serviceName);
    }
}