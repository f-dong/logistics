<?php

/*
 * This file is part of the daley/logistics.
 *
 * (c) daley <poicue@163.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Daley\Logistics;

use Daley\Logistics\Exceptions\InvalidArgumentException;

class Config
{
    /**
     * 服务商key配置.
     *
     * @var string[][]
     */
    protected static $config = [
        'kuaidibird' => ['app_key' => 'app_key', 'app_secret' => 'app_secret', 'vip' => false], // 快递鸟
        'kuaidi100' => ['app_key' => 'app_key', 'app_secret' => 'app_secret'], // 快递100
        'shujuzhihui' => ['app_key' => 'app_key'], // 数据智汇
        'jisu' => ['app_key' => 'app_key'], // 极速数据
    ];

    /**
     * 获取配置.
     *
     * @param string $key 获取渠道名
     *
     * @return array
     *
     * @throws InvalidArgumentException
     */
    public static function get($key)
    {
        if (!isset(self::$config[$key])) {
            throw new InvalidArgumentException('Invalid parameter:'.$key);
        }

        return self::$config[$key];
    }

    /**
     * 更新配置.
     *
     * @param array $config 配置参数
     *
     * @return array
     */
    public static function set(array $config)
    {
        self::$config = array_merge(self::$config, $config);

        return self::$config;
    }
}
