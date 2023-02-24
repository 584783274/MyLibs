<?php

namespace Kang\Libs\WeChat;
/**
 * 开放平台
 * $wechat = new Platform(['appid' => '', 'appsecret' => '']);
 * Class Platform
 * @property string $component_appid  第三方平台的appid
 * @property string $component_appsecret  第三方平台的appsecret
 * @property string $component_token  第三方平台的消息校验Token
 * @property string $component_encodingAESKey  第三方平台的消息加密密匙
 * @property string $component_access_token  第三方平台的AccessToken
 * @property string $component_verify_ticket  第三方平台的component_verify_ticket票据
 * @property string $authorizer_appid 授权方 appid
 * @property string $authorizer_access_token 授权方令牌
 * @property string $authorizer_refresh_token 授权方的刷新令牌
 * @package Kang\Libs\WeChat
 */
class Platform extends WeChatTrait{

}
