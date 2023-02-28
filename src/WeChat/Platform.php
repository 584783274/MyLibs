<?php

namespace Kang\Libs\WeChat;

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
use Kang\Libs\WeChat\Library\User;

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

    //授权事件类型
    const AUTHOR_INFO_TYPE_COMPONENT_VERIFY_TICKET = 'component_verify_ticket';
    const AUTHOR_INFO_TYPE_AUTHORIZED = 'authorized'; //授权成功通知
    const AUTHOR_INFO_TYPE_UNAUTHORIZED = 'unauthorized'; //取消授权通知
    const AUTHOR_INFO_TYPE_UPDATEAUTHORIZED = 'updateauthorized'; //授权更新通知


}
