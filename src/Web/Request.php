<?php

namespace Kang\Libs\Web;

/**
 * web请求
 * Class Request
 * @package Kang\Libs\Web
 */
class Request{

    public function header($name = null){
        if($this->_header === null){
            if (function_exists('apache_request_headers') && $result = apache_request_headers()) {
                $header = $result;
            } else {
                $header = [];
                $server = $_SERVER;
                foreach ($server as $key => $val) {
                    if (0 === strpos($key, 'HTTP_')) {
                        $key          = str_replace('_', '-', strtolower(substr($key, 5)));
                        $header[$key] = $val;
                    }
                }
                if (isset($server['CONTENT_TYPE'])) {
                    $header['content-type'] = $server['CONTENT_TYPE'];
                }
                if (isset($server['CONTENT_LENGTH'])) {
                    $header['content-length'] = $server['CONTENT_LENGTH'];
                }
            }

            $this->_header = $header;
        }

        if($name === null){
            return $this->_header;
        }

        $name = str_replace('_', '-', strtolower($name));
        return $this->_header[$name] ?? null;
    }

    public function method(){
        if($this->_method === false){
            if($this->header('REQUEST_METHOD')){
                $this->_method = $this->header('REQUEST_METHOD');
            } else if($this->header('HTTP_X_HTTP_METHOD_OVERRIDE')){
                $this->_method = $this->header('HTTP_X_HTTP_METHOD_OVERRIDE');
            }else{
                $this->method = $this->header('REQUEST_METHOD') ?: 'GET';
            }
        }

        return $this->_method;
    }

    private $_method = false;
    private $_header = null;
}
