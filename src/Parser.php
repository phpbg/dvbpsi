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

namespace PhpBg\DvbPsi;

use Evenement\EventEmitter;
use PhpBg\DvbPsi\TableParsers\TableParserInterface;

/**
 * Class Parser
 *
 * error event:
 *     The `error` event will be emitted when an error occurs, usually while parsing data
 *     The event receives a single `Exception` argument for the error instance.
 *
 * pat event:
 *     The `pat` event will be emitted when a PAT table is decoded
 *     The event will receive a single argument: PhpBg\DvbPsi\Tables\Pat instance
 *
 * pmt event:
 *     The `pmt` event will be emitted when a PMT table is decoded
 *     The event will receive a single argument: PhpBg\DvbPsi\Tables\Pmt
 *
 * tdt event:
 *     The `tdt` event will be emitted when a TDT table is decoded
 *     The event will receive a single int argument: a unix timestamp
 *
 * eit event:
 *     The `eit` event will be emitted when an EIT table is decoded
 *     The event will receive a single argument: PhpBg\DvbPsi\Tables\Eit instance
 *
 * parserChange event:
 *     The `parserChange` will be emitted when a parser is added or removed
 *
 * TODO other events?
 */
class Parser extends EventEmitter
{
    protected $parsers = [];

    /**
     * Register a SI table parser
     * @param TableParserInterface $parser
     * @throws Exception
     */
    public function registerTableParser(TableParserInterface $parser)
    {
        $pids = $parser->getPids();
        foreach ($pids as $pid) {
            if (!isset($this->parsers[$pid])) {
                $this->parsers[$pid] = [];
            }
            foreach ($parser->getTableIds() as $tableId) {
                if (isset($this->parsers[$pid][$tableId])) {
                    throw new Exception("Parser already registered for PID: {$pid} and Table ID: {$tableId}");
                }
                $this->parsers[$pid][$tableId] = $parser;
                $this->emit('parserChange');
            }
        }
    }

    /**
     * Unregister a SI table parser
     * @param TableParserInterface $parser
     */
    public function unregisterTableParser(TableParserInterface $parser) {
        $pids = $parser->getPids();
        foreach ($pids as $pid) {
            foreach ($parser->getTableIds() as $tableId) {
                if ($this->parsers[$pid][$tableId] == $parser) {
                    unset($this->parsers[$pid][$tableId]);
                }
            }
            if (empty($this->parsers[$pid])) {
                unset($this->parsers[$pid]);
            }
        }
        $this->emit('parserChange');
    }

    /**
     * Return PIDs that have at least one registered parser
     * @return array
     */
    public function getRegisteredPids(): array
    {
        return array_keys($this->parsers);
    }

    public function write(int $pid, string $data)
    {
        try {
            $this->feed($pid, $data);
        } catch (\Exception $e) {
            $this->emit('error', [$e]);
        }
    }

    protected function feed($pid, $data)
    {
        $len = strlen($data);
        $pointer = unpack('C', $data[0])[1];

        $currentPointer = 1 + $pointer;
        while ($currentPointer < $len) {
            $tableId = unpack('C', $data[$currentPointer])[1];

            //Table Identifier, that defines the structure of the syntax section and other
            //contained data. As an exception, if this is the byte that immediately follow
            //previous table section and is set to 0xFF, then it indicates that the repeat of table
            //section end here and the rest of TS packet payload shall be stuffed with 0xFF.
            //Consequently, the value 0xFF shall not be used for the Table Identifier.
            if ($tableId === 0xff) {
                return;
            }

            $headersBin = substr($data, $currentPointer + 1, 2);
            $headers = unpack('n', $headersBin)[1];

            // According to wikipedia section length is 2 unused (zero) bits then 10 bits
            // According to ETSI it is 12bits
            // Whoever is right, we can use 12bits assuming the two first bits will be zero if unused
            $sectionLength = $headers & 0xfff;

            //Move pointer after 3 bytes of header
            $currentPointer += 3;

            // Parse table
            if (!isset($this->parsers[$pid][$tableId])) {
                throw new Exception(sprintf("No parser for PID %d (0x%x) table ID %d (0x%x)\n", $pid, $pid, $tableId, $tableId));
            }
            $parsed = $this->parsers[$pid][$tableId]->parse($tableId, $data, $currentPointer, $sectionLength);
            $this->emit($this->parsers[$pid][$tableId]->getEventName(), [$parsed]);

            // Move pointer
            $currentPointer += $sectionLength;
        }
    }
}