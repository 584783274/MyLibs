<?php

namespace Kang\Libs\Git;

use Kang\Libs\Base\Component;
/**
 * git的操作
 * Class Git
 * @property string $token WebHook 密码/签名密钥
 * @property string $path 项目任务地址
 * @package Kang\Libs\Git
 */
class Git extends Component{
    const USER_AGENT = 'git-oschina-hook'; //固定为 git-oschina-hook，可用于标识为来自 gitee 的请求

    public function monitor(){
        if($this->isAgent() === false || $this->validateToken() === false){
            return;
        }

        $this->pull();
    }


    private function pull(){
        $path = $this->path ? $this->path : './';
        $command = "cd {$path} && git pull";
        $log = shell_exec($command);

    }

    public function event(){
        return $_SERVER['HTTP_X_GITEE_EVENT'] ?? '';
    }

    //判断请求是否是GIT发出的请求
    private function isAgent(){
        return isset($_SERVER['HTTP_USER_AGENT']) && $_SERVER['HTTP_USER_AGENT'] == self::USER_AGENT;
    }
    //验证密钥
    private function validateToken(){
        return isset($_SERVER['HTTP_X_GITEE_TOKEN']) && $this->token == $_SERVER['HTTP_X_GITEE_TOKEN'];
    }
}
