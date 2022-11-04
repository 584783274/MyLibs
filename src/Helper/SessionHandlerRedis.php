<?php

namespace Kang\Libs\Helper;

class SessionHandlerRedis implements \SessionHandlerInterface{

    public static function start($host = '127.0.0.1', $port = 6379, $password = '',array $options = []){
        $reids = new \Redis();
        $reids->connect($host, $port);
        if($password){
            $reids->auth($password);
        }

        $obj = new self($reids);
        session_set_save_handler($obj);
        return true;
    }

    private function __construct(\Redis $redis, array $options = []){
        $this->_redis = $redis;
        $this->_expire = isset($options['expiretime']) ? (int) $options['expiretime'] : 86400;
        $this->_prefix = isset($options['prefix']) ? $options['prefix'] : 'prefix_';
}

    /**
     * {@inheritdoc}
     */
    public function open($savePath, $sessionName){
        return true;
    }
    /**
     * {@inheritdoc}
     */
    public function close(){
        return $this->getRedis()->close();
    }
    /**
     * @var 读取 session 信息
     * @param string $sessionId
     * @return string
     */
    public function read($sessionId){
        return $this->getRedis()->get($this->getPrefix($sessionId)) ?: '';
    }
    /**
     * @var 写入数据
     * @param string $sessionId
     * @param string $data 序列号的数据
     * @return bool
     */
    public function write($sessionId, $data){
        $sessionId = $this->getPrefix($sessionId);
        $this->getRedis()->hSet($sessionId, $this->_key, $data);
        $this->getRedis()->expire($sessionId, $this->getExpire());
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function destroy($sessionId){
        var_dump($sessionId);
        return $this->getRedis()->del($this->getPrefix($sessionId));
    }
    /**
     * {@inheritdoc}
     */
    public function gc($maxlifetime){
        return true;
    }

    /**
     * Return a Memcache instance
     *
     * @return \Redis
     */
    public function getRedis(){
        return $this->_redis;
    }

    public function getPrefix($sessionId){
        return $this->_prefix . $sessionId;
    }

    public function getExpire(){
        return $this->_expire + time();
    }

    private $_redis;
    private $_expire;
    private $_prefix;
    private $_key = 'redis_session';
}
