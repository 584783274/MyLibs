<?php
namespace Kang\Libs\Helper;
/**
 * @var 加密安全控件 Rsa 加密
 * Class Aes
 * @package MyLibs\Safe
 */

class Rsa{
    /**
     * Rsa constructor.
     * @param array $config = ['key' => '密匙', 'path' => '密匙路径']
     */
    public function __construct(array $config = []){
        $this->_key = $config['key'] ?? file_get_contents($config['path']);
    }
    /**
     * @var 加密操作
     * @param $encrypted string
     * @param $type bool
     * @return string
     * @throws \Exception
     */
    public function encode($encrypted, $isPublic = false){
        if($isPublic){
            $this->_key = $this->getPublicKey();
            return $this->publicEncrypt($encrypted, false);
        }

        $this->_key = $this->getPrivateKey();
        return $this->privateEncrypt($encrypted, false);
    }
    /**
     * @var 解密操作
     * @param $encrypted string
     * @param $type bool
     * @return string
     * @throws \Exception
     */
    public function decode($encrypted, $isPublic = false){
        if($isPublic){
            $this->_key = $this->getPublicKey();
            return $this->publicDecrypt($encrypted, false);
        }

        $this->_key = $this->getPrivateKey();
        return $this->privateDecrypt($encrypted, false);
    }
    /**
     * 私钥加密
     * @param $data
     * @param bool $serialize 是为了不管你传的是字符串还是数组，都能转成字符串
     * @return string
     * @throws \Exception
     */
    public function privateEncrypt($data, $serialize = true){
        openssl_private_encrypt(
            $serialize ? serialize($data) : $data,
            $encrypted, $this->_key
        );

        if ($encrypted === false) {
            throw new \Exception('Could not encrypt the data.');
        }

        return $encrypted;
    }


    /**
     * 私钥解密
     * @param $data
     * @param bool $unserialize
     * @return mixed
     * @throws \Exception
     */
    public function privateDecrypt($data, $unserialize = true){
        openssl_private_decrypt($data, $decrypted, $this->_key);
        if ($decrypted === false) {
            throw new \Exception('Could not decrypt the data.');
        }

        return $unserialize ? unserialize($decrypted) : $decrypted;
    }

    /**
     * 公钥加密
     * @param $data
     * @param bool $serialize 是为了不管你传的是字符串还是数组，都能转成字符串
     * @return string
     * @throws \Exception
     */
    public function publicEncrypt($data, $serialize = true){
        openssl_public_encrypt(
            $serialize ? serialize($data) : $data,
            $encrypted, $this->_key
        );

        if ($encrypted === false) {
            throw new \Exception('Could not encrypt the data.');
        }

        return $encrypted;
    }


    /**
     * 公钥解密
     * @param $data
     * @param bool $unserialize
     * @return mixed
     * @throws \Exception
     */
    public function publicDecrypt($data, $unserialize = true){
        openssl_public_decrypt($data, $decrypted, $this->_key);
        if ($decrypted === false) {
            throw new \Exception('Could not decrypt the data.');
        }

        return $unserialize ? unserialize($decrypted) : $decrypted;
    }

    /**
     * @var 配置私钥
     * openssl_pkey_get_private这个函数可用来判断私钥是否是可用的，可用，返回资源
     * @return bool|resource
     */
    private function getPrivateKey(){
        return openssl_pkey_get_private($this->_key);
    }

    /**
     * @var 配置公钥
     * openssl_pkey_get_public这个函数可用来判断私钥是否是可用的，可用，返回资源
     * @return resource
     */
    public function getPublicKey(){
        return openssl_pkey_get_public($this->_key);
    }

    protected $_key; //string aes 加密key
}
