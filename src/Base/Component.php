<?php

namespace Kang\Libs\Base;

use Closure;

class Component{
    public function __construct($config = []){
        $this->setConfig($config);
    }
    /**
     * @var 获取该类扩展为该类提供服务
     * @return array
     */
    public function behaviors() : array {
        return [];
    }

    /**
     * @var 事件触发
     * @param $name 事件名称
     * @param Event|null $event
     */
    public function trigger($name, Event $event = null){
        $this->ensureBehaviors();
        if (!empty($this->_events[$name])) {
            $eventHandlers = $this->_events[$name];
            if ($event === null) {
                $event = new Event();
                $event->handled = false;
            }

            $event->name = $name;
            $event->sender = $this;

            foreach ($eventHandlers as $handler) {
                $event->onData = $handler[1];
                call_user_func($handler[0], $event);
                if ($event->handled) {
                    return;
                }
            }
        }
    }
    /**
     * @var 获取指定扩展
     * @param $name
     */
    public function getBehavior($name){
        $this->ensureBehaviors();
        if(!isset($this->_behaviors[$name])){
            throw new ErrorBase($name . ' extension not exist');
        }

        return $this->_behaviors[$name];
    }
    /**
     * @param $name
     * @param $behavior
     * @return $this
     */
    public function addBehavior($name, $behavior){
        $this->ensureBehaviors();
        $this->attachBehaviorInternal($name, $behavior, true);
        return $this;
    }

    /**
     * @var 添加事件监听
     * @param string $name 事件名称
     * @param array | Closure $handler
     * @param null $data
     * @param bool $append
     * @return $this
     * @throws ErrorBase
     */
    public function on($name, $handler, $data = null, $append = true){
        $this->ensureBehaviors();
        if($append == false){
            unset($this->_events[$name]);
        }

        $this->_events[$name][] = [$handler, $data];
        return $this;
    }
    /**
     * @var 解除事件
     * @param string $name 事件名称
     * @return $this
     * @throws ErrorBase
     */
    public function off($name){
        $this->ensureBehaviors();
        unset($this->_events[$name]);
        return $this;
    }
    /**
     * @throws ErrorBase
     */
    public function ensureBehaviors(){
        if ($this->_behaviors === null) {
            $this->_behaviors = [];
            foreach ($this->behaviors() as $name => $behavior) {
                $this->attachBehaviorInternal($name, $behavior);
            }
        }
    }
    /**
     * @param $name
     * @param $behavior
     * @return Behavior|mixed
     * @throws ErrorBase
     */
    private function attachBehaviorInternal($name, $behavior){
        if(empty($behavior)){
            return null;
        }

        try{
            if (!is_object($behavior)) {
                $behavior = new $behavior;
            }

            if($behavior instanceof Behavior){
                $behavior->attach($this);
            }
        }catch(\Exception $exception){
            throw new ErrorBase($exception->getMessage(), 1, $exception);
        }

        if (is_int($name)) {
            $this->_behaviors[] = $behavior;
        } else {
            $this->_behaviors[$name] = $behavior;
        }

        return $behavior;
    }

    /**
     * @var 设置初始配置信息!
     * @param array $config
     */
    public function setConfig($config = []){
        foreach($config as $attr => $value){
            $attr = 'set' . ucfirst($attr);
            if (method_exists($this, $attr)){
                $this->$attr($value);
            }
        }
    }
    /**
     * @var 获取配置信息
     * @return array
     */
    public function getConfig(){
        return $this->_config;
    }
    /**
     * @param $name
     * @param $value
     * @return $this
     */
    public function __set($name, $value){
        $attr = 'set' . ucfirst($name);
        if(method_exists($this, $attr)){
            return $this->{$attr}($value);
        }

        return $this;
    }
    /**
     * @param $name
     * @return |null
     */
    public function __get($name){
        $attr = 'get' . ucfirst($name);;
        if(method_exists($this, $attr)){
            return $this->{$attr}();
        }

        return $this->_config[$name] ?? null;
    }
    /**
     * @param $name
     * @param $arguments
     * @return mixed
     * @throws ExceptionError
     */
    public function __call($name, $arguments){
        $arguments[] = $this;
        $this->ensureBehaviors();
        foreach($this->_behaviors as $behavior){
            if(method_exists($behavior, $name)){
                return call_user_func_array([$behavior, $name], $arguments);
            }
        }

        throw new ErrorBase('当前类方法不存在:' . $name);
    }

    /**
     * @var 设置错误信息
     * @param int    $errorCode  错误码
     * @param string $errorMsg   错误信息
     * @return bool
     */
    public function setErrors($errorCode, $errorMsg){
        $this->_errorCode = $errorCode;
        $this->_errorMsg  = $errorMsg;
        return false;
    }
    /**
     * @var 获取错误响应码
     * @return string
     */
    public function getErrorCode(){
        return $this->_errorCode;
    }
    /**
     * @var 获取错误信息
     * @return string
     */
    public function getErrorMsg(){
        return $this->_errorMsg;
    }
    /**
     * @var 获取错误码和错误信息
     * @return array
     */
    public function getErrors(){
        return [
            'errcode' => $this->_errorCode,
            'errmsg' => $this->_errorMsg,
        ];
    }

    protected $_config = [];
    private $_events = [];
    private $_errorCode = 0;
    private $_errorMsg = '';
    private $_behaviors = null;
}