<?php

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
     * Constructor.
     *
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->config = new Config($config);
    }

    public function gateway($name)
    {
        if (!isset($this->gateways[$name])) {
            $this->gateways[$name] = $this->createGateway($name);
        }

        return $this->gateways[$name];
    }

    /**
     * @return \Daley\Logistics\Support\Config
     */
    public function getConfig()
    {
        return $this->config;
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
        if (!\class_exists($gateway) || !\in_array(GatewayInterface::class, \class_implements($gateway))) {
            throw new InvalidArgumentException(\sprintf('Class "%s" is a invalid logistics gateway.', $gateway));
        }

        return new $gateway($config);
    }
}
