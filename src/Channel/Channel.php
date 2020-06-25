<?php

/*
 * This file is part of the uuk020/logistics.
 *
 * (c) daley <poicue@163.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Daley\Logistics\Channel;

use Daley\Logistics\Config;
use Daley\Logistics\Util\UserAgent;
use GuzzleHttp\Client;

abstract class Channel
{
    use UserAgent;

    /**
     * http 实例.
     *
     * @var Client
     */
    protected $httpClient;

    /**
     * 接口地址
     *
     * @var string
     */
    protected $url;

    /**
     * 返回数据.
     *
     * @var array
     */
    protected $response;

    /**
     * 构造方法.
     */
    public function __construct()
    {
        $this->httpClient = new Client(['timeout' => 10, 'headers' => $this->getUserAgent(), 'verify' => false]);
    }

    /**
     * 获取渠道名称.
     *
     * @return string
     */
    protected function getClassName()
    {
        $name = basename(str_replace('\\', '/', static::class));

        return strtolower(preg_replace('/Channel/', '', $name));
    }

    /**
     * 获取配置.
     *
     * @return array
     *
     * @throws \Daley\Logistics\Exceptions\InvalidArgumentException
     */
    protected function getConfig()
    {
        $key = $this->getClassName();

        return Config::get($key);
    }

    /**
     * 转换为数组.
     *
     * @param string|array $response
     */
    abstract protected function toArray($response);

    /**
     * 调用查询接口.
     *
     * @param string $code
     * @param string $company
     *
     * @return array
     */
    abstract public function query($code, $company = '');

    /**
     * 统一返回格式.
     *
     * @return array
     */
    abstract protected function format();
}
