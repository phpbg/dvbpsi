<?php

/**
 * MIT License
 *
 * Copyright (c) 2019 Samuel CHEMLA
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

namespace PhpBg\DvbPsi\TableParsers;

use PhpBg\DvbPsi\Descriptors\Component;
use PhpBg\DvbPsi\Descriptors\Content;
use PhpBg\DvbPsi\Descriptors\ExtendedEvent;
use PhpBg\DvbPsi\Descriptors\Identifier;
use PhpBg\DvbPsi\Descriptors\NetworkName;
use PhpBg\DvbPsi\Descriptors\ParentalRating;
use PhpBg\DvbPsi\Descriptors\PrivateDataSpecifier;
use PhpBg\DvbPsi\Descriptors\ServiceList;
use PhpBg\DvbPsi\Descriptors\ShortEvent;
use PhpBg\DvbPsi\Descriptors\TerrestrialDeliverySystem;
use PhpBg\DvbPsi\Exception;

abstract class TableParserAbstract implements TableParserInterface
{

    /**
     * Parse a descriptor loop
     * @param string $data
     * @param int $currentPointer
     * @param int $descriptorLoopLength
     * @return array
     * @throws Exception
     */
    protected function parseDescriptorsLoop(string $data, int $currentPointer, int $descriptorLoopLength)
    {
        $descriptors = [];
        if ($descriptorLoopLength === 0) {
            return $descriptors;
        }
        // Safeguard
        $dataLen = strlen($data);
        $descriptorsEnd = $currentPointer + $descriptorLoopLength;
        if ($descriptorsEnd > $dataLen) {
            throw new Exception("Descriptors loop parse overflow (data length exceeded)");
        }
        while ($currentPointer < $descriptorsEnd) {
            // Safeguard, again
            if ($currentPointer >= $dataLen) {
                throw new Exception("Parse overflow (data length exceeded)");
            }
            $descriptorTag = unpack('C', $data[$currentPointer])[1];
            $currentPointer += 1;
            $descriptorLen = unpack('C', $data[$currentPointer])[1];
            $currentPointer += 1;
            try {
                $descriptors[] = $this->parseDescriptor($descriptorTag, $data, $currentPointer, $descriptorLen);
            } catch (Exception $e) {
                // Don't throw on descriptor parse error, because this allows retrieving descriptors that didn't fail
                echo $e->getMessage() . "\n";
            } finally {
                $currentPointer += $descriptorLen;
            }
        }
        return $descriptors;
    }

    /**
     * Parse a descriptor
     *
     * @param int $descriptorId
     * @param string $data
     * @param int $currentPointer
     * @param int $descriptorLength
     * @throws Exception
     * @return mixed|null
     */
    protected function parseDescriptor(int $descriptorId, string $data, int $currentPointer, int $descriptorLength)
    {
        if ($descriptorLength === 0) {
            return null;
        }
        $dataLen = strlen($data);
        if ($currentPointer + $descriptorLength > $dataLen) {
            throw new Exception("Descriptor length overflow");
        }
        $descriptorData = substr($data, $currentPointer, $descriptorLength);

        switch ($descriptorId) {
            //TODO handle private descriptor tag : 0x83 in case data specifier is 0x00000028 EACEM. In that case it provides logical channel numbering
            case Identifier::TERRESTRIAL_DELIVERY_SYSTEM_DESCRIPTOR:
                return new TerrestrialDeliverySystem($descriptorData);

            case Identifier::NETWORK_NAME_DESCRIPTOR:
                return new NetworkName($descriptorData);

            case Identifier::PRIVATE_DATA_SPECIFIER_DESCRIPTOR:
                return new PrivateDataSpecifier($descriptorData);

            case Identifier::SERVICE_LIST_DESCRIPTOR:
                return new ServiceList($descriptorData);

            case Identifier::SHORT_EVENT_DESCRIPTOR:
                return new ShortEvent($descriptorData);

            case Identifier::EXTENDED_EVENT_DESCRIPTOR:
                return new ExtendedEvent($descriptorData);

            case Identifier::PARENTAL_RATING_DESCRIPTOR:
                return new ParentalRating($descriptorData);

            case Identifier::COMPONENT_DESCRIPTOR:
                return new Component($descriptorData);

            case Identifier::CONTENT_DESCRIPTOR:
                return new Content($descriptorData);

            default:
                throw new Exception(sprintf("Unhandled descriptor tag : 0x%x", $descriptorId));
        }
    }
}