<?php

namespace Kang\Libs\WeChat\Library;

trait Qrcode{
    //------------------------------二维码--------------------------------//
    /**
     * @var 获取二维码
     * @param bool $actionName 二维码类型， QR_SCENE QR_STR_SCENE QR_LIMIT_SCENE QR_LIMIT_STR_SCENE
     * QR_SCENE为临时的整型参数值，QR_STR_SCENE为临时的字符串参数值，
     * QR_LIMIT_SCENE为永久的整型参数值，QR_LIMIT_STR_SCENE为永久的字符串参数值
     * @param $scene 二维码场景值
     * @param int $expireTime 临时二维码时长
     */
    public function qrcodeByCreate($actionName, $scene = null, $expireTime = 2592000){
        $data['action_name'] = $actionName;
        switch($actionName){
            case self::QR_SCENE :
                $data['action_info']['scene']['scene_id'] = intval($scene);
                $data['expire_seconds'] = $expireTime;
                break;
            case self::QR_STR_SCENE:
                $data['action_info']['scene']['scene_str'] = $scene;
                $data['expire_seconds'] = $expireTime;
                break;
            case self::QR_LIMIT_SCENE :
                $data['action_info']['scene']['scene_id'] = intval($scene);
                break;
            case self::QR_LIMIT_STR_SCENE :
                $data['action_info']['scene']['scene_str'] = $scene;
                break;
            default:
                return $this->setErrors(-3, '二维码类型错误!');
        }

        return $this->httpPost(self::API_QRCODE_URL, $data, true);
    }
    /**
     * @var 获取二维码图片
     * @param string $ticket 传入由getQRCode方法生成的ticket参数
     * @return string url 返回https地址
     */
    public function qrcodeUrlByShow($ticket) {
        return self::API_QRCODE_IMG_URL . urlencode($ticket);
    }
    //------------------------------二维码--------------------------------//
}
