<?php

namespace Kang\Libs\WeChat\Library;
/**
 * 模板消息
 * Trait Template
 * @package Kang\Libs\WeChat\Library
 */
trait MessageTemplate{
    /**
     * @see https://developers.weixin.qq.com/doc/offiaccount/Message_Management/Template_Message_Interface.html#0
     * @var 设置所属行业
     * @param number $industry_id1 公众号模板消息所属行业编号
     * @param number $industry_id2 公众号模板消息所属行业编号
     * @return bool
     */
    public function messageTemplateSetIndustry($industry_id1, $industry_id2){
        $data['industry_id1'] = $industry_id1;
        $data['industry_id2'] = $industry_id2;
        if(!$this->httpPost(Urls::MESSAGE_TEMPLATE_SET_INDUSTRY, $data, true)){
            return false;
        }

        return true;
    }
    /**
     * @var 获取设置的行业信息
     * @return bool|array
     */
    public function messageTemplateGetIndustry(){
        if(!$result = $this->httpGet(Urls::MESSAGE_TEMPLATE_GET_INDUSTRY, null, true)){
            return false;
        }

        return $result;
    }
    /**
     * @var 添加模板
     * @param string $template_id_short
     * @return bool|string
     */
    public function messageTemplateCreate(string $template_id_short){
        $data['template_id_short'] = $template_id_short;
        if(!$result = $this->httpPost(Urls::MESSAGE_TEMPLATE_CREATE, $data, true)){
            return false;
        }

        return $result['template_id'];
    }
    /**
     * @var 获取模板列表
     * @return false | array [[template_id => '模板ID',title => '模板标题',primary_industry => '模板所属行业的一级行业',deputy_industry => '模板所属行业的二级行业',content => '模板内容',example	 => '模板示例',]]
     */
    public function messageTemplateGet(){
        if(!$result = $this->httpGet(Urls::MESSAGE_TEMPLATE_GET, null, true)){
            return false;
        }

        return $result['template_list'];
    }
    /**
     * @var 删除模板
     * @param string $template_id
     * @return bool
     */
    public function messageTemplateDel(string $template_id){
        $data['template_id'] = $template_id;
        if(!$this->httpPost(Urls::MESSAGE_TEMPLATE_DEL, $data, true)){
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
    public function messageTemplateSend($touser, $template_id, array $body, $url = '', array $miniprogram = []){
        $data['touser'] = $touser;
        $data['template_id'] = $template_id;
        $data['url'] = $url;
        $data['miniprogram'] = $miniprogram;
        $data['data'] = $body;
        if(!$this->httpPost(Urls::MESSAGE_TEMPLATE_SEND, $data, true)){
            return false;
        }

        return true;
    }
}
