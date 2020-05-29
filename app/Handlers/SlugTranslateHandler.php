<?php

namespace App\Handlers;

use GuzzleHttp\Client;
use Overtrue\Pingyin\Pingyin;

class SlugTranslateHandler
{
    public function translate($text)
    {
        // 配置信息
        $api = 'http://api.fanyi.baidu.com/api/trans/vip/translate?';
        $appid = config('services.baidu_translate.appid');
        $key = config('services.baidu_translate.key');
        $salt = time();

        // 如果没有配置百度翻译，就使用拼音方案
        if (empty($appid) || empty($key)) {
            return $this->pinyin($text);
        }

        // 实例化 Http 客户端
        $http = new Client;

        // 拼接 sign 字段
        $sign = md5($appid . $text . $salt . $key);

        // 查询字符串
        $query = http_build_query([
            'q' => $text,
            'from' => 'zh',
            'to' => 'en',
            'appid' => $appid,
            'salt' => $salt,
            'sign' => $sign,
        ]);

        // 发送请求
        $response = $http->get($api . $query);
        $res = json_decode($response->getBody(), true);

        // 尝试获取翻译结果
        if (isset($res['trans_result'][0]['dst'])) {
            return \Str::slug($res['trans_result'][0]['dst']);
        } else {
            // 如果翻译没有结果，则使用拼音后备
            return $this->pinyin($text);
        }
    }

    public function pinyin($text)
    {
        return \Str::slug(app(Pinyin::class)->permalink($text));
    }
}
