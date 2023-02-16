<?php

trait TraitMaterialComment{
    //------------------------------素材评论--------------------------------//
    /**
     * @var 打开已群发文章评论
     * @param $msg_data_id 群发返回的msg_data_id
     * @param null $index 多图文时，用来指定第几篇图文，从0开始，不带默认返回该msg_data_id的第一篇图文
     * @return bool
     */
    public function commentByOpen($msg_data_id, $index = null){
        $data['msg_data_id'] = $msg_data_id;
        !$index OR $data['index'] = $index;
        if(!$this->httpPost(self::API_COMMENT_OPEN, $data, true)){
            return false;
        }

        return true;
    }
    /**
     * @var 关闭已群发文章评论
     * @param $msg_data_id 群发返回的msg_data_id
     * @param null $index 多图文时，用来指定第几篇图文，从0开始，不带默认返回该msg_data_id的第一篇图文
     * @return bool
     */
    public function commentByClose($msg_data_id, $index = null){
        $data['msg_data_id'] = $msg_data_id;
        !$index OR $data['index'] = $index;

        if(!$this->httpPost(self::API_COMMENT_CLOSE, $data, true)){
            return false;
        }

        return true;
    }
    /**
     * @var 获取评论列表
     * @param $msg_data_id 群发返回的msg_data_id
     * @param int $begin 起始位置
     * @param int $type type=0 普通评论&精选评论 type=1 普通评论 type=2 精选评论
     * @param int $index 多图文时，用来指定第几篇图文，从0开始，不带默认返回该msg_data_id的第一篇图文
     * @param int $count 获取数目（>=50会被拒绝）
     * @return false | array "total": TOTAL          //总数，非 comment 的size around
    "comment": [{
    "user_comment_id" : USER_COMMENT_ID  	//用户评论id
    "openid": OPENID        				//openid
    "create_time": CREATE_TIME    			//评论时间
    "content": CONTENT            			//评论内容
    "comment_type": IS_ELECTED   			//是否精选评论，0为即非精选，1为true，即精选
    "reply": {
    "content": CONTENT       		//作者回复内容
    "create_time" : CREATE_TIME  	//作者回复时间
    }
    }]
    }
     */
    public function commentBySelect($msg_data_id, $begin = 0, $type = self::TYPE_ALL, $index = 0, $count = 49){
        $data['msg_data_id'] = $msg_data_id;
        $data['index'] = $index;
        $data['begin'] = $begin;
        $data['type'] = $type;

        return $this->httpPost(self::API_COMMENT_LIST, $data, true);
    }
    /**
     * @var 评价加精
     * @param $msg_data_id 群发返回的msg_data_id
     * @param $user_comment_id 评论id
     * @param int $index 多图文时，用来指定第几篇图文，从0开始，不带默认返回该msg_data_id的第一篇图文
     * @return bool
     */
    public function commentFeaturedBySet($msg_data_id, $user_comment_id, $index = 0){
        $data['msg_data_id'] = $msg_data_id;
        $data['index'] = $index;
        $data['user_comment_id'] = $user_comment_id;
        if(!$this->httpPost(self::API_COMMENT_MARKELECT, $data, true)){
            return false;
        }

        return true;
    }
    /**
     * @var 评价取精
     * @param $msg_data_id 群发返回的msg_data_id
     * @param $user_comment_id 评论id
     * @param int $index 多图文时，用来指定第几篇图文，从0开始，不带默认返回该msg_data_id的第一篇图文
     * @return bool|void
     */
    public function commentFeaturedByUnSet($msg_data_id, $user_comment_id, $index = 0){
        $data['msg_data_id'] = $msg_data_id;
        $data['index'] = $index;
        $data['user_comment_id'] = $user_comment_id;
        if(!$this->httpPost(self::API_COMMENT_UN_MARKELECT, $data, true)){
            return false;
        }

        return true;
    }
    /**
     * @var 删除评论
     * @param $msg_data_id 群发返回的msg_data_id
     * @param $user_comment_id 评论id
     * @param int $index 多图文时，用来指定第几篇图文，从0开始，不带默认返回该msg_data_id的第一篇图文
     * @return bool|void
     */
    public function commentByDel($msg_data_id, $user_comment_id, $index = 0){
        $data['msg_data_id'] = $msg_data_id;
        $data['index'] = $index;
        $data['user_comment_id'] = $user_comment_id;
        if(!$this->httpPost(self::API_COMMENT_DELETE, $data, true)){
            return false;
        }

        return true;
    }
    /**
     * @var 回复评论
     * @param $content
     * @param $msg_data_id 群发返回的msg_data_id
     * @param $user_comment_id 评论id
     * @param int $index 多图文时，用来指定第几篇图文，从0开始，不带默认返回该msg_data_id的第一篇图文
     */
    public function commentReplyByCreate($content, $msg_data_id, $user_comment_id, $index = 0){
        $data['content'] = $content;
        $data['msg_data_id'] = $msg_data_id;
        $data['index'] = $index;
        $data['user_comment_id'] = $user_comment_id;
        return $this->httpPost(self::API_COMMENT_REPLY, $data, true);
    }
    /**
     * @var 删除回复评论
     * @param $msg_data_id 群发返回的msg_data_id
     * @param $user_comment_id 评论id
     * @param int $index 多图文时，用来指定第几篇图文，从0开始，不带默认返回该msg_data_id的第一篇图文
     */
    public function commentReplyByDel($msg_data_id, $user_comment_id, $index = 0){
        $data['msg_data_id'] = $msg_data_id;
        $data['index'] = $index;
        $data['user_comment_id'] = $user_comment_id;
        if(!$this->httpPost(self::API_COMMENT_DEL_REPLY, $data, true)){
            return false;
        }

        return true;
    }
    //------------------------------素材评论--------------------------------//
}
