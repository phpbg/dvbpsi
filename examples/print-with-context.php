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

require_once __DIR__ . '/../vendor/autoload.php';

// Check args and open files
if (count($argv) !== 2) {
    echo "Non redundant dump of all PSI Tables present in TS file\r\n";
    echo "Usage: php print-with-context.php <infile>\r\n";
    echo "Try piping with 'grep Summary -A 5' to get EIT statistics only\r\n";
    return;
}
if (!is_file($argv[1])) {
    echo "$argv[1] is not a file\r\n";
    return;
}
$inFileHandle = fopen($argv[1], 'rb');
if ($inFileHandle === false) {
    throw new Exception();
}

// Global and stream context
$globalContext = new \PhpBg\DvbPsi\Context\GlobalContext();
$streamContext = new \PhpBg\DvbPsi\Context\StreamContext();
$streamContext->on('update', function () use ($streamContext) {
    echo "Stream context update:\n";
    echo $streamContext;
});
$streamContext->on('time-update', function () use ($streamContext) {
    echo "Time update: " . date('Y-m-d H:i:s', $streamContext->tdtTimestamp) . "\n";
});

// Prepare dvb psi parser
$dvbPsiParser = \PhpBg\DvbPsi\ParserFactory::create();
$dvbPsiParser->on('error', function ($e) {
    echo "PSI parser error: {$e->getMessage()}\n";
});
$dvbPsiParser->on('pat', function ($pat) use ($streamContext) {
    $streamContext->addPat($pat);
});
$dvbPsiParser->on('tdt', function ($tdt) use ($streamContext) {
    $streamContext->setTdtTimestamp($tdt);
});
$dvbPsiParser->on('eit', function ($eit) use ($globalContext) {
    $globalContext->addEit($eit);
});
$dvbPsiParser->on('pmt', function ($pmt) use ($streamContext) {
    $streamContext->addPmt($pmt);
});

// Prepare mpegts parser
$mpegTsParser = \PhpBg\MpegTs\ParserFactory::createForDvbPsi();
$mpegTsParser->on('error', function ($e) {
    echo "TS parser error: {$e->getMessage()}\n";
});
$mpegTsParser->on('pes', function ($pid, $data) use ($dvbPsiParser) {
    $dvbPsiParser->write($pid, $data);
});

// Register PMT on PAT updates
$oldPat = null;
$streamContext->on('pat-update', function () use ($streamContext, $dvbPsiParser, $mpegTsParser, &$oldPat) {
    $pmtParser = new \PhpBg\DvbPsi\TableParsers\Pmt();
    $newPids = array_values($streamContext->pat->programs);
    $pmtParser->setPids($newPids);
    $dvbPsiParser->registerTableParser($pmtParser);
    $oldPids = isset($oldPat) ? array_values($oldPat->programs) : [];
    foreach ($oldPids as $pid) {
        $mpegTsParser->removePidFilter(new \PhpBg\MpegTs\Pid($pid));
    }
    foreach ($newPids as $pid) {
        $mpegTsParser->addPidFilter(new \PhpBg\MpegTs\Pid($pid));
    }
});

// Prepare packetizer
$packetizer = new \PhpBg\MpegTs\Packetizer();
$packetizer->on('error', function ($e) {
    echo "TS packetizer error: {$e->getMessage()}\n";
});
$packetizer->on('data', function ($data) use ($mpegTsParser) {
    $mpegTsParser->write($data);
});

// Read file and write packets
while (!feof($inFileHandle)) {
    $data = fread($inFileHandle, 1880);
    if (false === $data) {
        throw new Exception("Unable to read");
    }
    $packetizer->write($data);
}

fclose($inFileHandle);

// We have no way to know when a full aggregated EIT table is available.
// TODO try to find a way to print $globalContext when a full aggregated EIT table is available.
echo $globalContext;

echo "Done\r\n";