<?php

namespace Kang\Libs\Baidu;

use Kang\Libs\Helper\Curl;
use Kang\Libs\Helper\HelperFunc;

class BaiduSms extends Baidu{
    const API_BASE_URL = 'smsv3.bj.baidubce.com/'; //基础路由端口

    public function signatureApply(string $content, $contentType, $countryType = 'DOMESTIC', $description = '', $signatureFilePath = ''){


        return $this->post('')
    }
    public function post($url, $data = [], $autoToken = false){
        $headers['Authorization'] = $this->getAccessToken();
        $headers['host'] = HelperFunc::getServerIp();
        $headers['Content-Type'] = 'application/json; charset=utf-8';
        $headers['x-bce-date'] = date('Y-m-d');

        return parent::request($url, Curl::METHOD_POST, false, $data, $headers);
    }
}