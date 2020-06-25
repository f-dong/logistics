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

class IckdChannel extends Channel
{
    /**
     * 请求地址
     *
     * @var string
     */
    protected $url = 'https://biz.trace.ickd.cn/auto/';

    /**
     * 生成随机码
     *
     * @return string
     */
    private function randCode()
    {
        $str = '0123456789abcdefghijklmnopqrstuvwxyz';
        $code = '';
        for ($i = 0; $i < 5; ++$i) {
            $index = mt_rand(0, strlen($str) - 1);
            $code .= $str[$index];
        }

        return $code;
    }

    /**
     * 发起请求
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
        $urlParams = [
            'mailNo' => $code,
            'spellName' => '',
            'exp-textName' => '',
            'tk' => $this->randCode(),
            'tm' => time() - 1,
            'callback' => '_jqjsp',
            '_'.time(),
        ];

        try {
            $res = $this->httpClient->get($this->url.$code, ['query' => $urlParams, 'headers' => ['referer: https://biz.trace.ickd.cn']]);

            $this->toArray($res->getBody()->getContents());
        } catch (\Exception $e) {
            throw new HttpException($e->getMessage(), $e->getCode(), $e);
        }

        $this->format();

        return $this->response;
    }

    /**
     * 转为数组.
     *
     * @param array|string $response
     */
    protected function toArray($response)
    {
        $pattern = '/(\_jqjsp\()({.*})\)/i';
        if (preg_match($pattern, $response, $match)) {
            $response = json_decode($match[2], true);
            $this->response = [
                'status' => 3 == $response['status'] ? 1 : 0,
                'message' => $response['message'],
                'error_code' => isset($response['errCode']) ? $response['errCode'] : '',
                'data' => isset($response['data']) ? $response['data'] : '',
                'logistics_company' => isset($response['expTextName']) ? $response['expTextName'] : '',
                'logistics_bill_no' => $response['mailNo'],
            ];
        } else {
            $this->response = [
                'status' => 0,
                'message' => '查询不到数据',
                'error_code' => 0,
                'data' => '',
                'logistics_company' => '',
            ];
        }
    }

    /**
     * 统一物流信息.
     *
     * @return mixed|void
     */
    protected function format()
    {
        if (!empty($this->response['data']) && is_array($this->response['data'])) {
            $formatData = [];
            foreach ($this->response['data'] as $datum) {
                $formatData[] = ['time' => $datum['time'], 'description' => $datum['context']];
            }
            $this->response['data'] = $formatData;
        }
    }
}
