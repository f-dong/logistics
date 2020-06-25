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
use Daley\Logistics\Util\ChannelCode;
use Daley\Logistics\Util\UserAgent;

/**
 * 快递鸟查询物流接口.
 */
class KuaiDiBirdChannel extends Channel
{

    /**
     * 增值请求.
     *
     * @var int
     */
    const PAYED = 8001;

    /**
     * 免费请求.
     *
     * @var int
     */
    const FREE = 1002;

    /**
     * 接口地址
     *
     * @var string
     */
    protected $url = 'http://api.kdniao.com/Ebusiness/EbusinessOrderHandle.aspx';

    /**
     * 拼接请求URL链接.
     *
     * @param string $data 请求的数据
     * @return array
     * @throws \Daley\Logistics\Exceptions\InvalidArgumentException
     */
    public function setRequestParam($data)
    {
        $config = $this->getConfig();

        return [
            'EBusinessID' => isset($config['app_key']) ? $config['app_key'] : '',
            'DataType'    => 2,
            'RequestType' => isset($config['vip']) && $config['vip'] ? self::PAYED : self::FREE,
            'RequestData' => urlencode($data),
            'DataSign'    => $this->encrypt($data, isset($config['app_secret']) ? $config['app_secret'] : ''),
        ];
    }

    /**
     * 编码
     *
     * @param string $data
     * @param string $appKey
     *
     * @return string
     */
    protected function encrypt($data, $appKey)
    {
        return urlencode(base64_encode(md5($data . $appKey)));
    }

    /**
     * 发起查询请求
     *
     * @param string $code
     * @param string $company
     * @return array
     * @throws HttpException
     * @throws \Daley\Logistics\Exceptions\InvalidArgumentException
     */
    public function query($code, $company = '')
    {
        if (empty($company)) {
            $company = ChannelCode::getCode($this->getClassName(), $code, $this->httpClient);
        }

        $requestData = $this->setRequestParam(json_encode(['OrderCode' => '', 'ShipperCode' => $company, 'LogisticCode' => $code]));

        try {
            $response = $this->httpClient->post($this->url, ['form_params' => $requestData, 'headers' => ['charset' => 'utf-8']]);

            $this->toArray($response->getBody()->getContents());

        } catch (\Exception $e) {
            throw new HttpException($e->getMessage(), $e->getCode(), $e);
        }

        $this->format();

        return $this->response;
    }

    /**
     * 格式化.
     */
    protected function format()
    {
        if (!empty($this->response['data'])) {
            $formatData = [];
            foreach ($this->response['data'] as $datum) {
                $formatData[] = ['time' => str_replace('/', '-', $datum['AcceptTime']), 'description' => $datum['AcceptStation']];
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

        if (!isset($jsonToArray['Success']) || !$jsonToArray['Success']) {
            $this->response = [
                'status' => 0,
                'message' => isset($jsonToArray['Reason']) ? $jsonToArray['Reason'] : '物流信息查询失败',
                'error_code' => 0,
                'data' => [],
                'logistics_company' => '',
            ];
        } else {
            $this->response = [
                'status' => 0,
                'message' => $jsonToArray['Reason'],
                'error_code' => $jsonToArray['State'],
                'data' => [],
                'logistics_company' => '',
            ];
        }
    }
}