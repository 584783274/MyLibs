<?php
require_once './vendor/autoload.php';


//$wechat = new \Kang\Libs\WeChat\WeChat(['appid' => 'wx053536f0d2fab54f', 'appsecret' => 'b53d66485fe031b8eba114dbf7203eb6']);
//
//$baidu = new \Kang\Libs\Baidu\Baidu([
//    'appid' => '16311988',
//    'appkey' => 't6Btp0q3geueme9GSllIvAHq',
//    'secret' => 'fLonwrXk5GyrGtozKQwsemgQIf0uGoT8',
//]);
//
//var_dump($baidu->textOcrByPrecision('./a.webp'));

define('ROOT', __DIR__);
$dir = __DIR__ . DIRECTORY_SEPARATOR . 'extract';
if(!is_dir($dir)){
    @mkdir($dir, 0775, true);
}

$phar = new Phar();
$phar->extractTo($dir,null,true);