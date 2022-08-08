<?php
/**
 * https://cloud.baidu.com/doc/index.html
 */
namespace Kang\Libs\Baidu;

use Kang\Libs\Baidu\Behavior\BaiduBehavior;
use Kang\Libs\Base\Behavior;
use Kang\Libs\Base\Component;
use Kang\Libs\Base\Event;
use Kang\Libs\Helper\Curl;

/**
 * @var 百度接口使用
 * Class Baidu
 * @property Behavior $baiduBehavior 监听当前类触发的事件
 * @property string $secret //fLonwrXk5GyrGtozKQwsemgQIf0uGoT8
 * @property string $appid //16311988
 * @property string $appkey //t6Btp0q3geueme9GSllIvAHq
 * @package MyLibs\Baidu
 */
class Baidu extends Component {
    const API_BASE_URL = 'https://aip.baidubce.com/'; //基础路由端口

    const EVENT_AFTER_REFRESH_ACCESS_TOKEN = 'afterRefreshAccessToken'; //监听普通公众号跟授权第三方时公众号的AccessToken刷新事件
    const EVENT_BEFORE_REFRESH_ACCESS_TOKEN = 'beforeRefreshAccessToken'; //监听普通公众号跟授权第三方时公众号的AccessToken刷新事件
    const EVENT_REFRESH_JS_API_TICKET = 'refreshJsapiTicket'; //监听刷新事件
    const EVENT_ACCESS_TOKEN_ERROR = 'errorAccessToken';
    const EVENT_ACCESS_TOKEN_CACHE_GET = 'AccessTokenGetCache'; //获取AccessToken缓存
    const EVENT_lOG = 'log'; //日志事件

    public function behaviors(): array{
        return [
            $this->baiduBehavior,
        ];
    }
    /**
     * @var 高精度图片文字识别
     * @param string $imagePath
     * @param string $pdfFilePath
     * @param string $imageUrl
     * @return bool|mixed
     */
    public function textOcrByPrecision($imagePath = '', $pdfFilePath = '', $imageUrl = ''){
        if($imagePath && file_exists($imagePath)){
            $data['image'] = base64_encode(file_get_contents($imagePath));
        }elseif ($pdfFilePath && file_exists($pdfFilePath)){
            $data['pdf_file'] = base64_encode(file_get_contents($pdfFilePath));
        }elseif ($imageUrl){
            $data['url'] = $imageUrl;
        }

        return $this->post('rest/2.0/ocr/v1/accurate_basic?access_token=', $data, true);
    }
    /**
     * @var 获取AccessToken
     * @return mixed
     */
    public function getAccessToken(){
        if(empty($this->_config['accessToken'])){
            $event = new Event();
            $this->trigger(self::EVENT_ACCESS_TOKEN_CACHE_GET, $event);
            if(!empty($event->data)){
                if(is_string($event->data)){
                    $this->_config['accessToken'] = $event->data;
                }elseif (isset($event->data['accessToken'])){
                    $this->_config['accessToken'] = $event->data['accessToken'];
                }
            }
        }

        if(empty($this->_config['accessToken'])){
            $this->_config['accessToken'] = $this->refreshAccessToken();
        }

        return $this->_config['accessToken'];
    }
    /**
     * @var 刷新AccessToken
     * @return bool|mixed
     */
    public function refreshAccessToken(){
        $event = new Event();
        $event->sender = $this;
        $this->trigger(self::EVENT_BEFORE_REFRESH_ACCESS_TOKEN, $event);
        if($event->handled === true){
            return $event->data['access_token'] ?? $event->data;
        }

        $url = 'oauth/2.0/token?';
        $data['grant_type'] = 'client_credentials';
        $data['client_id'] = $this->appkey;
        $data['client_secret'] = $this->secret;
        $result = self::get($url, $data);
        if(!$result || !isset($result['access_token'])){
            return false;
        }

        $event->data = $result;
        $this->trigger(self::EVENT_AFTER_REFRESH_ACCESS_TOKEN, $event);
        return $result['access_token'];
    }

    public function post($url, $data = [], $autoToken = false){
       return $this->request($url, Curl::METHOD_POST, $autoToken, $data);
    }

    public function get($url, $data = [], $autoToken = false){
        if(!empty($data)){
            $url .= http_build_query($data);
        }

        return $this->request($url, Curl::METHOD_GET, $autoToken, null, []);
    }

    public function request($url, $method, $autoToken = false, $data = null, $headers = ['Content-Type' => 'application/x-www-form-urlencoded']){
        $url = self::API_BASE_URL . $url;
        $event = new Event();
        $event->data['url'] = $url;
        $event->data['method'] = $method;
        $event->data['data'] = $data;
        $this->trigger(self::EVENT_lOG, $event);

        $curl = Curl::getInstall();
        if(!empty($headers)){
            $curl->setHeader($headers, false);
        }

        if($autoToken){
            $url .= $this->getAccessToken();
        }

        $result = $curl->request($url, $data, $method);
        if($result === false){
            return $this->setErrors(-1, $curl->getError());
        }

        if(!$result = $this->responseResult($result)){
            if($this->getErrorCode() == 42001){
                $this->setAccessToken('');
                $this->trigger(self::EVENT_ACCESS_TOKEN_ERROR, $event);
                if($this->getAccessToken() && $autoToken){
                    return $this->request($url, $method, $data, $autoToken);
                }
            }

            return false;
        }

        return $result;
    }

    private function responseResult($result){
        $result = json_decode($result, JSON_UNESCAPED_UNICODE);

        return $result;
    }

    public function setAccessToken($value){
        $this->_config['accessToken'] = $value;
        return $this;
    }
    public function setAppid ($value){
        $this->_config['appid'] = $value;
        return $this;
    }
    public function setSecret($value){
        $this->_config['secret'] = $value;
        return $this;
    }
    public function setAppkey($value){
        $this->_config['appkey'] = $value;
        return $this;
    }
    public function setBaiduBehavior(Behavior $behavior){
        $this->_config['behavior'] = $behavior;
        return $this;
    }
    public function getBaiduBehavior(){
        return $this->_config['behavior'] ?? BaiduBehavior::class;
    }
}