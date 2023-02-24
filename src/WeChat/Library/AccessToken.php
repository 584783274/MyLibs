<?php

namespace Kang\Libs\WeChat\Library;

use Kang\Libs\Base\Event;
/**
 * 微信公众号的AccessToken管理
 * Trait AccessToken
 * @package Kang\Libs\WeChat\Library
 */
trait AccessToken{
    /**
     * @var 获取AccessToken
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
     * @var 刷新AccessToken
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
