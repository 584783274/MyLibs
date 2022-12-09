<?php

namespace Kang\Libs\Baidu;

use Kang\Libs\Helper\Curl;
use Kang\Libs\Helper\HelperFunc;

class BaiduSms extends Baidu{
    const API_BASE_URL = 'smsv3.bj.baidubce.com/'; //基础路由端口
    /**
     * @var 申请签名
     * @param string $content 签名内容
     * @param $contentType Enterprise：企业 MobileApp：移动应用名称 Web：工信部备案的网站名称 WeChatPublic：微信公众号名称 Brand：商标名称 Else：其他
     * @param string $countryType 签名适用的国家类型 DOMESTIC：国内 INTERNATIONAL：国际/港澳台 GLOBAL：全球均适用
     * @param string $description 对于签名的描述
     * @param string $signatureFilePath
     * @return bool|mixed
     */
    public function signatureCreate(string $content, $contentType, $countryType = 'DOMESTIC', $description = '', $signatureFilePath = ''){
        $data['content'] = $content;
        $data['contentType'] = $contentType;
        $data['description'] = $description;
        $data['countryType'] = $countryType;
        return $this->post('/sms/v3/signatureApply?clientToken=', $data, false);
    }
    /**
     * @var 短信发生
     * @param string $mobile 手机号码,支持单个或多个手机号，多个手机号之间以英文逗号分隔，一次请求最多支持200个手机号。国际/港澳台号码请按照E.164规范表示，例如台湾手机号以+886开头，”+“不能省略。
     * @param string $template 短信模板ID，模板申请成功后自动创建，全局内唯一
     * @param string $signatureId 短信签名ID，签名表申请成功后自动创建，全局内唯一
     * @param array $contentVar 模板变量内容，用于替换短信模板中定义的变量
     * @param string $custom 用户自定义参数，格式为字符串，状态回调时会回传该值
     * @param $userExtId 通道自定义扩展码，上行回调时会回传该值，其格式为纯数字串。默认为不开通，请求时无需设置该参数。如需开通请联系SMS帮助申请
     * @return bool|mixed
     */
    public function send($mobile, $template, $signatureId, array $contentVar, $custom = '', $userExtId = ''){
        $data['mobile'] = $mobile;
        $data['template'] = $template;
        $data['signatureId'] = $signatureId;
        $data['contentVar'] = $contentVar;
        $data['userExtId'] = $userExtId;

        return $this->post('/api/v3/sendSms', $data, false);
    }

    public function post($url, $data = [], $autoToken = false){
        $headers['Authorization'] = $this->getAccessToken();
        $headers['host'] = HelperFunc::getServerIp();
        $headers['Content-Type'] = 'application/json; charset=utf-8';
        $headers['x-bce-date'] = date('Y-m-d');

        return parent::request($url, Curl::METHOD_POST, false, $data, $headers);
    }
}