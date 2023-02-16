<?php

namespace Kang\Libs\WeChat\Library;

trait TraitTag{
    //------------------------------标签相关--------------------------------//
    /**
     * @var 创建标签
     * @param string $name 标签名称
     * @return bool|void
     */
    public function tagByCreate($name){
        $data['tag']['name'] = $name;
        if(!$result = $this->httpPost(self::API_TAG_CREATE, $data, true)){
            return false;
        }

        return $result['tag']['id'];
    }
    /**
     * @var 获取标签列表
     * @return bool|void
     */
    public function tagBySelect(){
        if(!$result = $this->httpGet(self::API_TAG_LSIT, null, true)){
            return false;
        }

        return $result['tags'];
    }
    /**
     * @var 修改标签
     * @param string $tag_id
     * @param string $name
     */
    public function tagByModify($tag_id, $name){
        $data['tag']['id'] = $tag_id;
        $data['tag']['name'] = $name;
        if(!$this->httpPost(self::API_TAG_UPDATE, $data, true)){
            return false;
        }

        return true;
    }
    /**
     * @var 删除标签
     * @param $tag_id
     */
    public function tagByDel($tag_id){
        $data['tag']['id'] = $tag_id;
        if(!$this->httpPost(self::API_TAG_DELETE, $data, true)){
            return false;
        }

        return true;
    }

    //------------------------------标签相关--------------------------------//
}
