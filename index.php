<?php
require_once './vendor/autoload.php';

$WeChatBase = new \Kang\Libs\WeChat\WeChat(['appid' => 'dfsfsfsfsf']);

$wechat = new \Kang\Libs\WeChat\WeChat();

$wechat->trigger(\Kang\Libs\WeChat\WeChat::EVENT_ACCESS_TOKEN_ERROR);