<?php

/*
 * This file is part of the uuk020/logistics.
 *
 * (c) daley <poicue@163.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Daley\Logistics\Util;

use Daley\Logistics\Exceptions\InvalidArgumentException;
use GuzzleHttp\Client;

class ChannelCode
{
    /**
     * 快递公司配置
     *
     * @var string[][]
     */
    protected static $companyList = [
        ['juhe' => 'sf', 'kuaidi100' => 'shunfeng', 'kuaidibird' => 'SF'], // 顺丰
        ['juhe' => 'sto', 'kuaidi100' => 'shentong', 'kuaidibird' => 'STO'], // 申通
        ['juhe' => 'zto', 'kuaidi100' => 'zhongtong', 'kuaidibird' => 'ZTO'], // 中通
        ['juhe' => 'yt', 'kuaidi100' => 'yuantong', 'kuaidibird' => 'YTO'], // 圆通
        ['juhe' => 'yd', 'kuaidi100' => 'yunda', 'kuaidibird' => 'YD'], // 韵达
        ['juhe' => 'tt', 'kuaidi100' => 'tiantian', 'kuaidibird' => 'HHTT'], // 天天
        ['juhe' => 'ems', 'kuaidi100' => 'ems', 'kuaidibird' => 'EMS'], // ems
        ['juhe' => 'emsg', 'kuaidi100' => 'emsguoji', 'kuaidibird' => 'EMSGJ'], // ems国际
        ['juhe' => 'ht', 'kuaidi100' => 'huitongkuaidi', 'kuaidibird' => ''], // 汇通
        ['juhe' => 'qf', 'kuaidi100' => 'quanfengkuaidi', 'kuaidibird' => ''], // 全峰
        ['juhe' => 'db', 'kuaidi100' => 'debangwuliu', 'kuaidibird' => 'DBL'], // 德邦
        ['juhe' => 'gt', 'kuaidi100' => 'guotongkuaidi', 'kuaidibird' => ''], // 国通
        ['juhe' => 'jd', 'kuaidi100' => 'jd', 'kuaidibird' => 'JD'], // 京东
        ['juhe' => 'zjs', 'kuaidi100' => 'zhaijisong', 'kuaidibird' => 'ZJS'], // 宅急送
        ['juhe' => 'fedex', 'kuaidi100' => 'fedex', 'kuaidibird' => 'FEDEX_GJ'], // fedex
        ['juhe' => 'ups', 'kuaidi100' => '', 'kuaidibird' => 'UPS'], // ups
        ['juhe' => 'ztky', 'kuaidi100' => '', 'kuaidibird' => 'ZHWL'], // 中铁
        ['juhe' => 'jiaji', 'kuaidi100' => 'jiajiwuliu', 'kuaidibird' => 'CNEX'], // 佳吉
        ['juhe' => 'suer', 'kuaidi100' => 'suer', 'kuaidibird' => 'SURE'], // 速尔
        ['juhe' => 'xfwl', 'kuaidi100' => 'xinfengwuliu', 'kuaidibird' => 'XFEX'], // 信丰
        ['juhe' => 'yousu', 'kuaidi100' => 'youshuwuliu', 'kuaidibird' => 'UC'], // 优速
        ['juhe' => 'zhongyou', 'kuaidi100' => 'zhongyouwuliu', 'kuaidibird' => 'ZYKD'], // 中邮
        ['juhe' => 'tdhy', 'kuaidi100' => 'tiandihuayu', 'kuaidibird' => 'HOAU'], // 天地华宇
        ['juhe' => 'axd', 'kuaidi100' => 'anxindakuaixi', 'kuaidibird' => ''], // 安信达
        ['juhe' => 'kuaijie', 'kuaidi100' => 'kuaijiesudi', 'kuaidibird' => 'DJKJWL'], // 快捷
        ['juhe' => 'aae', 'kuaidi100' => 'aae', 'kuaidibird' => 'AAE'], // aae
        ['juhe' => 'dhl', 'kuaidi100' => 'dhl', 'kuaidibird' => 'FEDEX'], // dhl国内件
        ['juhe' => 'dhl', 'kuaidi100' => 'dhlen', 'kuaidibird' => 'FEDEX_GJ'], // dhl国际件
        ['juhe' => 'dpex', 'kuaidi100' => 'dpex', 'kuaidibird' => 'DPEX'], // dpex国际
        ['juhe' => 'ds', 'kuaidi100' => 'dsukuaidi', 'kuaidibird' => 'DSWL'], // d速
        ['juhe' => 'fedexcn', 'kuaidi100' => 'fedexcn', 'kuaidibird' => 'FEDEX'], // fedex国内
        ['juhe' => 'fedexcn', 'kuaidi100' => 'fedex', 'kuaidibird' => 'FEDEX_GJ'], // fedex国际
        ['juhe' => 'ocs', 'kuaidi100' => 'ocs', 'kuaidibird' => ''], // ocs
        ['juhe' => 'tnt', 'kuaidi100' => 'tnt', 'kuaidibird' => 'TNT'], // tnt
        ['juhe' => 'coe', 'kuaidi100' => 'coe', 'kuaidibird' => ''], // 中国东方
        ['juhe' => 'cxwl', 'kuaidi100' => 'chuanxiwuliu', 'kuaidibird' => 'CXHY'], // 传喜
        ['juhe' => 'cs', 'kuaidi100' => 'city100', 'kuaidibird' => 'CITY100'], // 城市100
        ['juhe' => 'cszx', 'kuaidi100' => '', 'kuaidibird' => ''], // 城市之星
        ['juhe' => 'aj', 'kuaidi100' => 'anjie88', 'kuaidibird' => 'AJ'], // 安捷
        ['juhe' => 'bfdf', 'kuaidi100' => 'baifudongfang', 'kuaidibird' => 'BFDF'], // 百福东方
        ['juhe' => 'chengguang', 'kuaidi100' => 'chengguangkuaidi', 'kuaidibird' => 'CG'], // 橙光
        ['juhe' => 'dsf', 'kuaidi100' => 'disifang', 'kuaidibird' => 'D4PX'], // 递四方
        ['juhe' => 'ctwl', 'kuaidi100' => '', 'kuaidibird' => ''], // 长通
        ['juhe' => 'feibao', 'kuaidi100' => 'feibaokuaidi', 'kuaidibird' => ''], // 飞豹
        ['juhe' => 'ane66', 'kuaidi100' => 'annengwuliu', 'kuaidibird' => 'ANE'], // 安能
        ['juhe' => 'youzheng', 'kuaidi100' => 'youzhengbk', 'kuaidibird' => 'YZPY'], // 远成
        ['juhe' => 'bsky', 'kuaidi100' => 'huitongkuaidi', 'kuaidibird' => 'HTKY'], // 百世
        ['juhe' => 'suning', 'kuaidi100' => 'suning', 'kuaidibird' => 'SNWL'], // 苏宁
        ['juhe' => 'jiuye', 'kuaidi100' => 'jiuyescm', 'kuaidibird' => 'JIUYE'], // 九曳
        ['juhe' => '', 'kuaidi100' => '', 'kuaidibird' => 'AMAZON'], // 亚马逊
        ['juhe' => '', 'kuaidi100' => '', 'kuaibird' => 'HQSY'], // 环球速运
    ];

    /**
     * 通过快递100接口自动获取快递公司编码
     *
     * @param string $channel 指定渠道
     * @param string $code 指定单号
     * @param Client $http_client
     * @return string
     * @throws InvalidArgumentException
     */
    public static function getCode($channel, $code, Client $http_client)
    {
        $url = 'http://m.kuaidi100.com/autonumber/autoComNum';
        $params = ['resultv2' => 1, 'text' => $code];

        try {
            $companyCodeInfo = $http_client->get($url, ['query' => $params]);
            $res = json_decode($companyCodeInfo->getBody()->getContents());

        } catch (\Exception $exception) {
            throw new InvalidArgumentException($exception->getMessage(), $exception->getCode(), $exception);
        }

        $companyCode = '';
        $channelName = strtolower($channel);
        if (!in_array($channelName, ['juhe', 'kuaidi100', 'kuaidibird'])) {
            return '';
        }

        if (isset($res->auto)) {
            $kuaidi100CompanyCodeArr = array_column($res->auto, 'comCode');
            $kuaidi100CompanyCode = isset($kuaidi100CompanyCodeArr[0]) ? $kuaidi100CompanyCodeArr[0] : '';

            if (!isset($kuaidi100CompanyCode)) return '';

            foreach (self::$companyList as $name => $item) {
                if (isset($kuaidi100CompanyCode) && $item['kuaidi100'] === $kuaidi100CompanyCode) {
                    $companyCode = $item[$channelName];
                }
            }
        }

        return $companyCode;
    }
}
