<?php

namespace Kang\Libs\Helper\Security;

/**
 * @var 证书
 * Class SecurityCertificate
 * @package Kang\Libs\Helper\Security
 */

class SecurityCertificate implements SecurityInterface{
    /**
     * @var 加密操作
     * @param $encrypted
     * @param $key
     * @param bool $isPublic
     * @return mixed|string
     * @throws \Exception
     */
    public function encode($encrypted, $key, $isPublic = false){
        if($isPublic){
            return $this->publicEncrypt($encrypted, $this->getPublicKey($key));
        }

        return $this->privateEncrypt($encrypted, $this->getPrivateKey($key));
    }
    /**
     * @var 解密操作
     * @param $encrypted string
     * @param $type bool
     * @return string
     * @throws \Exception
     */
    public function decode($encrypted, $key, $isPublic = false){
        if($isPublic){
            return $this->publicDecrypt($encrypted, $this->getPublicKey($key));
        }

        return $this->privateDecrypt($encrypted, $this->getPrivateKey($key));
    }
    /**
     * @var 获取签名
     * @param $str
     * @param $privateKey
     * @return string
     */
    public function sign($str, $privateKey){
        $key = $this->getPrivateKey($privateKey);
        openssl_sign($str, $signature, $key);
        openssl_free_key($key);
        $sign = base64_encode($signature);

        return $sign;
    }
    /**
     * @var 私钥加密
     * @param $data
     * @param $key
     * @param int $padding
     * @return mixed
     */
    public function privateEncrypt($data, $key, $padding = OPENSSL_PKCS1_PADDING){
        openssl_private_encrypt($data, $encrypted, $key, $padding);
        openssl_free_key($key);
        return $encrypted;
    }
    /**
     * 私钥解密
     * @param $data
     * @param bool $unserialize
     * @return mixed
     * @throws \Exception
     */
    public function privateDecrypt($data, $key, $padding = OPENSSL_PKCS1_PADDING){
        openssl_private_decrypt($data, $decrypted, $key, $padding);
        openssl_free_key($key);
        return $decrypted;
    }
    /**
     * 公钥加密
     * @param $data
     * @param bool $serialize 是为了不管你传的是字符串还是数组，都能转成字符串
     * @return string
     * @throws \Exception
     */
    public function publicEncrypt($data, $key, $padding = OPENSSL_PKCS1_PADDING){
        openssl_public_encrypt($data, $encrypted, $key, $padding);
        openssl_free_key($key);
        return $encrypted;
    }
    /**
     * 公钥解密
     * @param $data
     * @param bool $unserialize
     * @return mixed
     * @throws \Exception
     */
    public function publicDecrypt($data, $key, $padding = OPENSSL_PKCS1_PADDING){
        openssl_public_decrypt($data, $decrypted, $key, $padding);
        openssl_free_key($key);
        return $decrypted;
    }
    /**
     * @var 配置私钥
     * openssl_pkey_get_private这个函数可用来判断私钥是否是可用的，可用，返回资源
     * @return bool|resource
     */
    private function getPrivateKey($privateKey){
        $privateKey = "-----BEGIN RSA PRIVATE KEY-----\n" .
            wordwrap($privateKey, 64, "\n", true) .
            "\n-----END RSA PRIVATE KEY-----";

        return openssl_pkey_get_private($privateKey);
    }
    /**
     *@var 配置公钥
     * @param $key
     * @return resource
     */
    public function getPublicKey($publicKey){
        $publicKey = "-----BEGIN PUBLIC KEY-----\n" .
            wordwrap($publicKey, 64, "\n", true) .
            "\n-----END PUBLIC KEY-----";

        return openssl_pkey_get_public($publicKey);
    }
}
