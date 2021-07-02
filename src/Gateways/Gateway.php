<?php

/*
 * This file is part of the daley/logistics.
 *
 * (c) daley <fdong26@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Daley\Logistics\Gateways;

use Daley\Logistics\Contracts\GatewayInterface;
use Daley\Logistics\Support\Config;

/**
 * Class Gateway.
 */
abstract class Gateway implements GatewayInterface
{
    const DEFAULT_TIMEOUT = 5.0;

    /**
     * @var \Daley\Logistics\Support\Config
     */
    protected $config;

    /**
     * @var array
     */
    protected $options;

    /**
     * @var float
     */
    protected $timeout;

    /**
     * Gateway constructor.
     *
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->config = new Config($config);
    }

    /**
     * Return timeout.
     *
     * @return int|mixed
     */
    public function getTimeout()
    {
        return $this->timeout ?: $this->config->get('timeout', self::DEFAULT_TIMEOUT);
    }

    /**
     * Set timeout.
     *
     * @param int $timeout
     *
     * @return $this
     */
    public function setTimeout($timeout)
    {
        $this->timeout = floatval($timeout);

        return $this;
    }

    /**
     * @return \Daley\Logistics\Support\Config
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * @param \Daley\Logistics\Support\Config $config
     *
     * @return $this
     */
    public function setConfig(Config $config)
    {
        $this->config = $config;

        return $this;
    }

    /**
     * @param $options
     *
     * @return $this
     */
    public function setGuzzleOptions($options)
    {
        $this->options = $options;

        return $this;
    }

    /**
     * @return array
     */
    public function getGuzzleOptions()
    {
        return $this->options ?: $this->config->get('options', []);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return \strtolower(str_replace([__NAMESPACE__.'\\', 'Gateway'], '', \get_class($this)));
    }
}
