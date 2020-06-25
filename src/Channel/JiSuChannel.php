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

use Daley\Logistics\Exceptions\HttpException;

/**
 * 极速数据物流查询.
 */
class JiSuChannel extends Channel
{
    protected $url = 'https://api.jisuapi.com/express/query';

    /**
     * 发起请求
     *
     * @param string $code
     * @param string $company
     *
     * @return array
     *
     * @throws HttpException
     * @throws \Daley\Logistics\Exceptions\InvalidArgumentException
     */
    public function query($code, $company = '')
    {
        $config = $this->getConfig();

        $params = ['type' => 'auto', 'number' => $code, 'appkey' => isset($config['app_key']) ? $config['app_key'] : ''];

        try {
            $res = $this->httpClient->get($this->url, ['query' => $params]);

            $this->toArray($res->getBody()->getContents());

            $this->format();
        } catch (\Exception $e) {
            throw new HttpException($e->getMessage(), $e->getCode(), $e);
        }

        return $this->response;
    }

    /**
     * 统一物流信息.
     */
    protected function format()
    {
        if (!empty($this->response['data'])) {
            $formatData = [];
            foreach ($this->response['data'] as $datum) {
                $formatData[] = ['time' => $datum['time'], 'description' => $datum['status']];
            }
            $this->response['data'] = $formatData;
        }
    }

    /**
     * 转为数组.
     *
     * @param array|string $response
     */
    protected function toArray($response)
    {
        $jsonToArray = json_decode($response, true);

        if (!isset($jsonToArray['status']) || 0 != $jsonToArray['status']) {
            $this->response = [
                'status' => 0,
                'message' => isset($jsonToArray['msg']) ? $jsonToArray['msg'] : '物流信息查询失败',
                'error_code' => isset($jsonToArray['status']) ? $jsonToArray['status'] : 0,
                'data' => [],
                'logistics_company' => '',
            ];
        } else {
            $this->response = [
                'status' => 1,
                'message' => 'ok',
                'error_code' => 0,
                'data' => $jsonToArray['result']['list'],
                'logistics_company' => '',
            ];
        }
    }
}
