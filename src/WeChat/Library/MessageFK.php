<?php

namespace Kang\Libs\WeChat\Library;

/**
 *客服消息
 * Trait MessageFK
 * @package Kang\Libs\WeChat\Library
 */
trait MessageFK{
    /**
     * @var 创建客服帐号
     * @param $account 账户 账号前缀@公众号微信号
     * @param $password 密码
     * @param string $nickname 昵称
     * @return bool
     *
     */
    public function messageFKCreate($account, $nickname, $password){
        $data['kf_account'] = $account;
        $data['nickname']   = $nickname;
        $data['password']   = $password;
        if(!$this->httpPost(Urls::MESSAGE_KF_ACCOUNT_CREATE, $data, true)){
            return false;
        }

        return true;
    }
    /**
     * @var 修改客服帐号
     * @param $account 账户
     * @param $password 密码
     * @param string $nickname 昵称
     * @return bool
     *
     */
    public function messageFKModify($account, $nickname, $password){
        $data['kf_account'] = $account;
        $data['nickname']   = $nickname;
        $data['password']   = $password;
        if(!$this->httpPost(Urls::MESSAGE_KF_ACCOUNT_MODIFY, $data, true)){
            return false;
        }

        return true;
    }
    /**
     * @var 删除客服账户
     * @param $account
     * @return bool
     */
    public function messageFKDel($account){
        $data['kf_account'] = $account;
        if(!$this->httpPost(Urls::MESSAGE_KF_ACCOUNT_DEL, $data, true)){
            return false;
        }

        return true;
    }

    /**
     * @var 上传客户头像
     * @param $account
     * @param $imgPath
     */
    public function messageKfHeadimg($account, $imgPath){
        $imgPath = realpath($imgPath);
        if($imgPath == false){
            return $this->setErrors('图片文件不存在!');
        }

        $imgType = pathinfo($imgPath)['extension'];
        $data['headimg'] = class_exists('\CURLFile') ? new \CURLFile($imgPath, $imgType, $account . '.' . $imgType) : '@' . $imgPath;
        $data['kf_account'] = $account;
        if(!$this->httpPost(Urls::MESSAGE_KF_ACCOUNT_HEAD, $data, true, true)){
            return false;
        }

        return true;
    }
    /**
     * @var 获取客服列表
     * @return bool
     */
    public function  messageKfSelect(){
        if(!$result = $this->httpGet(Urls::MESSAGE_KF_ACCOUNT_SELECT, null, true)){
            return false;
        }

        return $result;
    }

    /**
     * @var 发送客户消息
     * @param array $data
     * @return bool
     */
    public function messageKfSend(array $data){
        if(!$this->httpPost(Urls::MESSAGE_KF_ACCOUNT_SEND, $data, true)){
            return false;
        }

        return true;
    }
    /**
     * @var 发送文本消息
     * @param $touser
     * @param $content <a href="http://www.qq.com" data-miniprogram-appid="appid" data-miniprogram-path="pages/index/index">点击跳小程序</a>
     */
    public function messageKfSendTxt($touser, $content){
        $data['touser'] = $touser;
        $data['msgtype'] = 'text';
        $data['text']['content'] = $content;

        return $this->messageKfSend($data);
    }
    /**
     * @var 发送图文消息
     * @param string $touser
     * @param array $articles 1篇图文消息 ["title":"Happy Day",
    "description":"Is Really A Happy Day","url":"URL","picurl":"PIC_URL"]
     */
    public function messageKfSendNews($touser, array $articles){
        $data['touser'] = $touser;
        $data['msgtype'] = 'news';
        $data['news']['articles'][] = $articles;

        return $this->messageKfSend($data);
    }
    /**
     * @var 发送图文消息
     * @param string $touser
     * @param string $media_id
     */
    public function messageKfSendNewsMp($touser, $media_id){
        $data['touser'] = $touser;
        $data['msgtype'] = 'mpnews';
        $data['mpnews']['media_id'] = $media_id;

        return $this->messageKfSend($data);
    }
    /**
     * @var 发送菜单消息
     * @param string $touser
     * @param array $list  //[['id' => 101, 'content' => '满意', ], ['id' => 102, 'content' => '不满意', ]]
     * @param string $head_content //您对本次服务是否满意呢?
     * @param string $tail_content //欢迎再次光临
     */
    public function messageKfSendMenu($touser, array $list, $head_content, $tail_content){
        $data['touser'] = $touser;
        $data['msgtype'] = 'msgmenu';
        $data['msgmenu']['head_content'] = $head_content;
        $data['msgmenu']['list'] = $list;
        $data['msgmenu']['tail_content'] = $tail_content;

        return $this->messageKfSend($data);
    }
    /**
     * @var 发送图片消息
     * @param string $touser
     * @param string $media_id
     */
    public function messageKfSendImage($touser, $media_id){
        $data['touser'] = $touser;
        $data['msgtype'] = 'image';
        $data['image']['media_id'] = $media_id;

        return $this->messageKfSend($data);
    }
    /**
     * @var 发送语音消息
     * @param $touser
     * @param $media_id
     */
    public function messageKfSendVoice($touser, $media_id){
        $data['touser'] = $touser;
        $data['msgtype'] = 'voice';
        $data['voice']['media_id'] = $media_id;

        return $this->messageKfSend($data);
    }
    /**
     * @var 发送视频消息
     * @param string $touser
     * @param string $media_id
     * @param string $thumb_media_id
     * @param string $title
     * @param string $description
     * @return bool|void
     */
    public function messageKfSendVodeo($touser, $media_id, $thumb_media_id, $title = '', $description = ''){
        $data['touser'] = $touser;
        $data['msgtype'] = 'video';
        $data['video']['media_id'] = $media_id;
        $data['video']['thumb_media_id'] = $thumb_media_id;
        $data['video']['title'] = $title;
        $data['video']['description'] = $description;

        return $this->messageKfSend($data);
    }
    /**
     * @var 发送卡券 仅支持非自定义Code码和导入code模式的卡券的卡券
     * @param string $touser
     * @param string $card_id
     * @return bool|void
     */
    public function messageKfSendCard($touser, $card_id){
        $data['touser'] = $touser;
        $data['msgtype'] = 'wxcard';
        $data['wxcard']['card_id'] = $card_id;

        return $this->messageKfSend($data);
    }
    /**
     * @var 发送小程序卡片（要求小程序与公众号已关联）
     * @param string $touser
     * @param string $appid
     * @param string $pagepath
     * @param string $thumb_media_id
     * @param string $title
     */
    public function messageKfSendMiniprogrampage($touser, $appid, $pagepath, $thumb_media_id, $title = ''){
        $data['touser'] = $touser;
        $data['msgtype'] = 'miniprogrampage';
        $data['miniprogrampage'] = [
            'title' => $title,
            'appid' => $appid,
            'pagepath' => $pagepath,
            'thumb_media_id' => $thumb_media_id
        ];

        return $this->messageKfSend($data);
    }
    /**
     * @var 小程序图文消息
     * @param string $title 消息标题
     * @param string $description 图文链接消息
     * @param string $url 图文链接消息被点击后跳转的链接
     * @param string $thumb_url 图文链接消息的图片链接，支持 JPG、PNG 格式，较好的效果为大图 640 X 320，小图 80 X 80
     */
    public function messageKfSendLink($title, $description, $url, $thumb_url){
        $data['title'] = $title;
        $data['description'] = $description;
        $data['url'] = $url;
        $data['thumb_url'] = $thumb_url;

        return $this->messageKfSend($data);
    }
}
