<?php

/*
 * This file is part of the uuk020/logistics.
 *
 * (c) daley <poicue@163.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Daley\Logistics\Tests;

use Daley\Logistics\Util\ChannelCode;
use Daley\Logistics\Util\UserAgent;
use GuzzleHttp\Client;
use PHPUnit\Framework\TestCase;
use Daley\Logistics\Exceptions\InvalidArgumentException;
use Daley\Logistics\Logistics;

class LogisticsTest extends TestCase
{
    use UserAgent;

    /**
     * 测试不传数据.
     *
     * @throws InvalidArgumentException
     * @throws \Daley\Logistics\Exceptions\NoAvailableException
     */
    public function testChannelWithInvalidParams()
    {
        $logistics = new Logistics();
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('code arguments cannot empty.');
        $logistics->query('');
    }

    /**
     * 测试不存在渠道.
     *
     * @throws InvalidArgumentException
     * @throws \Daley\Logistics\Exceptions\NoAvailableException
     */
    public function testChannelWithChannelClass()
    {
        $logistics = new Logistics();
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Class "Daley\Logistics\Channel\KuaidBirdChannel" not exists.');
        $logistics->setChannel('kuaidBird')->query('123123');
    }

    /**
     * 测试取快递编码
     *
     * @throws InvalidArgumentException
     */
    public function testSupportLogistics()
    {
        $supportLogistics = \Mockery::mock(ChannelCode::class);
        $supportLogistics->shouldReceive('getCode')->andReturn('yuantong');
        $this->assertSame('yuantong', $supportLogistics->getCode('kuaidi100', 'YT9200095554375', new Client(['timeout' => 10, 'headers' => $this->getUserAgent(), 'verify' => false])));
    }
}
