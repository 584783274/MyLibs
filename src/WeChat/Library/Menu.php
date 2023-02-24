<?php

namespace Kang\Libs\WeChat\Library;
/**
 *  自定义菜单
 * Trait TraitMenu
 * @package Kang\Libs\WeChat\Library
 */
trait Menu{
    /**
     * @var 创建菜单
     * @param array $button 菜单按钮 [['name' => '菜单名称','sub_button' => [子菜单],
    'type' => '菜单的响应动作类型，view表示网页类型，click表示点击类型，miniprogram表示小程序类型',
    'key' => 'click等点击类型必须	菜单 KEY 值，用于消息接口推送，不超过128字节',
    'url' => 'view、miniprogram类型必须	网页 链接，用户点击菜单可打开链接，不超过1024字节。 type为 miniprogram 时，不支持小程序的老版本客户端将打开本url',
    'media_id' => 'media_id类型和view_limited类型必须,调用新增永久素材接口返回的合法media_id',
    'appid' => 'miniprogram类型必须	小程序的appid（仅认证公众号可配置）',
    'pagepath' => 'miniprogram类型必须	小程序的页面路径',
    'article_id' => 'article_id类型和article_view_limited类型必须	发布后获得的合法 article_id',
    ]]
     * @param array $matchrule 个性化菜单匹配规则--"tag_id": 用户标签的id ,"client_platform_type": 客户端版本,当前只具体到系统型号：IOS(1), Android(2),Others(3)
     * @return bool
     */
    public function menuCreate(array $button, $matchrule = []){
        $menu['button'] = $button;
        $url = Urls::MENU_BASE_CREATE;
        if(!empty($matchrule)){
            $menu['matchrule'] = $matchrule;
            $url = Urls::MENU_CONDITIONAL_CREATE;
        }

        if(!$result = $this->httpPost($url, $menu, true)){
            return false;
        }

        return true;
    }
    /**
     * @var 菜单查询
     * @return bool|[is_menu_open=> 菜单是否开启，0代表未开启，1代表开启, 'selfmenu_info' => []//菜单信息]
     */
    public function menuGet(){
        return $this->httpGet( Urls::MENU_BASE_GET, null, true);
    }
    /**
     * @var 删除菜单
     * @param null $menuid 个体化菜单ID
     * @return bool|void
     */
    public function menuDel($menuid = null){
        if($menuid){
            $data = ['menuid' => $menuid];
            return $this->httpPost(Urls::MENU_CONDITIONAL_DEL, $data, true);
        }

        return $this->httpGet(Urls::MENU_BASE_DEL, null, true);
    }

    /**
     * @var 个性化菜单用户匹配结果
     * @param string $userId 可以是粉丝的OpenID，也可以是粉丝的微信号。
     * @return bool|array
     */
    public function menuMatch($userId){
        $data['user_id'] = $userId;
        if(!$result =  $this->httpPost(Urls::MENU_CONDITIONAL_MATCH, $data, true)){
            return false;
        }

        return $result['button'];
    }
}
