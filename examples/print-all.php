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
    echo "Dump all PSI Tables present in TS file\r\n";
    echo "Usage: php print-all.php <infile>\r\n";
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

// Prepare dvb psi parser
$dvbPsiParser = \PhpBg\DvbPsi\ParserFactory::create();
$dvbPsiParser->on('error', function ($e) {
    echo "PSI parser error: {$e->getMessage()}\n";
});
$dvbPsiParser->on('pat', function ($pat) {
    echo "PAT\r\n{$pat}\r\n";
});
$dvbPsiParser->on('nit', function ($nit) {
    echo "NIT\r\n{$nit}\r\n";
});
$dvbPsiParser->on('tdt', function ($tdt) {
    echo "TDT: {$tdt}\n";
});
$dvbPsiParser->on('eit', function ($eit) {
    echo "EIT\r\n{$eit}\n";
});
$dvbPsiParser->on('pmt', function ($pmt) {
    echo "PMT\r\n{$pmt}\n";
});

// Create MPEG TS parser and filter all requested PIDs
$mpegTsParser = new \PhpBg\MpegTs\Parser();
foreach($dvbPsiParser->getRegisteredPids() as $pid) {
    $mpegTsParser->addPidFilter(new \PhpBg\MpegTs\Pid($pid));
}
$mpegTsParser->on('error', function ($e) {
    echo "TS parser error: {$e->getMessage()}\n";
});
$mpegTsParser->on('pes', function ($pid, $data) use ($dvbPsiParser) {
    $dvbPsiParser->write($pid, $data);
});

// Prepare packetizer
$packetizer = new \PhpBg\MpegTs\Packetizer();
$packetizer->on('error', function ($e) {
    echo "TS packetizer error: {$e->getMessage()}\n";
});
$packetizer->on('data', function ($data) use ($mpegTsParser) {
    $mpegTsParser->write($data);
});

// Register PMT on PAT updates
$streamContext = new \PhpBg\DvbPsi\Context\StreamContext();
$streamContext->on('pat-update', function ($newPat, $oldPat) use ($dvbPsiParser, $mpegTsParser) {
    $pmtParser = new \PhpBg\DvbPsi\TableParsers\Pmt();
    $newPids = array_values($newPat->programs);
    $pmtParser->setPids($newPids);
    $dvbPsiParser->registerTableParser($pmtParser);
    $oldPids = isset($oldPat) ? array_values($oldPat->programs) : [];
    foreach ($oldPids as $pid) {
        if ($pid == \PhpBg\MpegTs\Pid::NIT_ST) {
            // This is NIT (PAT program 0), we don't support it yet
            continue;
        }
        $mpegTsParser->removePidFilter(new \PhpBg\MpegTs\Pid($pid));
    }
    foreach ($newPids as $pid) {
        if ($pid == \PhpBg\MpegTs\Pid::NIT_ST) {
            // This is NIT (PAT program 0), we don't support it yet
            continue;
        }
        $mpegTsParser->addPidFilter(new \PhpBg\MpegTs\Pid($pid));
    }
});
$dvbPsiParser->on('pat', function ($pat) use ($streamContext) {
    $streamContext->addPat($pat);
});
$dvbPsiParser->on('pmt', function ($pmt) use ($streamContext) {
    $streamContext->addPmt($pmt);
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

echo "Done\r\n";