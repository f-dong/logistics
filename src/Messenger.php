<?php

/*
 * This file is part of the daley/logistics.
 *
 * (c) daley <fdong26@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Daley\Logistics;

use Daley\Logistics\Exceptions\NoGatewayAvailableException;

/**
 * Class Messenger.
 */
class Messenger
{
    const STATUS_SUCCESS = 'success';

    const STATUS_FAILURE = 'failure';

    /**
     * @var \Daley\Logistics\Logistics
     */
    protected $logistics;

    /**
     * Messenger constructor.
     *
     * @param \Daley\Logistics\Logistics $logistics
     */
    public function __construct(Logistics $logistics)
    {
        $this->logistics = $logistics;
    }

    /**
     * Send a message.
     *
     * @param string $code
     * @param array  $gateways
     *
     * @return array
     *
     * @throws \Daley\Logistics\Exceptions\NoGatewayAvailableException
     */
    public function query($code, array $gateways = [])
    {
        $results = [];
        $isSuccessful = false;

        foreach ($gateways as $gateway => $config) {
            try {
                $results[$gateway] = [
                    'gateway' => $gateway,
                    'status' => self::STATUS_SUCCESS,
                    'result' => $this->logistics->gateway($gateway)->query($code, $config),
                ];
                $isSuccessful = true;

                break;
            } catch (\Exception $e) {
                $results[$gateway] = [
                    'gateway' => $gateway,
                    'status' => self::STATUS_FAILURE,
                    'exception' => $e,
                ];
            }
        }

        if (!$isSuccessful) {
            throw new NoGatewayAvailableException($results);
        }

        return $results;
    }
}
