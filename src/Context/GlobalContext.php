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

namespace PhpBg\DvbPsi\Context;


use Evenement\EventEmitter;
use PhpBg\DvbPsi\Tables\Eit;

class GlobalContext extends EventEmitter
{
    protected $eitByNetworks = [];

    public function addEit(Eit $eit)
    {
        if (!isset($this->eitByNetworks[$eit->originalNetworkId])) {
            $this->eitByNetworks[$eit->originalNetworkId] = [];
        }

        if (!isset($this->eitByNetworks[$eit->originalNetworkId][$eit->transportStreamId])) {
            $this->eitByNetworks[$eit->originalNetworkId][$eit->transportStreamId] = [];
        }

        if (!isset($this->eitByNetworks[$eit->originalNetworkId][$eit->transportStreamId][$eit->serviceId])) {
            $this->eitByNetworks[$eit->originalNetworkId][$eit->transportStreamId][$eit->serviceId] = new EitServiceAggregator();
        }

        $update = $this->eitByNetworks[$eit->originalNetworkId][$eit->transportStreamId][$eit->serviceId]->add($eit);

        if ($update) {
            $this->emit('partial-update');
        }
    }

    /**
     * Return an array of EitServiceAggregator, grouped by network ID, transport stream ID and service ID
     * E.g. [
     *          <network id> => [
     *              <transport stream id> => [
     *                  <service id> => <EitServiceAggregator instance>
     *              ]
     *          ]
     *      ]
     *
     * @return array
     */
    public function getAllEvents()
    {
        return $this->eitByNetworks;
    }

    public function __toString()
    {
        $str = '';
        if (!empty($this->eitByNetworks)) {
            foreach ($this->eitByNetworks as $networkId => $transportStreams) {
                $str .= sprintf("Network: %d (0x%x)\n", $networkId, $networkId);
                foreach ($transportStreams as $transportStream => $services) {
                    $str .= sprintf("\tTransport Stream: %d (0x%x)\n", $transportStream, $transportStream);
                    foreach ($services as $service => $eitAggregator) {
                        $str .= sprintf("\t\tService: %d (0x%x)\n", $service, $service);
                        $str .= (string)$eitAggregator;
                    }
                }
            }
        }
        return $str;
    }
}