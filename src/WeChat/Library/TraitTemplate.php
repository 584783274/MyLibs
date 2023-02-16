<?php

namespace Kang\Libs\WeChat\Library;
/**
 * @var 模板
 * Trait TraitTemplate
 * @package Kang\Libs\WeChat\Library
 */
trait TraitTemplate{
    //--------------------------模板消息接口--------------------------------//
    /**
     * @var 添加模板
     * @param string $template_id_short
     * @return bool|mixed
     */
    public function templateByCreate(string $template_id_short){
        $data['template_id_short'] = $template_id_short;
        if(!$result = $this->httpPost(self::API_TEMPLATE_ADD, $data, true)){
            return false;
        }

        return $result['template_id'];
    }
    /**
     * @var 获取模板列表
     * @return false | array [[template_id => '模板ID',title => '模板标题',primary_industry => '模板所属行业的一级行业',deputy_industry => '模板所属行业的二级行业',content => '模板内容',example	 => '模板示例',]]
     */
    public function templateBySelect(){
        if(!$result = $this->httpGet(self::API_TEMPLATE_GET, null, true)){
            return false;
        }

        return $result['template_list'];
    }
    /**
     * @var 删除模板
     * @param string $template_id
     * @return bool
     */
    public function templateByDel(string $template_id){
        $data['template_id'] = $template_id;
        if(!$this->httpPost(self::API_TEMPLATE_DEL, $data, true)){
            return false;
        }

        return true;
    }
    /**
     * @var 发送模板消息
     * @param $touser 用户的openid
     * @param $template_id 模板ID
     * @param array $data ['first' => ['value' => '', 'color']]
     * @param string $url
     * @param array $miniprogram ['appid' => '所需跳转到的小程序appid', 'pagepath' => '所需跳转到小程序的具体页面路径']
     */
    public function templateBySend($touser, $template_id, array $body, $url = '', array $miniprogram = []){
        $data['touser'] = $touser;
        $data['template_id'] = $template_id;
        $data['url'] = $url;
        $data['miniprogram'] = $miniprogram;
        $data['data'] = $body;
        if(!$this->httpPost(self::API_TEMPLATE_SEND, $data, true)){
            return false;
        }

        return true;
    }
    //--------------------------模板消息接口--------------------------------//
}
