<?php

namespace Kang\Libs\Helper\Security;

/**
 * @see https://www.jianshu.com/p/0dcbcfa08ca5
 * 配合前端 safe.js 的使用 对数据进行加解密 又名 CryptoJS
 * 前端加密用法 Safe.encryption(要加密的数据, 密匙, 加密方式);  获取结果
 * 前端解密用法 Safe.decrypt(要加密的数据, 密匙);  获取结果
 * 后台加密用法 new (Safe())->encrypt(传入要加密的数据);
 * 后台签名验证 if(new (Safe())->validateSign(加密包))
 * 后台解密方法 (new (Safe())->->decrypt(加密包))
 */

class Safe{
    const METHOD_BASE64 = 'base64';
    const METHOD_ASE = 'ase';//php mcrypt | openssl
    const METHOD_RSA = 'rsa';  //php Rsa

    public function __construct($key = 'KANG2008', $encryption = self::METHOD_ASE){
        $this->_encryption = $encryption;
        $this->_key = $key;
        $this->init();
    }
    /**
     * @var 加密
     * @param $pack
     * @return string
     * @throws \Exception
     */
    public function encode(&$pack = []){
        $pack = json_encode($pack, JSON_UNESCAPED_UNICODE);
        switch($this->_encryption){
            case self::METHOD_BASE64 :
                $pack = $this->base64Encode($pack, $this->_key);
                break;
            case self::METHOD_ASE :
                $pack = $this->isOpenssl ? $this->opensslEncode($pack) : $this->mcryptEncode($pack);
                $pack = base64_encode($pack);
                break;
            case self::METHOD_RSA :
                break;
            default :
                return $pack;
        }

        $data['encryption'] =  $this->_encryption;
        $data['pack'] =  $pack;
        $data['timestamp'] =  time();
        $data['sign'] = $this->getSign($data);

        return $data;
    }
    /**
     * @var 解密操作
     * @param $encrypted
     * @return string
     * @throws \Exception
     */
    public function decode($encrypted){
        $pack = is_string($encrypted) ? $encrypted : ($encrypted['pack'] ?? '');
        switch($this->_encryption){
            case self::METHOD_BASE64 :
                $pack = $this->base64Decode($pack, $this->_key);
                break;
            case self::METHOD_ASE :
                $pack = base64_decode($pack);
                $pack = $this->isOpenssl ? $this->opensslDecode($pack) : $this->mcryptDecode($pack);
                break;
            case self::METHOD_RSA :
                break;
            default :
                return $encrypted;
        }

        return json_decode($pack, JSON_UNESCAPED_UNICODE);
    }

    public function validateSign($data){
        $sign = $data['sign'];
        unset($data['sign']);
        return $sign == $this->getSign($data);
    }

    public function getSign(array $data){
        $data['key'] = $this->_key;
        ksort($data);
        $string = '';
        foreach($data as $key => $value){
            $string .= ($key . '=' . $value) . '&';
        }

        $string = trim($string, '&');
        return md5($string);
    }

    protected function mcryptEncode($encrypted){
        try{
            return mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $this->_ase_key, $encrypted, MCRYPT_MODE_CBC, $this->_ase_iv);
        }catch(\Exception $e){
            throw new \Exception('加密失败!');
        }
    }

    protected function mcryptDecode($encrypted){
        try{
            $decrypted = mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $this->_ase_key, $encrypted, MCRYPT_MODE_CBC, $this->_ase_iv);
            $decrypted = $decrypted ? $decrypted : mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $this->_ase_key, base64_decode($encrypted), MCRYPT_MODE_CBC, $this->_ase_iv);
            $decrypted = rtrim($decrypted, "\0");
            return  $decrypted;
        }catch(\Exception $e){
            throw new \Exception('解密失败!');
        }
    }

    protected function opensslEncode($encrypted){
        try{
            return openssl_encrypt($encrypted, "AES-128-CBC", $this->_ase_key, OPENSSL_RAW_DATA, $this->_ase_iv);
        }catch(\Exception $e){
            throw new \Exception('加密失败!');
        }
    }

    protected function opensslDecode($encrypted){
        try{
            $result = openssl_decrypt($encrypted, 'AES-128-CBC', $this->_ase_key, OPENSSL_RAW_DATA, $this->_ase_iv);
            return $result ? $result :  openssl_decrypt(base64_decode($encrypted), 'AES-128-CBC', $this->_ase_key, OPENSSL_RAW_DATA, $this->_ase_iv);
        }catch(\Exception $e){
            throw new \Exception('解密失败!');
        }
    }

    protected function base64Encode($encrypted, $key){
        return base64_encode($encrypted . $key);
    }

    protected function base64Decode($encrypted, $key){
        $encrypted = base64_decode($encrypted);
        return rtrim($encrypted, $key);
    }

    protected function init(){
        $this->_ase_key = md5($this->_key);
        $this->_ase_iv = substr($this->_ase_key, 0, 16);
        $this->_ase_key = substr($this->_ase_key, 16);
        $this->isOpenssl = function_exists('openssl_decrypt');
    }

    private $isOpenssl;
    private $_encryption;
    private $_key;
    private $_ase_key;
    private $_ase_iv;
}
