<?php

/*
 * This file is part of the daley/logistics.
 *
 * (c) daley <poicue@163.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Daley\Logistics\Channel;

use Daley\Logistics\Exceptions\HttpException;

/**
 * 快递鸟查询物流接口.
 */
class BaiDuChannel extends Channel
{
    /**
     * 接口地址
     *
     * @var string
     */
    protected $url = 'https://express.baidu.com/express/api/express';

    /**
     * 查询物流
     *
     * @param string $code
     * @param string $company
     *
     * @return array
     *
     * @throws HttpException
     */
    public function query($code, $company = '')
    {
        $tokenV2 = $this->getTokenV2();

        try {
            $response = $this->httpClient->get($this->url, ['query' => [
                'tokenV2' => $tokenV2,
                'appid' => 4001,
                'nu' => $code,
            ]]);
        } catch (\Exception $e) {
            throw new HttpException($e->getMessage(), $e->getCode(), $e);
        }

        $this->toArray($response->getBody()->getContents());
        $this->format();

        return $this->response;
    }

    /**
     * 获取TokenV2.
     *
     * @return mixed|null
     */
    protected function getTokenV2()
    {
        $tokenUrl = 'https://www.baidu.com/baidu?isource=infinity&iname=baidu&itype=web&tn=02003390_42_hao_pg&ie=utf-8&wd=%E5%BF%AB%E9%80%92';

        try {
            $response = $this->httpClient->get($tokenUrl);

            preg_match('/tokenV2=(.*?)"/i', $response->getBody()->getContents(), $match);
            if (!empty($match[1])) {
                return $match[1];
            }
        } catch (\Exception $e) {
            return null;
        }

        return null;
    }

    /**
     * 格式化.
     */
    protected function format()
    {
        if (!empty($this->response['data'])) {
            $formatData = [];
            foreach ($this->response['data'] as $datum) {
                $formatData[] = ['time' => date('Y-m-d H:i:s', $datum['time']), 'description' => $datum['desc']];
            }
            $this->response['data'] = $formatData;
        }
    }

    /**
     * 转换为数组.
     *
     * @param array|string $response
     */
    public function toArray($response)
    {
        $jsonToArray = json_decode($response, true);

        if (!isset($jsonToArray['status']) || 0 != $jsonToArray['status']) {
            $this->response = [
                'status' => 0,
                'message' => isset($jsonToArray['msg']) ? $jsonToArray['msg'] : '物流信息查询失败',
                'error_code' => isset($jsonToArray['error_code']) ? $jsonToArray['error_code'] : 0,
                'data' => [],
                'logistics_company' => '',
            ];
        } else {
            $this->response = [
                'status' => 1,
                'message' => $jsonToArray['msg'],
                'error_code' => $jsonToArray['error_code'],
                'data' => isset($jsonToArray['data']['info']['context']) ? $jsonToArray['data']['info']['context'] : [],
                'logistics_company' => isset($jsonToArray['data']['com']) ? $jsonToArray['data']['com'] : '',
            ];
        }
    }
}
