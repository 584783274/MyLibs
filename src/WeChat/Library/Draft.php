<?php

namespace Kang\Libs\WeChat\Library;
/**
 * 草擦管理
 * Trait Draft
 * @package Kang\Libs\WeChat\Library
 */
trait Draft{
    /**
     * 上传草稿素材
     * @param array $articles[] = []二维数组
     *[
        title	是	图文消息的标题
        author	否	图文消息的作者
        digest	否	图文消息的描述，如本字段为空，则默认抓取正文前64个字
        content	是	图文消息页面的内容，支持HTML标签
        content_source_url	否	在图文消息页面点击“阅读原文”后的页面，受安全限制，如需跳转Appstore，可以使用itun.es或appsto.re的短链服务，并在短链后增加 #wechat_redirect 后缀。
        thumb_media_id: 是	图文消息缩略图的media_id，可以在素材管理-新增素材中获得
        need_open_comment	否	Uint32 是否打开评论，0不打开，1打开
        only_fans_can_comment	否	Uint32 是否粉丝才可评论，0所有人可评论，1粉丝才可评论
    ]
     * @return bool| media_id
     */
    public function draftCreate($articles = []){
        $data['articles'] = $articles;
        if(!$result = $this->httpPost(Urls::DRAFT_CREATE, $data, true)){
            return false;
        }

        return $result['media_id'];
    }
    /**
     * @var 获取某个草稿详情
     * @param $media_id
     * @return bool|mixed
     */
    public function draftGet($media_id){
        $data['media_id'] = $media_id;
        if(!$result = $this->httpPost(Urls::DRAFT_GET, $data, true)){
            return false;
        }

        return $result['news_item'];
    }
    /**
     * @var 删除草稿
     * @param $media_id
     * @return bool
     */
    public function draftDel($media_id){
        $data['media_id'] = $media_id;
        if(!$result = $this->httpPost(Urls::DRAFT_DEL, $data, true)){
            return false;
        }

        return true;
    }
    /**
     * @var 草稿修改
     * @param string $media_id 要修改的图文消息的id
     * @param $index 要更新的文章在图文消息中的位置（多图文消息时，此字段才有意义），第一篇为0
     * @param array $articles
     * @return bool
     */
    public function draftModify($media_id, $index, array $articles){
        $data['media_id'] = $media_id;
        $data['index'] = $index;
        $data['articles'] = $articles;
        if(!$result = $this->httpPost(Urls::DRAFT_MODIFY, $data, true)){
            return false;
        }

        return true;
    }
    /**
     * @var 获取草稿总数
     * @return false | int
     */
    public function draftCount(){
        if(!$result = $this->httpGet(Urls::DRAFT_COUNT, null, true)){
            return false;
        }

        return $result['total_count'];
    }
    /**
     * @param int $offset 从全部素材的该偏移位置开始返回，0表示从第一个素材返回
     * @param int $count 返回素材的数量，取值在1到20之间
     * @param int $no_content 1 表示不返回 content 字段，0 表示正常返回，默认为 0
     * @return bool|void|null
     */
    public function draftSelect($offset = 0, $count = 20, $no_content = 0){
        $data['offset'] = $offset;
        $data['count'] = $count;
        $data['no_content'] = $no_content;
        if(!$result = $this->httpPost(Urls::DRAFT_LIST, $data, true)){
            return false;
        }

        return $result;
    }
    /**
     * @var 发布草稿
     * @param string $media_id
     * @return bool|publish_id 发布任务id
     */
    public function draftPush($media_id){
        $data['media_id'] = $media_id;
        if(!$result = $this->httpPost(Urls::DRAFT_PUSH, $data, true)){
            return false;
        }

        return $result['publish_id'];
    }
    /**
     * @var 查询发表状态
     * @param string $media_id
     * @return [
         publish_id	发布任务id
         publish_status	发布状态，0:成功, 1:发布中，2:原创失败, 3: 常规失败, 4:平台审核不通过, 5:成功后用户删除所有文章, 6: 成功后系统封禁所有文章
         article_id	当发布状态为0时（即成功）时，返回图文的 article_id，可用于“客服消息”场景
         count	当发布状态为0时（即成功）时，返回文章数量
         idx	当发布状态为0时（即成功）时，返回文章对应的编号
         article_url	当发布状态为0时（即成功）时，返回图文的永久链接
        fail_idx	当发布状态为2或4时，返回不通过的文章编号，第一篇为 1；其他发布状态则为空
    ]
     */
    public function draftPushStatus($media_id){
        $data['media_id'] = $media_id;
        if(!$result = $this->httpPost(Urls::DRAFT_PUSH_STATUS, $data, true)){
            return false;
        }

        return $result;
    }
    /**
     * @var 删除发布
     * @param string $article_id 成功发布时返回的 article_id
     * @param null $index 要删除的文章在图文消息中的位置，第一篇编号为1，该字段不填或填0会删除全部文章
     * @return bool
     */
    public function draftPushDel($article_id, $index = null){
        $data['article_id'] = $article_id;
        $data['index'] = $index;
        if(!$result = $this->httpPost(Urls::DRAFT_PUSH_DEL, $data, true)){
            return false;
        }

        return true;
    }
    /**
     * @var 通过 article_id 获取已发布文章
     * @param string $article_id 要获取的草稿的article_id
     * @return bool|void|null
     */
    public function draftPushGetArticle($article_id){
        $data['article_id'] = $article_id;
        if(!$result = $this->httpPost(Urls::DRAFT_PUSH_ARTICLE, $data, true)){
            return false;
        }

        return $result;
    }
    /**
     * @var 获取成功发布列表
     * @param int $offset 从全部素材的该偏移位置开始返回，0表示从第一个素材返回
     * @param int $count 返回素材的数量，取值在1到20之间
     * @param int $no_content  表示不返回 content 字段，0 表示正常返回，默认为 0
     * @return bool|void|null
     */
    public function draftPushSuccessSelect($offset = 0, $count = 20, $no_content = 0){
        $data['offset'] = $offset;
        $data['count'] = $count;
        $data['no_content'] = $no_content;
        if(!$result = $this->httpPost(Urls::DRAFT_PUSH_SUCCESS_ARTICLE, $data, true)){
            return false;
        }

        return $result;
    }
}
