<?php

namespace Kang\Libs\WeChat\Library;

/**
 * 用户管理
 * Trait User
 * @package Kang\Libs\WeChat\Library
 */
trait User{
    /**
     * @var 代公众号授权
     * @param string $redirectUrl 回调地址
     * @param bool $isReturn 是否返回跳转地址
     * @param string $state 自定义携带参数
     * @param string $scope 授权作用域 snsapi_base寂寞授权 snsapi_userinfo 用户授权
     * @param bool $forcePopup 强制此次授权需要用户弹窗确认；默认为false；需要注意的是，若用户命中了特殊场景下的静默授权逻辑，则此参数不生效
     * @return array 用户信息
     */
    public function userOAuth2($redirectUrl = '', $state = 'STATE', $isReturn = true, $scope = 'snsapi_userinfo ', $forcePopup = true){
        $url = Urls::USER_OAUTH2;
        $url .= 'appid=' . $this->appid;
        $url .= '&redirect_uri=' . urlencode($this->getRedirectUrl($redirectUrl));
        $url .= '&response_type=code';
        $url .= '&scope=' . $scope;
        $url .= '&state=' . $state;
        $url .= '&forcePopup=' . $forcePopup;
        $url .= '#wechat_redirect';
        if ($isReturn) {
            return $url;
        }

        header('location:' . $url);
        exit;
    }
    /**
     * @var 获取用户授权的信息
     * @param string $lang 语言
     * @param bool $isUnionId 是否通过UnionId记者获取
     * @return bool|void
     */
    public function userOAuth2GetInfo($isUnionId = false, $lang = 'zh_CN'){
        if (!$userAccessToken = $this->userOAuth2UserGetUserAccessToken()) {
            return false;
        }

        if($isUnionId && $info = $this->userInfo($userAccessToken['openid'])){
            if($info['subscribe']){
                return $info;
            }
        }

        $url = Urls::USER_OAUTH2_INFO . $userAccessToken['access_token'];
        $url .= '&openid=' . $userAccessToken['openid'];
        $url .= '&lang=' . $lang;
        return $this->httpGet($url);
    }
    /**
     * 获取用户的AccessToken
     * @param string $code 用户授权得到的 $code;
     * @return ok ["access_token":"ACCESS_TOKEN","expires_in":7200,"refresh_token":"REFRESH_TOKEN","openid":"OPENID","scope":"SCOPE"]
     */
    public function userOAuth2UserGetUserAccessToken($code = null){
        $code = $code ? $code : $_GET['code'];
        $url = Urls::USER_OAUTH2_ACCESS_TOKEN;
        $url .= 'appid=' . $this->appid;
        $url .= '&secret=' . $this->appsecret;
        $url .= '&code=' . $code;
        $url .= '&grant_type=authorization_code';
        return $this->httpGet($url);
    }
    /**
     * @var 通过UnionID机制获取用户信息
     * @param string $openid
     * @param string $lang
     * @return bool|void
     */
    public function userInfo($openid, $lang = Urls::LANG_ZH_CN){
        $url = Urls::USER_UNIONN_ID_INFO;
        $url .= '&openid=' . $openid;
        $url .= '&lang=' . $lang;
        $url .= '&access_token=';

        return $this->httpGet($url, null, true);
    }

    /**
     * @param string $next_openid
     * @return false| 参数	说明
    total	关注该公众账号的总用户数
    count	拉取的 OPENID 个数，最大值为10000
    data	列表数据，OPENID的列表
    next_openid	拉取列表的最后一个用户的OPENID
     */
    public function userOpenidSelect($next_openid = ''){
        $url = Urls::USER_OPENID_SELECT;
        $url .= 'next_openid=' . $next_openid;
        $url .= '&access_token=';

        return $this->httpGet($url, null, true);
    }
    /**
     * @var 通过用户的openid获取基本信息
     * @param array $userList [ ['openid' => ''] ]
     * @return bool
     */
    public function userInfoSelect($userList){
        $data['user_list'] = $userList;
        if(!$result = $this->httpPost(Urls::USER_INFO_SELECT, null, true)){
            return false;
        }

        return $result['user_info_list'];
    }
    /**
     * @var 对用户进行备注
     * @param $openid
     * @param $remark
     * @return bool
     */
    public function userRemarkSet($openid, $remark){
        $data['openid'] = $openid;
        $data['remark'] = $remark;
        if(!$this->httpPost(Urls::USER_REMARK_SET, null, true)){
            return false;
        }

        return true;
    }

    /**
     * @var 获取公众号的黑名单列表
     * @param string $begin_openid
     * @return mixed|array {
        "total":23000,
        "count":1000,
        "data": {"
            openid":[
                "OPENID1",
                "OPENID2",
                ...,
                "OPENID1000"
                ]
        },
        "next_openid":"OPENID1000"
    }
     */
    public function userBlackSelect($begin_openid = ''){
        $data['begin_openid'] = $begin_openid;
        if(!$result = $this->httpGet(Urls::USER_BLACK_OPENID_SELECT, $data, true)){
            return false;
        }

        return $result;
    }
    /**
     * @var  拉黑用户
     * @param array $openids
     * @return bool
     */
    public function  userBlackSet(array $openids){
        $data['openid_list'] = $openids;

        if(!$this->httpPost(Urls::USER_BLACK_OPENID_SET, $data, true)){
            return false;
        }

        return true;
    }
    /**
     * @var  取消拉黑用户
     * @param array $openids
     * @return bool
     */
    public function  userBlackUnSet(array $openids){
        $data['openid_list'] = $openids;
        if(!$this->httpPost(Urls::USER_BLACK_OPENID_UN_SET, $data, true)){
            return false;
        }

        return true;
    }


}
