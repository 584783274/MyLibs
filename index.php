<?php
require_once './vendor/autoload.php';


$wechat = new \Kang\Libs\WeChat\WeChat(['appid' => 'wx053536f0d2fab54f', 'appsecret' => 'b53d66485fe031b8eba114dbf7203eb6']);

$wechat->getAccessToken();
////
//$baidu = new \Kang\Libs\Baidu\Baidu([
//    'appid' => '28937645',
//    'appkey' => 'Ak5kyVTlrde5epeH8NVX7bjn',
//    'secret' => 'tl0v8HjM8UsXqWemsGHgOLKioCO1i3LT',
//]);
////
//var_dump($baidu->text->textOcrByPrecision('./a.webp'));

//$service = new \Kang\Libs\Center\Zookeeper\Service();

//$service->register();

