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

use Daley\Logistics\Contracts\GatewayInterface;
use Daley\Logistics\Exceptions\InvalidArgumentException;
use Daley\Logistics\Gateways\Gateway;
use Daley\Logistics\Support\Config;

class Logistics
{
    /**
     * @var \Daley\Logistics\Support\Config
     */
    protected $config;

    /**
     * @var string
     */
    protected $defaultGateway;

    /**
     * @var array
     */
    protected $gateways = [];

    /**
     * @var array
     */
    protected $customCreators = [];

    /**
     * @var \Daley\Logistics\Messenger
     */
    protected $messenger;

    /**
     * Constructor.
     *
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->config = new Config($config);
    }

    /**
     * Create a gateway.
     *
     * @param string|null $name
     *
     * @return \Daley\Logistics\Contracts\GatewayInterface
     *
     * @throws \Daley\Logistics\Exceptions\InvalidArgumentException
     */
    public function gateway($name)
    {
        if (! isset($this->gateways[$name])) {
            $this->gateways[$name] = $this->createGateway($name);
        }

        return $this->gateways[$name];
    }

    /**
     * Create a new driver instance.
     *
     * @param string $name
     * @return GatewayInterface
     *
     * @throws \InvalidArgumentException
     * @throws \Daley\Logistics\Exceptions\InvalidArgumentException
     */
    protected function createGateway($name)
    {
        $config = $this->config->get("gateways.{$name}", []);

        if (!isset($config['timeout'])) {
            $config['timeout'] = $this->config->get('timeout', Gateway::DEFAULT_TIMEOUT);
        }

        $config['options'] = $this->config->get('options', []);

        if (isset($this->customCreators[$name])) {
            $gateway = $this->callCustomCreator($name, $config);
        } else {
            $className = $this->formatGatewayClassName($name);
            $gateway = $this->makeGateway($className, $config);
        }

        if (!($gateway instanceof GatewayInterface)) {
            throw new InvalidArgumentException(\sprintf('Gateway "%s" must implement interface %s.', $name, GatewayInterface::class));
        }

        return $gateway;
    }

    /**
     * @return \Daley\Logistics\Support\Config
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * @return \Daley\Logistics\Messenger
     */
    public function getMessenger()
    {
        return $this->messenger ?: $this->messenger = new Messenger($this);
    }

    /**
     * Make gateway instance.
     *
     * @param string $gateway
     * @param array  $config
     *
     * @return \Daley\Logistics\Contracts\GatewayInterface
     *
     * @throws \Daley\Logistics\Exceptions\InvalidArgumentException
     */
    protected function makeGateway($gateway, $config)
    {
        if (! \class_exists($gateway) || ! \in_array(GatewayInterface::class, \class_implements($gateway))) {
            throw new InvalidArgumentException(\sprintf('Class "%s" is a invalid logistics gateway.', $gateway));
        }

        return new $gateway($config);
    }

    /**
     * Call a custom gateway creator.
     *
     * @param string $gateway
     * @param array  $config
     *
     * @return mixed
     */
    protected function callCustomCreator($gateway, $config)
    {
        return \call_user_func($this->customCreators[$gateway], $config);
    }

    /**
     * Format gateway name.
     *
     * @param string $name
     *
     * @return string
     */
    protected function formatGatewayClassName($name)
    {
        if (\class_exists($name) && \in_array(GatewayInterface::class, \class_implements($name))) {
            return $name;
        }

        $name = \ucfirst(\str_replace(['-', '_', ''], '', $name));

        return __NAMESPACE__."\\Gateways\\{$name}Gateway";
    }
}
