<?php

namespace Kang\Libs\Baidu\Behavior;

use Kang\Libs\Baidu\Baidu;
use Kang\Libs\Base\Behavior;
use Kang\Libs\Base\Event;
use Kang\Libs\Helper\FileCache;

class BaiduBehavior extends Behavior {
    public function events(){
        return [
            Baidu::EVENT_ACCESS_TOKEN_ERROR => [$this, 'errorAccessToken'],
            Baidu::EVENT_BEFORE_REFRESH_ACCESS_TOKEN => [$this, 'beforeRefreshAccessToken'],
            Baidu::EVENT_AFTER_REFRESH_ACCESS_TOKEN => [$this, 'afterRefreshAccessToken'],
            Baidu::EVENT_REFRESH_JS_API_TICKET => [$this, 'refreshJsapiTicket'],
            Baidu::EVENT_ACCESS_TOKEN_CACHE_GET => [$this, 'accessTokenGetCache'],
            Baidu::EVENT_lOG => [$this, 'log'],
        ];
    }
    /**
     * @var 监听 AccessToken 42001
     * @param Event $event
     */
    public function errorAccessToken(Event $event){
        $wechat = $event->sender;
        FileCache::getInstall()->set($this->getCacheKey($event->sender), null);
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
            $event->data = FileCache::getInstall($this->getLogsPath())
                ->set($this->getCacheKey($event->sender), $event->data['access_token'], 30 * 24 * 60 * 60);
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
        $event->data = FileCache::getInstall($this->getLogsPath())
            ->get($this->getCacheKey($event->sender));
    }

    private function getLogsPath(){
        return __DIR__ . DIRECTORY_SEPARATOR . 'logs' . DIRECTORY_SEPARATOR;
    }

    private function getCacheKey(Baidu $baidu){
        return $baidu->appid . $baidu->appkey . $baidu->secret;
    }
}