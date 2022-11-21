<?php
require_once './vendor/autoload.php';


//$wechat = new \Kang\Libs\WeChat\WeChat(['appid' => 'wx053536f0d2fab54f', 'appsecret' => 'b53d66485fe031b8eba114dbf7203eb6']);
////
//$baidu = new \Kang\Libs\Baidu\Baidu([
//    'appid' => '16311988',
//    'appkey' => 't6Btp0q3geueme9GSllIvAHq',
//    'secret' => 'fLonwrXk5GyrGtozKQwsemgQIf0uGoT8',
//]);
//
//var_dump($baidu->textOcrByPrecision('./a.webp'));

//$service = new \Kang\Libs\Center\Zookeeper\Service();

//$service->register();


$service = new \Kang\Libs\WeChat\WeChatEnterprise();

$service->corpid = "wwc43462436bdd414e";
$service->corpsecret = "audiqsj-7seNhS85G_tlfCEMIhS7-2HcnE8pD4xH_Zg";


var_dump($service->getAccessToken());

var_dump($service->getErrors());
