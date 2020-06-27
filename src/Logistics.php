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
use Daley\Logistics\Exceptions\NoAvailableException;

class Logistics
{
    /**
     * 成功
     *
     * @var string
     */
    const SUCCESS = 'success';

    /**
     * 失败.
     *
     * @var string
     */
    const FAILURE = 'failure';

    /**
     * 配置项.
     *
     * @var array
     */
    protected $config;

    /**
     * 渠道工厂
     *
     * @var Factory
     */
    protected $factory;

    /**
     * 默认渠道.
     *
     * @var string[]
     */
    protected $channel = [];

    /**
     * 默认渠道.
     *
     * @var string
     */
    protected $defaultChannel = 'kuaidi100';

    /**
     * 构造函数.
     *
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        $this->config = Config::set($config);

        $this->factory = new Factory();
    }

    /**
     * 设置查询渠道.
     *
     * @param string|string[] $name
     *
     * @return self
     */
    public function setChannel($name)
    {
        if (is_array($name)) {
            foreach ($name as $item) {
                $this->setChannel($item);
            }

            return $this;
        }

        if (!in_array($name, $this->channel)) {
            array_push($this->channel, $name);
        }

        return $this;
    }

    /**
     * 查询快递.
     *
     * @param string          $number  快递单号
     * @param string|string[] $company 快递公司 不填则自动获取
     * @param string          $mobile  收件 / 寄件人 手机号 顺丰快递需要
     *
     * @return array
     *
     * @throws InvalidArgumentException
     * @throws NoAvailableException
     */
    public function query($number, $company = '', $mobile = '')
    {
        $results = [];

        if (empty($number)) {
            throw new InvalidArgumentException('code arguments cannot empty.');
        }

        if (empty($this->channel)) {
            $this->setChannel($this->defaultChannel);
        }

        foreach ($this->channel as $channel) {
            // 取出快递编码
            if (is_array($company)) {
                $company_code = isset($company[$channel]) ? $company[$channel] : '';
            } else {
                $company_code = $company;
            }

            $request = $this->factory->channel($channel)->query($number, $company_code, $mobile);
            if (1 === $request['status']) {
                $results[$channel] = [
                    'channel' => $channel,
                    'status' => self::SUCCESS,
                    'result' => $request,
                ];
            } else {
                $results[$channel] = [
                    'channel' => $channel,
                    'status' => self::FAILURE,
                    'exception' => $request['message'],
                ];
            }
        }

        $collectionOfException = array_column($results, 'exception');
        if ($collectionOfException === count($this->channel)) {
            throw new NoAvailableException('no channel class available');
        }

        return $results;
    }
}
