<?php

namespace Kang\Libs\WeChat\Library;
/**
 * 二维码管理
 * Trait Qrcode
 * @package Kang\Libs\WeChat\Library
 */
trait Qrcode{
    //------------------------------二维码--------------------------------//
    /**
     * @var 获取二维码
     * @param bool $actionName 二维码类型，QR_SCENE为临时的整型参数值，QR_STR_SCENE为临时的字符串参数值，QR_LIMIT_SCENE为永久的整型参数值，QR_LIMIT_STR_SCENE为永久的字符串参数值
     * @param $scene 二维码场景值
     * @param int $expireTime 临时二维码时长
     * @return bool|{"ticket":"凭借此 ticket 可以在有效时间内换取二维码","expire_seconds":60,"url":"该二维码有效时间，开发者可根据该地址自行生成需要的二维码图片"}
     */
    public function qrcodeCreate($actionName, $scene = null, $expireTime = 2592000){
        $data['action_name'] = $actionName;
        switch($actionName){
            case self::QR_SCENE : //为临时的整型参数值
                $data['action_info']['scene']['scene_id'] = intval($scene);
                $data['expire_seconds'] = $expireTime;
                break;
            case self::QR_STR_SCENE: //临时的字符串参数值
                $data['action_info']['scene']['scene_str'] = $scene;
                $data['expire_seconds'] = $expireTime;
                break;
            case self::QR_LIMIT_SCENE ://为永久的整型参数值
                $data['action_info']['scene']['scene_id'] = intval($scene);
                break;
            case self::QR_LIMIT_STR_SCENE ://为永久的字符串参数值
                $data['action_info']['scene']['scene_str'] = $scene;
                break;
            default:
                return $this->setErrors(-3, '二维码类型错误!');
        }

        return $this->httpPost(Urls::QRCODE_CREATE, $data, true);
    }
    /**
     * @var 获取二维码图片
     * @param string $ticket 传入由getQRCode方法生成的ticket参数
     * @return string url 返回https地址
     */
    public function qrcodeUrl($ticket) {
        return Urls::QRCODE_SHOW . urlencode($ticket);
    }
}
