<?php
namespace Kang\Libs\Helper;

/**
 * @var curl请求
 * Class Curl
 * @package MyLibs\Help
 */
class Curl{
    const METHOD_POST = 'POST';
    const METHOD_GET = 'GET';

    public $charset = null; //UTF-8 输出编码

    /**
     * @return Curl
     */
    public static function getInstall(){
        if(self::$_install === null){
            self::$_install = new self();
        }

        return self::$_install;
    }
    /**
     * @var 单个请求
     * @param string $url 请求地址
     * @param string|array $data 提交的请求数据
     * @param string $method 请求方法
     * @param bool $isUseCommon 是否使用公共定义的请求部分
     * @return bool|mixed
     */
    public function request($url, $data = '', $method = self::METHOD_GET, $isUseCommon = true){
        $curl = $this->create($url, $method, $data, $isUseCommon);
        return $this->execute($curl);
    }

    /**
     * @var 批量执行
     * @param array $urls [ ['url'=> 'url', 'data' => [], 'method' => 'GET']]
     * @return mixed
     */
    public function requestBatch(array $urls,  $isUseCommon = true){
        $multi = curl_multi_init();
        $curls = [];
        foreach($urls as $key => $item){
            $url  = $item['url'];
            $data = isset($item['data']) ? $item['data'] : '';
            $method = isset($item['method']) ? $item['method'] : self::METHOD_GET;
            $curls[$key] = $this->create($url, $method, $data, $isUseCommon);
            curl_multi_add_handle($multi, $curls[$key]);
        }

        // 执行批处理句柄
        do {
            curl_multi_exec($multi, $active);
        } while ($active);

        foreach ($curls as $k => $curl) {
            $res[$k] = $this->execute($curl, false);
            curl_multi_remove_handle($multi, $curl);//释放资源
        }

        curl_multi_close($multi);
        return $res;
    }
    /**
     *  @var 设置CURL的 setopt
     * @param array $setopts
     * @param bool $append
     * @return $this
     */
    public function setSetopts(array $setopts, $append = false){
        $this->_setopts = $append ? $this->_setopts + $setopts : $setopts;
        return $this;
    }
    /**
     * @var 设置单个CURL的setopt
     * @param $option
     * @param $value
     * @return $this
     */
    public function setSetoptVaule($option, $value, $isCommon = false){
        if($isCommon){
            $this->_common[$option] = $value;
        }else{
            $this->_setopts[$option] = $value;
        }

        return $this;
    }
    /**
     * @var 设置Ssl的文件地址
     * @param string $sslCertPath 路径
     * @param string $sslKeyPath 路径
     * @param string $sslType ssl文件类型
     * @return $this
     */
    public function setSslCert($sslCertPath, $sslKeyPath, $sslType = 'PEM', $isCommon = true){
        $this->setSetoptVaule(CURLOPT_SSLCERTTYPE, $sslType, $isCommon);
        $this->setSetoptVaule(CURLOPT_SSLCERT, realpath('./' . $sslCertPath), $isCommon);
        $this->setSetoptVaule(CURLOPT_SSLKEY, realpath('./' . $sslKeyPath), $isCommon);
        return $this;
    }
    /**
     * @var 设置公共样式
     * @return array
     */
    public function setCommonSetopt(array $setopts = [], $append = true){
        if(!$append){
            $this->_common = [];
        }

        $this->_common = $this->_common + $setopts;
        return $this;
    }
    /**
     * @var 设置Header头部
     * @param array $header ['Content-Type' => 'application/x-www-form-urlencoded']
     * @return Curl
     */
    public function setHeader($header = [], $isAppend = false){
        if($isAppend === false){
            $this->_headers = [];
        }

        foreach ($header as $k => $v) {
            $this->_headers[] = is_string($k) ? sprintf('%s:%s', $k, $v) : $v;
        }

        return $this;
    }
    /**
     * @var 模拟CURL请求头部
     * @return $this
     */
    public function setRandHeader(){
        $ip_long = [
            ['607649792', '608174079'], //36.56.0.0-36.63.255.255
            ['1038614528', '1039007743'], //61.232.0.0-61.237.255.255
            ['1783627776', '1784676351'], //106.80.0.0-106.95.255.255
            ['2035023872', '2035154943'], //121.76.0.0-121.77.255.255
            ['2078801920', '2079064063'], //123.232.0.0-123.235.255.255
            ['-1950089216', '-1948778497'], //139.196.0.0-139.215.255.255
            ['-1425539072', '-1425014785'], //171.8.0.0-171.15.255.255
            ['-1236271104', '-1235419137'], //182.80.0.0-182.92.255.255
            ['-770113536', '-768606209'], //210.25.0.0-210.47.255.255
            ['-569376768', '-564133889'], //222.16.0.0-222.95.255.255
        ];

        $rand_key = mt_rand(0, 9);
        $ip = long2ip(mt_rand($ip_long[$rand_key][0], $ip_long[$rand_key][1]));
        $header = ["Connection: Keep-Alive",
            "Accept: text/html, application/xhtml+xml,
             */*",
            "Pragma: no-cache",
            "Accept-Language: zh-Hans-CN,zh-Hans;q=0.8,en-US;q=0.5,en;q=0.3",
            "User-Agent: Mozilla/5.0 (compatible; MSIE 10.0; Windows NT 6.2; WOW64; Trident/6.0)", 'CLIENT-IP:' . $ip, 'X-FORWARDED-FOR:' . $ip
        ];

        $this->setHeader($header);
        return $this;
    }

    private function __construct(){}
    /**
     * @var 设置CURL 请求模式
     * @param string $url
     * @param string $method
     * @param string|array $url
     * @return mixed
     */
    private function setUrlSetopt($url, $method, $data){
        $url = trim($url, '?');
        if($method == Curl::METHOD_GET && !empty($data)){
            $data = $data ? (is_array($data) ? http_build_query($data) : $data ) : '';
            $url = $url . '?' . $data;
        }

        $this->_setopts[CURLOPT_URL] = $url;
        if (stripos($url, "https://") !== FALSE) {
            $this->_setopts[CURLOPT_SSL_VERIFYPEER] = false;// 对认证证书来源的检查
            $this->_setopts[CURLOPT_SSL_VERIFYHOST] = false;// 从证书中检查SSL加密算法是否存在
            $this->_setopts[CURLOPT_SSLVERSION] = true;//CURL_SSLVERSION_TLSv1
        }

        if ($method == Curl::METHOD_POST) {
            $this->_setopts[CURLOPT_POST] = true;
            empty($data) OR $this->_setopts[CURLOPT_POSTFIELDS] = (is_array($data) ? http_build_query($data) : $data);
        }
    }
    /**
     * @var 获取创建对象
     * @return resource
     */
    private function create($url, $method, $data, $isCommon = true){
        $curl = curl_init();
        $this->setCommonSetopt([], $isCommon);
        $this->setUrlSetopt($url, $method, $data);
        if(!empty($this->_headers)){
            $this->setSetoptVaule(CURLOPT_HTTPHEADER, $this->_headers);
        }

        $setopts = $this->_setopts + $this->_common;
        foreach ($setopts as $option => $value) {
            curl_setopt($curl, $option, $value);
        }

        $this->_setopts = []; //清除上一次的调用
        return $curl;
    }

    /**
     * @var 获取执行结果
     * @param $isOnly bool 是否是单例请求
     * @return bool|mixed
     */
    protected function execute($curl, $isOnly = true){
        $result = $isOnly ? curl_exec($curl) : curl_multi_getcontent($curl); //执行操作
        if($result === false){
            $this->setError(curl_error($curl));
        }

        if($this->charset){
            $this->charset($result, curl_getinfo($curl), $this->charset);
        }

        if($isOnly){
            curl_close($curl); // 关闭CURL会话
        }

        return $result;
    }
    /**
     * @var 对获取的结果 进行编码转换
     * @param $curlInfo
     * @param $result
     * @param $out_charset
     * @return string
     */
    protected function charset(&$result, $curlInfo, $out_charset){
        $charSet = strtoupper(trim(substr($curlInfo['content_type'], strpos($curlInfo['content_type'], '=') + 1)));
        if(in_array($charSet, [
            'EXT/HTML',
        ])){
            return $result;
        }

        if($curlInfo['content_type'] && $charSet != strtoupper($out_charset)){
            $encode = mb_detect_encoding($result, ["ASCII",'UTF-8',"GB2312","GBK",'BIG5']);
            $result = mb_convert_encoding($result, $out_charset, $encode);
        }

        return $result;
    }

    private function setError($error){
        $this->_error = $error;
    }

    public function getError(){
        return $this->_error;
    }

    private $_headers = [];

    //公共的setopts
    private $_common = [
        CURLOPT_HEADER => 0, // 显示返回的Header区域内容
        CURLOPT_RETURNTRANSFER => 1, // 获取的信息以文件流的形式返回
        CURLOPT_FOLLOWLOCATION => true, // 使用自动跳转
        CURLOPT_AUTOREFERER => true, //自动设置Referer
        CURLOPT_TIMEOUT => 5,// 设置超时限制防止死循环
    ];

    private $_setopts = [];
    private static $_install = null;
    private $_error = null;
}
