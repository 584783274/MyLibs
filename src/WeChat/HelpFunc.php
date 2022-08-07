<?php
namespace Kang\Libs\WeChat;

class HelpFunc{
    /**
     * @var 获取服务端IP
     * @return mixed
     */
    public static function spbillIp(){
        return  $_SERVER['SERVER_ADDR'];
    }
    /**
     * @var 获取Sha1签名结果
     * @param $data array 签名数组
     * @return string 签名值
     */
    public static function signSha1(array $data) {
        $Sign = sha1(self::ksortArrToString($data));
        return $Sign;
    }
    /**
     * @var 获取HMAC-SHA256签名结果
     * @param array $data
     * @param string $key
     * @return string
     */
    public static function signHmacSha256(array $data, string $key){
        $string = self::ksortArrToString($data);
        $string .=( '&key=' . $key);
        return strtoupper(hash_hmac('sha256', $string, $key));
    }
    /**
     * @var 获取MD5签名结果
     * @param array $data
     * @param string $key
     * @return string
     */
    public static function signMD5(array $data, string $key){
        $string = self::ksortArrToString($data);
        $string .=( '&key=' . $key);
        return strtoupper(md5($string));
    }
    /**
     * @var 对数组进行ksort排序转换为key=>value键值对字符串
     * @param array $data
     * @return string
     */
    public static function ksortArrToString(array $data){
        ksort($data);
        $paramstring = "";
        foreach ($data as $key => $value) {
            if (strlen($paramstring) == 0){
                $paramstring .= $key . "=" . $value;
            }else{
                $paramstring .= "&" . $key . "=" . $value;
            }
        }

        return $paramstring;
    }
    /**
     * 是否微信浏览器
     * @return bool
     */
    public static function isWechatBrower(){
        return (stripos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false);
    }

    /**
     * 是否是本地浏览
     * @return bool
     */
    public static function isRemoteHost(){
        $a = stripos($_SERVER["SERVER_NAME"], "localhost");
        $b = stripos($_SERVER["SERVER_NAME"], "127.0.0.1");
        $c = stripos($_SERVER["SERVER_NAME"], "192.168");
        return ($a === false && $b === false && $c === false);
    }

    /**
     * @var 判断是否为https
     * @return bool
     */
    public static function isHttps(){
        if (!empty($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) !== 'off') {
            return true;
        } elseif (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') {
            return true;
        } elseif (!empty($_SERVER['HTTP_FRONT_END_HTTPS']) && strtolower($_SERVER['HTTP_FRONT_END_HTTPS']) !== 'off') {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @var 获取当前地址
     * @param $redirectUrl
     * @return string
     */
    public static function getRedirectUrl($redirectUrl = null){
        if (empty($redirectUrl)) {
            $redirectUrl = (self::isHttps() ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        }

        return $redirectUrl;
    }
    /**
     * @var 微信sha1加密
     * @param array $tmpStr
     * @return string
     */
    public static function sha1(array $tmpStr = []){
        sort($tmpStr, SORT_STRING);
        $tmpStr = implode($tmpStr);
        return sha1($tmpStr);
    }

    /**
     * @var XML编码
     * @param mixed $data 数据
     * @param string $root 根节点名
     * @param string $item 数字索引的子节点名
     * @param string $attr 根节点属性
     * @param string $id 数字索引子节点key转换的属性名
     * @param string $encoding 数据编码
     * @return string
     */
    public static function xmlEncode($data, $root = 'xml', $item = 'item', $attr = '', $id = 'id', $encoding = 'utf-8'){
        if (is_array($attr)) {
            $_attr = [];
            foreach ($attr as $key => $value) {
                $_attr[] = "{$key}=\"{$value}\"";
            }
            $attr = implode(' ', $_attr);
        }

        $attr = trim($attr);
        $attr = empty($attr) ? '' : " {$attr}";
        $xml = "<{$root}{$attr}>";
        $xml .= self::arrayToXml($data);
        $xml .= "</{$root}>";

        return $xml;
    }

    /**
     * 数据XML编码
     * @param mixed $data 数据
     * @return string
     */
    public static function arrayToXml($data){
        $xml = '';
        foreach ($data as $key => $val) {
            is_numeric($key) && $key = "item id=\"$key\"";
            $xml .= "<$key>";
            $xml .= (is_array($val) || is_object($val)) ? self::arrayToXml($val) : self::xmlSafeStr($val);
            list($key,) = explode(' ', $key);
            $xml .= "</$key>";
        }

        return $xml;
    }
    /**
     * @var 微信安全
     * @param $str
     * @return string
     */
    public static function xmlSafeStr($str){
        return '<![CDATA[' . preg_replace("/[\\x00-\\x08\\x0b-\\x0c\\x0e-\\x1f]/", '', $str) . ']]>';
    }
    /**
     * 将XML 转换为数组!
     * @param $xml
     * @return array
     */
    public static function xmlToArray($xml){
        libxml_disable_entity_loader(true);
        $xml = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
        return (array)$xml;
    }
    /**
     * 获取随机字符串
     * @param string $len 长度最大32位
     */
    public static function getRandString($len = 32){
        $len = $len > 32 ? 32 : ($len < 6 ? 6 : $len);
        $str = 'qwertyuiopasdfghjklzxcvbnm1230456789AQZWSXEDCRFVTGBYHNUJMIKLOP';
        $str = str_shuffle($str) . time();
        $str = md5($str . uniqid($str));
        return substr($str, 0, $len);
    }
}