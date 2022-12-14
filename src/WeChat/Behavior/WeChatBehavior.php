<?php

namespace Kang\Libs\WeChat\Behavior;

use Kang\Libs\Base\Behavior;
use Kang\Libs\Base\Event;
use Kang\Libs\Helper\FileCache;
use Kang\Libs\WeChat\WeChat;

class WeChatBehavior extends Behavior {
    public function events(){
        return [
            WeChat::EVENT_ACCESS_TOKEN_ERROR => [$this, 'errorAccessToken'],
            WeChat::EVENT_BEFORE_REFRESH_ACCESS_TOKEN => [$this, 'beforeRefreshAccessToken'],
            WeChat::EVENT_AFTER_REFRESH_ACCESS_TOKEN => [$this, 'afterRefreshAccessToken'],
            WeChat::EVENT_REFRESH_JS_API_TICKET => [$this, 'refreshJsapiTicket'],
            WeChat::EVENT_REFRESH_COMPONENT_ACCESS_TOKEN => [$this, 'componentAccessToken'],
            WeChat::EVENT_ACCESS_TOKEN_CACHE_GET => [$this, 'accessTokenGetCache'],
            WeChat::EVENT_lOG => [$this, 'log'],
        ];
    }
    /**
     * @var 监听 AccessToken 42001
     * @param Event $event
     */
    public function errorAccessToken(Event $event){
        $wechat = $event->sender;
        FileCache::getInstall()->set($wechat->appid . $wechat->appsecret, null);
    }
    /**
     * @var 监听AccessToken更新之前事件
     * @param Event $event
     */
    public function beforeRefreshAccessToken(Event $event){

    }
    /**
     * @var 监听AccessToken更新之后事件
     * @param Event $event
     */
    public function afterRefreshAccessToken(Event $event){
        if(!empty($event->data['access_token'])){
            $wechat = $event->sender;
            $event->data = FileCache::getInstall($this->getLogsPath())->set($wechat->appid . $wechat->appsecret, $event->data['access_token'], 7150);
        }
    }
    /**
     * @var 监听 JsapiTicket 的刷新事件
     * @param Event $event
     */
    public function refreshJsapiTicket(Event $event){

    }
    /**
     * @var 监听第 三方平台的 AccessToken 的刷新事件
     * @param Event $event
     */
    public function componentAccessToken(Event $event){

    }
    /**
     * @var 日志事件
     * @param Event $event
     */
    public function log(Event $event){

    }

    public function accessTokenGetCache(Event $event){
        $wechat = $event->sender;
        $event->data = FileCache::getInstall($this->getLogsPath())->get($wechat->appid . $wechat->appsecret);
    }

    private function getLogsPath(){
        return __DIR__ . DIRECTORY_SEPARATOR . 'logs' . DIRECTORY_SEPARATOR;
    }
}