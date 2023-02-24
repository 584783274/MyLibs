<?php

namespace Kang\Libs\WeChat\Library;
/**
 * 用户标签
 * Trait Tag
 * @package Kang\Libs\WeChat\Library
 */
trait Tag{
    /**
     * @var 创建标签
     * @param string $name 标签名称
     * @return bool|void
     */
    public function tagCreate($name){
        $data['tag']['name'] = $name;
        if(!$result = $this->httpPost(Urls::TAG_CREATE, $data, true)){
            return false;
        }

        return $result['tag']['id'];
    }
    /**
     * @var 获取标签列表
     * @return bool|void
     */
    public function tagGet(){
        if(!$result = $this->httpGet(Urls::TAG_GET, null, true)){
            return false;
        }

        return $result['tags'];
    }
    /**
     * @var 修改标签
     * @param string $tag_id
     * @param string $name
     */
    public function tagModify($tag_id, $name){
        $data['tag']['id'] = $tag_id;
        $data['tag']['name'] = $name;
        if(!$this->httpPost(Urls::TAG_MODIFY, $data, true)){
            return false;
        }

        return true;
    }
    /**
     * @var 删除标签
     * @param $tag_id
     */
    public function tagDel($tag_id){
        $data['tag']['id'] = $tag_id;
        if(!$this->httpPost(Urls::TAG_DELETE, $data, true)){
            return false;
        }

        return true;
    }
    /**
     * @var 获取标签下的微信openid
     * @param $tagid
     * @param string $next_openid
     * @return mixed
     */
    public function tagUsersSelect($tagid, $next_openid = ''){
        $data['tagid'] = $tagid;
        $data['next_openid'] = $next_openid;

        return $this->httpPost(Urls::TAG_USERS_SELECT, $data, true);
    }
    /**
     * @var 为用户打标签
     * @param $tagid
     * @param array $openidList
     * @return bool
     */
    public function tagUsersSet($tagid, array $openidList){
        $data['tagid'] = $tagid;
        $data['openid_list'] = $openidList;

        if(!$this->httpPost(Urls::TAG_USERS_SET, $data, true)){
            return false;
        }

        return true;
    }
    /**
     * @var 批量为用户取消标签
     * @param $tagid
     * @param array $openidList
     * @return bool
     */
    public function tagUsersUnSet($tagid, array $openidList){
        $data['tagid'] = $tagid;
        $data['openid_list'] = $openidList;
        if(!$this->httpPost(Urls::TAG_USERS_UN_SET, $data, true)){
            return false;
        }

        return true;
    }
    /**
     * @var 获取用户身上的标签列表
     * @param $openid
     * @return bool|array [1,2,3]
     */
    public function tagUserTags($openid){
        $data['openid'] = $openid;
        if(!$result = $this->httpPost(Urls::TAG_USERS_UN_SET, $data, true)){
            return false;
        }

        return $result['tagid_list'];
    }
}
