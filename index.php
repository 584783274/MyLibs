<?php
require_once './vendor/autoload.php';


$wechat = new \Kang\Libs\WeChat\WeChat([
    'appid' => 'wx6a0d4dddac14cdfc',
    'appsecret' => 'ea0840a45b69c358a9e775525c95273d',
    'token' => 'token',
    'EncodingAESKey' => '1jBc2gaRDKnDO8S50soKzKihgAdqpINmvmUrbbxYDr8'
]);


$wechat->validate();

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

