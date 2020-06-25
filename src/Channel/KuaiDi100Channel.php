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
use Daley\Logistics\Util\ChannelCode;

class KuaiDi100Channel extends Channel
{
    /**
     * 接口地址
     *
     * @var string
     */
    protected $url = 'http://poll.kuaidi100.com/poll/query.do';

    /**
     * 调用查询接口.
     *
     * @param string $code    快递单号
     * @param string $company 物流公司编码 留空自动获取
     *
     * @return array
     *
     * @throws HttpException
     * @throws \Daley\Logistics\Exceptions\InvalidArgumentException
     */
    public function query($code, $company = '')
    {
        if (empty($company)) {
            $company = ChannelCode::getCode($this->getClassName(), $code, $this->httpClient);
        }

        $postJson = json_encode([
            'num' => $code,
            'com' => $company,
        ]);
        $config = $this->getConfig();
        $params = [
            'customer' => $config['app_secret'],
            'sign' => strtoupper(md5($postJson.$config['app_key'].$config['app_secret'])),
            'param' => $postJson,
        ];

        try {
            $response = $this->httpClient->post($this->url, ['form_params' => $params])->getBody()->getContents();
        } catch (\Exception $exception) {
            throw new HttpException($exception->getMessage(), $exception->getCode(), $exception);
        }

        $this->toArray($response);
        $this->format();

        return $this->response;
    }

    /**
     * 转换为数组.
     *
     * @param string|array $response
     */
    protected function toArray($response)
    {
        $array = json_decode($response, true);
        if (!isset($array['state'])) {
            $this->response = [
                'status' => 0,
                'message' => isset($array['message']) ? $array['message'] : 'Cannot find logistics information',
                'error_code' => 0,
                'data' => [],
                'logistics_company' => '',
            ];
        } else {
            $this->response = [
                'status' => 1,
                'message' => 'ok',
                'error_code' => 0,
                'data' => $array['data'],
                'logistics_company' => $array['com'],
            ];
        }
    }

    /**
     * 统一返回格式.
     *
     * @return array
     */
    protected function format()
    {
        if (!empty($this->response['data'])) {
            $formatData = [];
            foreach ($this->response['data'] as $datum) {
                $formatData[] = ['time' => $datum['ftime'], 'description' => $datum['context']];
            }
            $this->response['data'] = $formatData;
        }

        return $this->response;
    }
}
