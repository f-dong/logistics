<h1 align="center"> logistics </h1>


<p align="center"> 一个简单便捷查询运单快递信息的SDK. </p>

[![Build Status](https://travis-ci.org/f-dong/logistics.svg?branch=master)](https://travis-ci.org/f-dong/logistics)
[![StyleCI build status](https://github.styleci.io/repos/274927704/shield)](https://github.styleci.io/repos/274927704)
[![Latest Stable Version](https://poser.pugx.org/daley/logistics/v)](//packagist.org/packages/daley/logistics) 
[![Total Downloads](https://poser.pugx.org/daley/logistics/downloads)](//packagist.org/packages/daley/logistics) 
[![Latest Unstable Version](https://poser.pugx.org/daley/logistics/v/unstable)](//packagist.org/packages/daley/logistics) 
[![License](https://poser.pugx.org/daley/logistics/license)](//packagist.org/packages/daley/logistics)

## 支持查询接口平台

| 平台 | 调用方式 | 是否需要快递公司编码 |
| :-----: | :-----: | :-----: |
| [快递100](https://www.kuaidi100.com/openapi/applyapi.shtml) | kuaidi100 | Y |
| [快递鸟](http://www.kdniao.com/api-all) | kuaidibird | Y |
| [急速数据](https://www.jisuapi.com/api/express) | jisu | N |
| [爱查快递](https://www.ickd.cn/api) | ickd | N |

* 爱查快递为抓取接口，无法保证数据准确性与稳定性

## 环境需求
*   PHP >= 5.6

## 安装

```shell
$ composer require daley/logistics -vvv
```

## 使用

```php
use Daley\Logistics\Logistics;

$logistics = new Logistics([
    // 快递100配置
    'kuaidi100' => [
        'app_key' => '',
        'app_secret' => '',
    ],
    // 快递鸟配置
    'kuaidibird' => [
        'app_key' => '', // 用户ID
        'app_secret' => '', // API key
        'vip' => false, // 是否付费用户
    ],
    // 急速快递配置
    'jisu' => [
        'app_key' => '4280d81691e86974'
    ],
]);

// 查询物流
try {
    var_dump($log->query('73129084446868', 'zhongtong'));
} catch (\Daley\Logistics\Exceptions\HttpException $exception) {
    // HTTP请求异常
} catch (\Daley\Logistics\Exceptions\InvalidArgumentException $exception) {
    // 参数异常
} catch (\Daley\Logistics\Exceptions\NoAvailableException $exception) {
    // 没有成功数据
} catch (Exception $exception) {
    // 其他异常
}
```
### 参数说明
```php
array query(string $code [, mixed $company = null])
```
* $code - 运单号
* $company - 快递公司编码 参考各渠道提供的渠道列表 不填为自动抓取 不保证准确性 多公司时使用数组 如 `['kuaidi100' => 'zhongtong', 'kuaidibird' => 'ZTO']`

### 更换查询渠道
```php
// 不设置默认使用快递100
$logistics->setChannel('kuaidibird')->query('73129084446868');

// 查询多渠道
$logistics->setChannel(['kuaidi100', 'kuaidibird'])->query('73129084446868');
```

## 返回事例

```php
//  成功返回
[
   'kuaidi100' => [
       'channel' => 'kuaidi100',
       'status' => 'success',
       'result' => [
           [
               'status' => 1,
               'message'  => 'ok',
               'error_code' => 0,
               'data' => [
                   ['time' => '2020-06-25 00:00:00', 'description' => '仓库-已签收'],
                   ['time' => '2020-06-25 00:00:00', 'description' => '广东XX服务点'],
                   ['time' => '2020-06-25 00:00:00', 'description' => '广东XX转运中心'],
               ],
               'logistics_company' => 'zhongtong',
           ],
       ]
   ]
]

// 失败返回
[
   'kuaidi100' => [
       'channel' => 'kuaidi100',
       'status' => 'failure',
       'exception' => '数据不完整',
   ],
]
```
* 所有渠道返回格式均一致

## Laravel 中使用
* 发布配置
```shell
$ php artisan vendor:publish --provider=Daley\Logistics\Laravel\LogisticsServiceProvider --tag=logistics
```
* 随后，请在`config`文件夹中完善配置信息。
* 方法参数注入
```php
public function edit(Logistics $logistics) 
{
    $response = $logistics->setChannel('kuaidi100')->query('73129084446868', 'zhongtong');
}
```
* 服务名访问
```php
public function edit() 
{
    $response = app('logistics')->setChannel('kuaidi100')->query('73129084446868', 'zhongtong');
}
```

## License

MIT