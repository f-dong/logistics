<?php

/*
 * This file is part of the daley/logistics.
 *
 * (c) daley <fdong26@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Daley\Logistics\Contracts;

use Daley\Logistics\Support\Config;

/**
 * Class GatewayInterface.
 */
interface GatewayInterface
{
    /**
     * Get gateway name.
     *
     * @return string
     */
    public function getName();


    public function query($code, Config $config);
}
