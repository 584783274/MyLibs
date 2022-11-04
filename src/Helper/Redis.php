<?php

namespace Kang\Libs\Helper;

/**
 * Class RedisCache
 * @package MyLibs\Library
 */
class Redis extends \Redis{

    public function lockByLua($key, $expire = 5){
        $script = <<<EOF
local key = KEYS[1]
local value = ARGV[1]
local ttl = ARGV[2]
if (redis.call('setnx', key, value) == 1) then
return redis.call('expire', key, ttl)
elseif (redis.call('ttl', key) == -1) then
return redis.call('expire', key, ttl)
end

return 0

EOF;

        $this->_lockFlag = md5($key .microtime(true));
        return $this->_eval($script, [$key, $this->_lockFlag, $expire]);
    }

    public function unlock($key){
        $script = <<<EOF
local key = KEYS[1]
local value = ARGV[1]
if (redis.call('exists', key) == 1 and redis.call('get', key) == value)
then
return redis.call('del', key)
end
return 0
EOF;
        if ($this->_lockFlag) {
            return $this->_eval($script, [$key, $this->_lockFlag]);
        }
    }

    private function _eval($script, array $params, $keyNum = 1){
        $hash = $this->script('load', $script);
        return $this->evalSha($hash, $params, $keyNum);
    }

    private $_lockFlag;
}
