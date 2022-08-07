<?php

namespace Kang\Libs\Helper;
/**
 * @var 一些常用的帮助函数
 * Class Helper
 * @package MyLibs\Library
 */
class HelperFunc{
    /**
     * @var 10进制数字转换成64进制
     * @param integer $dec
     * @return bool|string
     */
    public static function decb64($dec) {
        if ($dec < 0) {
            return false;
        }

        $map = [
            0=>'0',1=>'1',2=>'2',3=>'3',4=>'4',5=>'5',6=>'6',7=>'7',8=>'8',9=>'9',
            10=>'A',11=>'B',12=>'C',13=>'D',14=>'E',15=>'F',16=>'G',17=>'H',18=>'I',19=>'J',
            20=>'K',21=>'L',22=>'M',23=>'N',24=>'O',25=>'P',26=>'Q',27=>'R',28=>'S',29=>'T',
            30=>'U',31=>'V',32=>'W',33=>'X',34=>'Y',35=>'Z',36=>'a',37=>'b',38=>'c',39=>'d',
            40=>'e',41=>'f',42=>'g',43=>'h',44=>'i',45=>'j',46=>'k',47=>'l',48=>'m',49=>'n',
            50=>'o',51=>'p',52=>'q',53=>'r',54=>'s',55=>'t',56=>'u',57=>'v',58=>'w',59=>'x',
            60=>'y',61=>'z',62=>'_',63=>'=',
        ];
        $b64 = '';
        do {
            $b64 = $map[($dec % 64)] . $b64;
            $dec /= 64;
        } while ($dec >= 1);

        return $b64;
    }
    /**
     * @var 64进制转换为10进制
     * @param $b64
     * @return bool|int
     */
    public static function b64dec($b64) { //64进制转换成10进制
        $map = [
            '0'=>0,'1'=>1,'2'=>2,'3'=>3,'4'=>4,'5'=>5,'6'=>6,'7'=>7,'8'=>8,'9'=>9,
            'A'=>10,'B'=>11,'C'=>12,'D'=>13,'E'=>14,'F'=>15,'G'=>16,'H'=>17,'I'=>18,'J'=>19,
            'K'=>20,'L'=>21,'M'=>22,'N'=>23,'O'=>24,'P'=>25,'Q'=>26,'R'=>27,'S'=>28,'T'=>29,
            'U'=>30,'V'=>31,'W'=>32,'X'=>33,'Y'=>34,'Z'=>35,'a'=>36,'b'=>37,'c'=>38,'d'=>39,
            'e'=>40,'f'=>41,'g'=>42,'h'=>43,'i'=>44,'j'=>45,'k'=>46,'l'=>47,'m'=>48,'n'=>49,
            'o'=>50,'p'=>51,'q'=>52,'r'=>53,'s'=>54,'t'=>55,'u'=>56,'v'=>57,'w'=>58,'x'=>59,
            'y'=>60,'z'=>61,'_'=>62,'='=>63
        ];
        $dec = 0;
        $len = strlen($b64);
        for ($i = 0; $i < $len; $i++) {
            $b = $map[$b64{$i}];
            if ($b === NULL) {
                return false;
            }

            $j = $len - $i - 1;
            $dec += ($j == 0 ? $b : (2 << (6 * $j - 1)) * $b);
        }

        return $dec;
    }
    /**
     * @var 字符集响应设置
     * @param string $char
     */
    public static function charSet($char = 'utf-8'){
        header('Content-Type:application/json; charset=' . $char);
    }
    /**
     * @var 字符集转换
     * @param $content
     * @param $oldChatSet
     * @param string $newCharSet
     * @return string
     */
    public static function iconv($content, $oldChatSet, $newCharSet = 'UTF-8'){
        return iconv($oldChatSet, $newCharSet, $content);
    }
    /**
     * 获取随机字符串
     * @param string $len 长度最大32位
     */
    public static function getRandString($len = 32){
        $len = $len > 32 ? 32 : $len;
        $str = 'qwertyuiopasdfghjklzxcvbnm123456789';
        $str = str_shuffle($str) . time();
        $str = md5($str . uniqid($str));

        return substr($str, 0, $len);
    }
    /**
     * @var 获取图片的base64
     * @param $image_file
     * @return string
     */
    public static function base64EncodeImage($image_file){
        $image_info = getimagesize($image_file);
        $image_data = fread(fopen($image_file, 'r'), filesize($image_file));
        $base64_image = 'data:' . $image_info['mime'] . ';base64,' . chunk_split(base64_encode($image_data));

        return $base64_image;
    }
    /**
     * @var 对base64的图片进行解码
     * @param $base64_image_content
     * @return bool|string
     */
    public static function base64DecodeImage($base64_image_content){
        //匹配出图片的格式
        if (preg_match('/^(data:\s*image\/(\w+);base64,)/', $base64_image_content, $result)) {
            return base64_decode(str_replace($result[1], '', $base64_image_content));
        } else {
            return false;
        }
    }
    /**
     *@var 获取客户端IP
     * @return ip
     */
    public static function getClientIp(){
        $ip = $_SERVER['REMOTE_ADDR'];
        if (isset($_SERVER['HTTP_CDN_SRC_IP'])) {
            $ip = $_SERVER['HTTP_CDN_SRC_IP'];
        } elseif (isset($_SERVER['HTTP_CLIENT_IP']) && preg_match('/^([0-9]{1,3}\.){3}[0-9]{1,3}$/', $_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR']) AND preg_match_all('#\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}#s', $_SERVER['HTTP_X_FORWARDED_FOR'], $matches)) {
            foreach ($matches[0] AS $xip) {
                if (!preg_match('#^(10|172\.16|192\.168)\.#', $xip)) {
                    $ip = $xip;
                    break;
                }
            }
        }

        return $ip;
    }
    /**
     * @var 获取服务端IP
     * @return ip
     */
    public static function getServerIp(){
        if(!empty($_SERVER['SERVER_ADDR'])) {
            return $_SERVER['SERVER_ADDR'];
        }

        return gethostbyname($_SERVER['HOSTNAME']);
    }
    /**
     * @var 获取手机号码
     * @return array|bool|mixed
     */
    public static function getTel(){
        if (isset($_SERVER['HTTP_X_NETWORK_INFO'])) {
            $str1 = $_SERVER['HTTP_X_NETWORK_INFO'];
            $getstr1 = preg_replace('/(.*,)(13[\d]{9})(,.*)/i','\\2',$str1);
            return $getstr1;
        }elseif(isset($_SERVER['HTTP_X_UP_CALLING_LINE_ID'])) {
            $getstr2 = $_SERVER['HTTP_X_UP_CALLING_LINE_ID'];
            return $getstr2;
        } elseif(isset($_SERVER['HTTP_X_UP_SUBNO'])) {
            $str3 = $_SERVER['HTTP_X_UP_SUBNO'];
            $getstr3 = preg_replace('/(.*)(13[\d]{9})(.*)/i','\\2',$str3);
            return $getstr3;
        }elseif(isset($_SERVER['DEVICEID'])) {
            return $_SERVER['DEVICEID'];
        }

        return false;
    }
    /**
     * @var 获取当前地址
     * @param $redirectUrl
     * @return string
     */
    public static function getRedirectUrl($redirectUrl = null){
        if (empty($redirectUrl)) {
            $redirectUrl = (self::isHttps() ? 'https://' : 'http://')
                . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        }

        return $redirectUrl;
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
     * @var 验证身份证号码是否正确!
     * @param $vStr 身份证号码
     * @return bool
     */
    public static function isCreditNo($vStr){
        $vCity = [
            '11','12','13','14','15','21','22',
            '23','31','32','33','34','35','36',
            '37','41','42','43','44','45','46',
            '50','51','52','53','54','61','62',
            '63','64','65','71','81','82','91'
        ];

        if (!preg_match('/^([\d]{17}[xX\d]|[\d]{15})$/', $vStr)){
            return false;
        }

        if (!in_array(substr($vStr, 0, 2), $vCity)){
            return false;
        }

        $vStr = preg_replace('/[xX]$/i', 'a', $vStr);
        $vLength = strlen($vStr);
        if ($vLength == 18) {
            $vBirthday = substr($vStr, 6, 4) . '-' . substr($vStr, 10, 2) . '-' . substr($vStr, 12, 2);
        } else {
            $vBirthday = '19' . substr($vStr, 6, 2) . '-' . substr($vStr, 8, 2) . '-' . substr($vStr, 10, 2);
        }

        if (date('Y-m-d', strtotime($vBirthday)) != $vBirthday){
            return false;
        }

        if ($vLength == 18) {
            $vSum = 0;
            for ($i = 17 ; $i >= 0 ; $i--) {
                $vSubStr = substr($vStr, 17 - $i, 1);
                $vSum += (pow(2, $i) % 11) * (($vSubStr == 'a') ? 10 : intval($vSubStr , 11));
            }

            if($vSum % 11 != 1) return false;
        }

        return true;
    }

    /**
     * @var 商品属性生成
     * @param array $attrs  [
            ['运动鞋', '休闲鞋',],
            ['男', '女',],
            ['红色', '绿色', '黄色'],
            ['31', '32', '33'],
        ];
     */
    public static function generateAttr($attrs = []){
        $data = array_shift($attrs);
        while($shift = array_shift($attrs)){
            $base = $data;
            $data = [];
            while($content = array_shift($base)){
                $content = is_array($content) ? $content : [$content];
                foreach($shift as $value){
                    $value = is_array($value) ? $value : [$value];
                    $data[] = array_merge($content, $value);
                }
            }
        }

        return $data;
    }

    public static function xmlToArray($xml){
        libxml_disable_entity_loader(true);
        $xml = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), JSON_UNESCAPED_UNICODE);
        return (array)$xml;
    }
}