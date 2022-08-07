<?php

namespace Kang\Libs\Base;

/**
 * @var 错误类
 * Class BaseErrors
 * @package MyLibs\Errors
 */
class ErrorBase extends \Exception{
    /**
     * @var 判断是否是错误类
     * @param $object
     * @return bool
     */
    public static function isError($object){
        if($object instanceof self){
            return true;
        }

        return false;
    }
    /**
     * @var 设置错误信息
     * @param $message 错误消息
     * @param $code 错误代码
     * @return $this
     */
    public function setErrors($message, $code){
        $this->message = $message;
        $this->code = $code;
        return $this;
    }
    /**
     * @var 设置错误信息
     * @param $message
     * @return $this
     */
    public function setError($message){
        $this->message = $message;
        $this->code = 1000;
        return $this;
    }
    /**
     * @var 设置错误信息
     * @param $code
     * @return $this
     */
    public function setCode($code){
        $this->code = $code;
        return $this;
    }
    /**
     * @var 获取错误信息
     * @return mixed
     */
    public function getError(){
        return $this->message;
    }
    /**
     * @var 获取错误信息数组
     * @return array
     */
    public function getErrors(){
        return [
            'code' => $this->code,
            'msg'  => $this->message
        ];
    }
}