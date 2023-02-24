<?php
namespace Kang\Libs\WeChat\Library;
/**
 * 消息群发
 * Trait Mass
 * @package Kang\Libs\WeChat\Library
 */
trait MessageMass{
    /**
     * @var 消息群发
     * @param array $data
     * @return bool|array msg_id 消息发送任务的ID msg_data_id 消息的数据ID，该字段只有在群发图文消息时，才会出现。可以用于在图文分析数据接口中，获取到对应的图文消息的数据，是图文分析数据接口中的 msgid 字段中的前半部分，详见图文分析数据接口中的 msgid 字段的介绍。
     */
    public function messageMassSend(array $data){
        if(!$result = $this->httpPost(Urls::MESSAGE_MASS_SEND, $data, true)){
            return false;
        }

        return $result;
    }
    /**
     * @var
     * @param string $media_id 素材管理的media_id
     * @param $title
     * @param $description
     * @return bool|string
     */
    public function messageUploadVideo($media_id, $title, $description){
        $data['media_id'] = $media_id;
        $data['title'] = $title;
        $data['description'] = $description;
        if(!$result = $this->httpPost(Urls::MESSAGE_MASS_UPLOAD_VIDEO, $data, true)){
            return false;
        }

        return $result['media_id'];
    }
    /**
     * @var 群发图文消息
     * @param $media_id 图文消息的media_id
     * @param int $send_ignore_reprint 当判断转载的时候是否继续群发 0 停止， 1继续
     * @param null $tag_id 要发送的用户标签群体
     * @param null $clientmsgid 主动设置 clientmsgid 参数，避免重复推送
     */
    public function messageMassSendNewsMp($media_id, $send_ignore_reprint = 1, $tag_id = null, $clientmsgid = null){
        $data['filter']['is_to_all'] = $tag_id ? false : true;
        $data['filter']['tag_id'] = $tag_id;
        $data['mpnews']['media_id'] = $media_id;
        $data['msgtype']= 'mpnews';
        $data['send_ignore_reprint'] = $send_ignore_reprint;
        $data['clientmsgid'] = $clientmsgid;

        return $this->messageMassSend($data);
    }
    /**
     * @var 群发文本消息
     * @param string$content 文本内容
     * @param null $tag_id 要发送的用户标签群体
     */
    public function messageMassSendText($content,  $tag_id = null, $clientmsgid = null){
        $data['filter']['is_to_all'] = $tag_id ? false : true;
        $data['filter']['tag_id'] = $tag_id;
        $data['text']['content'] = $content;
        $data['msgtype']= 'text';
        $data['clientmsgid'] = $clientmsgid;

        return $this->messageMassSend($data);
    }
    /**
     * @var 群发卡劵消息
     * @param string$content 文本内容
     * @param null $tag_id 要发送的用户标签群体
     */
    public function messageMassSendCard($card_id,  $tag_id = null, $clientmsgid = null){
        $data['filter']['is_to_all'] = $tag_id ? false : true;
        $data['filter']['tag_id'] = $tag_id;
        $data['msgtype']= 'wxcard';
        $data['wxcard']['card_id'] = $card_id;
        $data['clientmsgid'] = $clientmsgid;

        return $this->messageMassSend($data);
    }
    /**
     * @var 删除群发消息
     * @param string $msg_id 发送出去的消息ID
     * @param null $article_idx 要删除的文章在图文消息中的位置，第一篇编号为1，该字段不填或填0会删除全部文章
     */
    public function messageMassDel($msg_id,  $article_idx = null){
        $data['msg_id'] = $msg_id;
        $data['article_idx'] = $article_idx;
        if(!$this->httpPost(Urls::MESSAGE_MASS_DEL, $data, true)){
            return false;
        }

        return true;
    }
    /**
     * @var 查询群发消息发送状态
     * @param $msg_id
     * @return bool| array msg_status 消息发送后的状态，SEND_SUCCESS表示发送成功，SENDING表示发送中，SEND_FAIL表示发送失败，DELETE表示已删除
     */
    public function messageMassGet($msg_id){
        $data['msg_id'] = $msg_id;
        return $this->httpPost(Urls::MESSAGE_MASS_GET, $data, true);
    }

    /**
     * @var 发送消息预览
     * @param array $data
     * @return bool|void
     */
    public function  messageMassSendPreview(array $data){
        if(!$this->httpPost(Urls::MESSAGE_MASS_PREVIEW, $data, true)){
            return false;
        }

        return true;
    }
    /**
     * @var 预览图文消息
     * @param string $touser
     * @param string $media_id
     * @return bool|void
     */
    public function messageMassSendPreviewNewsMp($touser, $media_id){
        $data['touser'] = $touser;
        $data['mpnews']['media_id'] = $media_id;
        $data['msgtype'] = 'mpnews';
        return $this->messageMassSendPreview($data);
    }
    /**
     * @var 预览文本消息
     * @param string $touser
     * @param string $content
     * @return bool|void
     */
    public function messageMassSendPreviewText($touser, $content){
        $data['touser'] = $touser;
        $data['text']['content'] = $content;
        $data['msgtype'] = 'text';

        return $this->messageMassSendPreview($data);
    }
    /**
     * @var 设置群发速度
     * @param int $speed 0-4
     * @return bool
     */
    public function messageMassSendSpeed($speed){
        $data['speed'] = $speed;
        if(!$this->httpPost(Urls::MESSAGE_MASS_SPEED, $data, true)){
            return false;
        }

        return true;
    }
}
