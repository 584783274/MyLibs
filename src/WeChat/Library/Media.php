<?php

namespace Kang\Libs\WeChat\Library;
/**
 *素材管理
 * Trait Media
 * @package Kang\Libs\WeChat\Library
 */
trait Media{
    /**
     * @var 上传图片获取URL地址
     * @param string $imgPath
     * @param string $name
     * @return bool | string url
     */
    public function imageUploadGetUrl($imgPath, $filename = ''){
        $imgPath = realpath($imgPath);
        if($imgPath == false){
            return $this->setErrors(-1, '图片文件不存在!');
        }

        $info = pathinfo($imgPath);
        $imgType = $info['extension'];
        $filename = $filename ? $filename : $info['filename'];
        $data['media'] = class_exists('\CURLFile') ? new \CURLFile($imgPath, $imgType, $name . '.' . $imgType) : '@' . $imgPath;
        if(!$result = $this->httpPost(Urls::FILE_UPLOAD_IMAGE, $data, true, true)){
            return false;
        }

        return $result['url'];
    }
    /**
     * @var 上传--图片视频语音缩略图
     * @param string $imgPath 文件地址
     * @param string $type 图片（image）、语音（voice）、视频（video）和缩略图（thumb）
     * @param string $name 文件名称
     * @param string $introduction 视频描述
     * @return string media_id 新增的素材的media_id url新增的图片素材的图片URL（仅新增图片素材时会返回该字段）
     */
    public function mediaUploadFile($imgPath, $type = 'image', $isLong = true, $filename = '', $introduction = null){
        $imgPath = realpath($imgPath);
        if($imgPath == false){
            return $this->setErrors(-1, '文件不存在!');
        }

        $info = pathinfo($imgPath);
        $imgType = $info['extension'];
        $filename = $filename ? $filename : $info['filename'];
        $data['media'] = class_exists('\CURLFile') ? new \CURLFile($imgPath, $imgType, $filename . '.' . $imgType) : '@' . $imgPath;
        $data['type'] = $type;
        if($isLong && $type == 'voice'){
            $data['title'] = $filename;
            $data['introduction'] = $introduction;
        }

        if(!$result = $this->httpPost($isLong ? Urls::MEDIA_UPLOAD_LONG : Urls::MEDIA_UPLOAD_TEMPORARY, $data, true, true)){
            return false;
        }

        return $result;
    }
    /**
     * @var 删除永久素材
     * @param $media_id
     * @return bool
     */
    public function mediaDel($media_id){
        $data['media_id'] = $media_id;
        if(!$result = $this->httpPost(Urls::MEDIA_DEL, $data, true)){
            return false;
        }

        return true;
    }
    /**
     * @var 获取永久素材的总数，也会计算公众平台官网素材管理中的素材 2.图片和图文消息素材（包括单图文和多图文）的总数上限为5000，其他素材的总数上限为1000
     * @return bool|array voice_count 语音总数量 video_count	视频总数量 image_count	图片总数量 news_count	图文总数量
     */
    public function mediaTotal(){
        return $this->httpGet(Urls::MEDIA_CONST, null, true);
    }
    /**
     * @var 获取素材列表
     * @param string $type 素材的类型，图片（image）、视频（video）、语音 （voice）、图文（news）
     * @param int $offset 从全部素材的该偏移位置
     * @param int $count 每次拉取多少条数据， 最大20
     */
    public function  mediaSelect($type = 'image', $offset = 0, $count = 20){
        $data['type'] = $type;
        $data['offset'] = $offset;
        $data['count'] = $count;
        return $this->httpPost(Urls::MEDIA_LIST, $data, true);
    }
}
