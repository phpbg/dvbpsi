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

use PhpBg\DvbPsi\TableParsers\Eit;
use PhpBg\DvbPsi\TableParsers\Nit;
use PhpBg\DvbPsi\TableParsers\Pat;
use PhpBg\DvbPsi\TableParsers\Sdt;
use PhpBg\DvbPsi\TableParsers\Tdt;

class ParserFactory
{
    /**
     * @return Parser
     * @throws Exception
     */
    public static function create(): Parser
    {
        $parser = new Parser();
        $parser->registerTableParser(new Pat());
        $parser->registerTableParser(new Nit());
        $parser->registerTableParser(new Tdt());
        $parser->registerTableParser(new Eit());
        $parser->registerTableParser(new Sdt());
        return $parser;
    }
}