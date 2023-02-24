<?php
namespace Kang\Libs\WeChat;

use Kang\Libs\Base\Event;
use Kang\Libs\WeChat\Library\Comment;
use Kang\Libs\WeChat\Library\Draft;
use Kang\Libs\WeChat\Library\Media;
use Kang\Libs\WeChat\Library\Menu;
use Kang\Libs\WeChat\Library\MessageFK;
use Kang\Libs\WeChat\Library\MessageMass;
use Kang\Libs\WeChat\Library\MessageServer;
use Kang\Libs\WeChat\Library\MessageTemplate;
use Kang\Libs\WeChat\Library\Ocr;
use Kang\Libs\WeChat\Library\OpenApi;
use Kang\Libs\WeChat\Library\Qrcode;
use Kang\Libs\WeChat\Library\Tag;
use Kang\Libs\WeChat\Library\Urls;
use Kang\Libs\WeChat\Library\User;

/**
 * 微信公众号开发
 * $wechat = new WeChat(['appid' => '', 'appsecret' => '']);
 * Class WeChat
 * @property string $appid  微信 appid
 * @property string $appsecret  微信 appsecret
 * @property string $token  微信消息的token
 * @property string $accessToken  微信公众号的accessToken
 * @property string $encodingAESKey  微信的消息加密密匙
 * @property string $mch_id  微信支付商户ID
 * @property string $mch_key  微信支付商户KEY
 * @property string $sslCertPath  证书的相对路径 或者绝对路径
 * @property string $sslKeyPath; 证书的相对路径 或者绝对路径
 * @package Kang\Libs\WeChat
 */
class WeChat extends WeChatTrait {
    use Comment;
    use Draft;
    use Media;
    use Menu;
    use MessageFK;
    use MessageMass;
    use MessageServer;
    use MessageTemplate;
    use Ocr;
    use OpenApi;
    use Qrcode;
    use Tag;
    use User;

    /**
     * 获取AccessToken
     * @return mixed
     */
    public function getAccessToken(){
        if(empty($this->_config['accessToken'])){
            $event = new Event();
            $this->trigger(self::EVENT_ACCESS_TOKEN_CACHE_GET, $event);
            $this->_config['accessToken'] = empty($event->data['accessToken']) ? $this->refreshAccessToken() : $event->data['accessToken'];
        }

        return $this->_config['accessToken'];
    }

    /**
     * 刷新AccessToken
     * @return bool|mixed
     */
    public function refreshAccessToken(){
        $event = new Event();
        $event->sender = $this;
        $this->trigger(self::EVENT_BEFORE_REFRESH_ACCESS_TOKEN, $event);
        if($event->handled === true){
            return $event->data['access_token'] ?? $event->data;
        }

        $url    = Urls::ACCESS_TOKEN_URL;
        $url   .= 'appid=' . $this->appid;
        $url   .= '&secret=' . $this->appsecret;
        $url   .= '&grant_type=client_credential';
        $result = $this->httpGet($url);
        if(!$result || !isset($result['access_token'])){
            return false;
        }

        $event->data = $result;
        $this->trigger(self::EVENT_AFTER_REFRESH_ACCESS_TOKEN, $event);
        return $result['access_token'];
    }

    /**
     * @param $accessToken
     * @return $this
     */
    public function setAccessToken($accessToken){
        $this->_config['accessToken'] = $accessToken;
        return $this;
    }
}
