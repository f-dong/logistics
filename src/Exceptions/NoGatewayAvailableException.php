<?php

/*
 * This file is part of the daley/logistics.
 *
 * (c) daley <fdong26@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Daley\Logistics\Exceptions;

/**
 * Class NoGatewayAvailableException.
 */
class NoGatewayAvailableException extends Exception
{
    /**
     * @var array
     */
    public $results = [];

    /**
     * @var array
     */
    public $exceptions = [];

    /**
     * NoGatewayAvailableException constructor.
     *
     * @param array           $results
     * @param int             $code
     */
    public function __construct(array $results = [], $code = 0)
    {
        $this->results = $results;
        $this->exceptions = \array_column($results, 'exception', 'gateway');

        parent::__construct('All the gateways have failed. You can get error details by `$exception->getExceptions()`', $code);
    }

    /**
     * @return array
     */
    public function getResults()
    {
        return $this->results;
    }

    /**
     * @param string $gateway
     *
     * @return mixed|null
     */
    public function getException($gateway)
    {
        return isset($this->exceptions[$gateway]) ? $this->exceptions[$gateway] : null;
    }

    /**
     * @return array
     */
    public function getExceptions()
    {
        return $this->exceptions;
    }

    /**
     * @return mixed
     */
    public function getLastException()
    {
        return end($this->exceptions);
    }
}
