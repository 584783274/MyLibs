<?php

namespace Kang\Libs\WeChat\Library;

trait TraitUser{
    //------------------------------用户信息相关--------------------------------//
    /**
     * @var 代公众号授权
     * @param string $redirectUrl 回调地址
     * @param bool $isReturn 是否返回跳转地址
     * @param string $state 自定义携带参数
     * @param string $scope 授权作用域 snsapi_base寂寞授权 snsapi_userinfo 用户授权
     * @return array 用户信息
     */
    public function oAuth2($redirectUrl = '', $state = 'STATE', $isReturn = true, $scope = self::SCOPE_USER_INFO){
        $url = self::API_USER_OAUTH2;
        $url .= 'appid=' . $this->appid;
        $url .= '&redirect_uri=' . urlencode(HelpFunc::getRedirectUrl($redirectUrl));
        $url .= '&response_type=code';
        $url .= '&scope=' . $scope;
        $url .= '&state=' . $state;
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
    public function oAuth2UserByFindInfo($isUnionId = false, $lang = self::LANG_ZH_CN){
        if (!$userAccessToken = $this->oAuth2UserByUserAccessToken()) {
            return false;
        }

        if($isUnionId && $info = $this->userInfoByUnionID($userAccessToken['openid'])){
            if($info['subscribe']){
                return $info;
            }
        }

        $url = self::API_OAUTH2_USER_INFO . $userAccessToken['access_token'];
        $url .= '&openid=' . $userAccessToken['openid'];
        $url .= '&lang=' . $lang;
        return $this->httpGet($url);
    }
    /**
     * 获取用户的AccessToken
     * @param string $code 用户授权得到的 $code;
     * @return ok ["access_token":"ACCESS_TOKEN","expires_in":7200,"refresh_token":"REFRESH_TOKEN","openid":"OPENID","scope":"SCOPE"]
     */
    public function oAuth2UserByUserAccessToken($code = null){
        $code = $code ? $code : $_GET['code'];
        $url = self::API_OAUTH2_ACCESS_TOKEN;
        $url .= 'access_token=' . $this->getAccessToken();
        $url .= '&appid=' . $this->appid;
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
    public function userInfoUnionIDByFind($openid, $lang = self::LANG_ZH_CN){
        $url = self::API_USER_UNIONN_ID_INFO;
        $url .= 'openid=' . $openid;
        $url .= '&lang=' . $lang;
        $url .= '&access_token=';

        return $this->httpGet($url, null, true);
    }
    /**
     * @var 批量获取用户信息
     * @param array $openids [['openid' => '', 'lang' => '']]  lang 可不传
     */
    public function userInfoUnionIDBySelect(array $openids){
        if(count($openids) > 100){
            return $this->setErrors(-1, '单次最大拉取100用户信息');
        }

        $data['user_list'] = $openids;
        if(!$list = $this->httpPost(self::API_USER_UNIONN_ID_INFO_LSIT, $data, true)){
            return false;
        }

        return $list['user_info_list'];
    }
    /**
     * @var 拉取关注者的openid列表
     * @param $next_openid
     * @return [
     *     'total' => '',
     *     'count' => '',
     *     'data' => [
     *          'openid' => [
     *
     *          ],
     *      ],
     *      'next_openid' => '',
     * ]
     */
    public function userOpenidBySelect($next_openid = ''){
        $url = self::API_USER_OPENID_LIST;
        $url .= 'next_openid=' . $next_openid;
        $url .= '&access_token=';
        return $this->httpGet($url, null, true);
    }
    /**
     * @var 拉取黑名单列表
     * @param string $begin_openid
     */
    public function userBlackOpenidsBySelect($begin_openid = ''){
        $data['begin_openid'] = $begin_openid;
        return $this->httpPost(self::API_USER_BLACK_OPENID_LIST, $data, true);
    }
    /**
     * @var 批量拉黑用户
     * @param array $openids
     */
    public function userBlackOpenidsBySet($openids = []){
        $data['openid_list'] = $openids;
        if(!$this->httpPost(self::API_USER_BLACK_SET, $data, true)){
            return false;
        }

        return true;
    }
    /**
     * @var 批量取消拉黑用户
     * @param array $openids
     */
    public function userBlackOpenidsByUnSet($openids = []){
        $data['openid_list'] = $openids;
        if(!$this->httpPost(self::API_USER_UN_BLACK_SET, $data, true)){
            return false;
        }

        return true;
    }
    /**
     * @var 获取某个用户打上去的标签
     * @param string $openid
     */
    public function userTagByFind($openid){
        $data['openid'] = $openid;
        if(!$result = $this->httpPost(self::API_TAG_USER_GET ,$data, true)){
            return false;
        }

        return $result['tagid_list'];
    }
    /**
     * @var 获取标签下粉丝列表
     * @param $tag_id
     * @param null $next_openid
     * @return array count next_openid data['openid']=>[]
     */
    public function userTagBySelect($tag_id, $next_openid = ''){
        $data['tagid'] = $tag_id;
        $data['next_openid'] = $next_openid;
        if(!$result = $this->httpPost(self::API_TAG_USER_LIST, $data, true)){
            return false;
        }

        return $result;
    }
    /**
     * @var 批量为用户打标签
     * @param array $openid_list [openid, openid]
     * @param $tagid
     * @return bool
     */
    public function usersTagBySet($openid_list = [], $tagid){
        $data['openid_list'] = $openid_list;
        $data['tagid'] = $tagid;
        if(!$this->httpPost(self::API_TAG_USER_ADDS, $data, true)){
            return false;
        }

        return true;
    }
    /**
     * @var 批量为用户取消标签
     * @param array $openid_list 最多50个
     * @param $tagid
     */
    public function usersTagByUnSet($openid_list = [], $tagid){
        $data['openid_list'] = $openid_list;
        $data['tagid'] = $tagid;
        return $this->httpPost(self::API_TAG_USER_UNADDS, $data, true);
    }
    //------------------------------用户信息相关--------------------------------//
}
