<?php
namespace Kang\Libs\WeChat;

use Kang\Libs\Base\Behavior;
use Kang\Libs\Base\Component;
use Kang\Libs\Base\Event;
use Kang\Libs\Helper\Curl;
use Kang\Libs\WeChat\Behavior\WeChatBehavior;

/**
 * Class WeChat
 * @property \Kang\Libs\Base\Behavior $wechatBehavior 监听当前类触发的事件
 * @property string $appid  微信 appid
 * @property string $appsecret  微信 appsecret
 * @property string $token  微信消息的token
 * @property string $accessToken  微信公众号的accessToken
 * @property string $encodingAESKey  微信的消息加密密匙
 * @property string $mch_id  微信支付商户ID
 * @property string $mch_key  微信支付商户KEY
 * @property string $sslCertPath  证书的相对路径 或者绝对路径
 * @property string $sslKeyPath; 证书的相对路径 或者绝对路径
 * @property string $component_appid  第三方平台的appid
 * @property string $component_appsecret  第三方平台的appsecret
 * @property string $component_token  第三方平台的消息校验Token
 * @property string $component_encodingAESKey  第三方平台的消息加密密匙
 * @property string $component_access_token  第三方平台的AccessToken
 * @property string $component_verify_ticket  第三方平台的component_verify_ticket票据
 * @property string $authorizer_appid 授权方 appid
 * @property string $authorizer_access_token 授权方令牌
 * @property string $authorizer_refresh_token 授权方的刷新令牌
 * @package Kang\Libs\WeChat
 */
class WeChat extends Component {
    const EVENT_AFTER_REFRESH_ACCESS_TOKEN = 'afterRefreshAccessToken'; //监听普通公众号跟授权第三方时公众号的AccessToken刷新事件
    const EVENT_BEFORE_REFRESH_ACCESS_TOKEN = 'beforeRefreshAccessToken'; //监听普通公众号跟授权第三方时公众号的AccessToken刷新事件
    const EVENT_REFRESH_JS_API_TICKET = 'refreshJsapiTicket'; //监听刷新事件
    const EVENT_ACCESS_TOKEN_ERROR = 'errorAccessToken'; //监听AccessToken发生 42001|40001错误
    const EVENT_lOG = 'log'; //日志事件
    const EVENT_REFRESH_COMPONENT_ACCESS_TOKEN = 'componentAccessToken'; //监听第三方平台的AccessToken 刷新
    const EVENT_ACCESS_TOKEN_CACHE_GET = 'AccessTokenGetCache'; //获取AccessToken缓存



    public function behaviors(): array{
        return [
            $this->wechatBehavior,
        ];
    }


    //--------------------------openApi管理--------------------------------//
    /**
     * @var 清空api的调用quota
     * @param string $appid
     * @return bool
     */
    public function clearQuota($appid = ''){
        $data['appid'] = $appid ? $appid : $this->appid;
        if(!$this->httpPost(self::API_CLEAR_COUNT, $data, true)){
            return false;
        }

        return true;
    }

    //--------------------------openApi管理--------------------------------//


    //--------------------------订阅通知接口--------------------------------//
    /**
     * @var 添加模板
     * @param $tid
     * @param array $kidList
     * @param $sceneDesc
     * @return bool|mixed
     */
    public function subscribeNoticeByCreate($tid, array $kidList, $sceneDesc){
        $data['tid'] = $tid;
        $data['kidList'] = $kidList;
        $data['sceneDesc'] = $sceneDesc;
        if(!$result = $this->httpPost(self::API_TEMPLATE_MINI_ADD, $data, true)){
            return false;
        }

        return $result['priTmplId'];
    }
    /**
     * @var 删除模板
     * @param $priTmplId
     * @return bool
     */
    public function subscribeNoticeByDel($priTmplId){
        $data['priTmplId'] = $priTmplId;
        if(!$result = $this->httpPost(self::API_TEMPLATE_MINI_DEL, $data, true)){
            return false;
        }

        return true;
    }


    //--------------------------订阅通知接口--------------------------------//

    
    //------------------------------接口归零-智能接口--------------------------------//
    /**
     * @var 接口调用数进行清零
     * @param string $appid
     * @return bool|void
     */
    /**
     * @var 长链接转短连接
     * @param $longUrl
     * @return bool|void
     */
    public function shortUrl($longUrl){
        $data['action']   = 'long2short';
        $data['long_url'] = $longUrl;
        if(!$result = $this->httpPost(self::API_SHOR_URL, $data, true)){
            return false;
        }

        return $result['short_url'];
    }
    /**
     * @var 身份证识别
     * @param $imgUrl string 图片路由地址
     */
    public function ocrIdCard($imgUrl){
        $url  = self::API_OCR_ID_CARD;
        $url .=  'img_url=' . $imgUrl;
        $url .=  '&access_token=';

        return $this->httpGet($url, null, true);
    }
    /**
     * @var 银行卡OCR识别
     * @param $imgUrl string 图片路由地址
     * @param $type   照片正反 photo | MODE
     */
    public function ocrBankCard($imgUrl){
        $url = self::API_OCR_BANK_CARD_URL ;
        $url .=  '?img_url=' . $imgUrl;
        $url .=  '&access_token=';

        return $this->httpGet($url, null, true);
    }
    /**
     * @var 行驶证OCR识别接口
     * @param $imgUrl string 图片路由地址
     * @param $type   照片正反 photo | MODE
     */
    public function ocrDriving($imgUrl){
        $url  = self::API_OCR_DRIVING_URL;
        $url .=  '?img_url=' . $imgUrl;
        $url .=  '&access_token=';

        return $this->httpGet($url, null, true);
    }
    /**
     * @var 行业设置
     * @param $industry_id1
     * @param $industry_id2
     * @return bool|void
     */
    public function setIndustry($industry_id1, $industry_id2){
        $data['industry_id1'] = $industry_id1;
        $data['industry_id2'] = $industry_id2;

        return $this->httpPost(self::API_INDUSTRY_SET, $data, true);
    }
    /**
     * @var 行业设置查询
     * @return bool|void
     */
    public function industryByFind(){
        return $this->httpGet(self::API_INDUSTRY_GET, null, true);
    }
    //------------------------------接口归零-智能接口--------------------------------//

    //---------------------------------------配置信息-----------------------------------//
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

        $url    = self::API_TOKEN_URL;
        $url   .= 'appid=' . $this->appid;
        $url   .= '&secret=' . $this->appsecret;
        $url   .= '&grant_type=client_credential';
        $result = $this->httpGet($url);
        if(!$result || !isset($result['access_token'])){
            return false;
        }

        $event->data = $result;
        $this->trigger(self::EVENT_AFTER_REFRESH_ACCESS_TOKEN, $event);
        return $result['access_token'];
    }
    //--------------------------自定义菜单--------------------------------//

    public function setWechatBehavior(Behavior $behavior){
        $this->_config['behavior'] = $behavior;
        return $this;
    }

    public function getWechatBehavior(){
        return $this->_config['behavior'] ?? WeChatBehavior::class;
    }

    /**
     * @var 设置微信 appid
     * @param string $appid
     * @return $this
     */
    public function setAppid($appid){
        $this->_config['appid'] = $appid;
        return $this;
    }
    /**
     * @var 设置微信 appsecret
     * @param string $appsecret
     * @return $this
     */
    public function setAppsecret($appsecret){
        $this->_config['appsecret'] = $appsecret;
        return $this;
    }
    /**
     * @var 设置 未失效的令牌 accessToken
     * @param string $accessToken
     * @return $this
     */
    public function setAccessToken($accessToken){
        $this->_config['accessToken'] = $accessToken;
        return $this;
    }
    /**
     * @var 设置微信消息的token
     * @param string $token
     * @return $this
     */
    public function setToken($token){
        $this->_config['token'] = $token;
        return $this;
    }
    /**
     * @var $encodingAESKey  微信的消息加密密匙
     * @param string $encodingAESKey
     * @return $this
     */
    public function setEncodingAESKey($encodingAESKey){
        $this->_config['encodingAESKey'] = $encodingAESKey;
        return $this;
    }
    /**
     * @var 设置微信支付商户ID
     * @param string $mch_id
     * @return $this
     */
    public function setMch_id($mch_id){
        $this->_config['mch_id'] = $mch_id;
        return $this;
    }
    /**
     * @var 设置微信支付商户KEY
     * @param string $mch_key
     * @return $this
     */
    public function setMch_key($mch_key){
        $this->_config['mch_key'] = $mch_key;
        return $this;
    }
    /**
     * @var 证书的相对路径 或者绝对路径
     * @param string $path
     * @return $this
     */
    public function setSslCertPath($path){
        $this->_config['sslCertPath'] = $path;
        return $this;
    }
    /**
     * @var 证书的相对路径 或者绝对路径
     * @param string $path
     * @return $this
     */
    public function setSslKeyPath($path){
        $this->_config['sslKeyPath'] = $path;
        return $this;
    }
    /**
     * @var 设置第三方平台的 $appid
     * @param string $component_appid
     * @return $this
     */
    public function setComponent_appid($appid){
        $this->_config['component_appid'] = $appid;
        return $this;
    }
    /**
     * @var 设置第三方平台的 $appsecret
     * @param string $appsecret
     * @return $this
     */
    public function setComponent_appsecret($appsecret){
        $this->_config['component_appsecret'] = $appsecret;
        return $this;
    }
    /**
     * @var 设置第三方平台的AccessToken
     * @param string $componentAccessToken
     * @return $this
     */
    public function setComponent_access_token($accessToken){
        $this->_config['component_access_token'] = $accessToken;
        return $this;
    }
    /**
     * @var 微信推送给给第三方平的component_verify_ticket票据
     * @param string $component_verify_ticket
     * @return $this
     */
    public function setComponent_verify_ticket($verify_ticket){
        $this->_config['component_verify_ticket'] = $verify_ticket;
        return $this;
    }
    /**
     * @var 授权方的access_token令牌
     * @param string $authorizer_access_token
     * @return $this
     */
    public function setAuthorizer_access_token($authorizer_access_token){
        $this->_config['authorizer_access_token'] = $authorizer_access_token;
        return $this;
    }
    /**
     * @var 授权方的 appid
     * @param string $authorizer_appid
     * @return $this
     */
    public function setAuthorizer_appid($authorizer_appid){
        $this->_config['authorizer_appid'] = $authorizer_appid;
        return $this;
    }
    /**
     * @var 授权方的刷新令牌
     * @param string $authorizer_refresh_token
     * @return $this
     */
    public function setAuthorizer_refresh_token($authorizer_refresh_token){
        $this->_config['authorizer_refresh_token'] = $authorizer_refresh_token;
        return $this;
    }
    //---------------------------------配置信息------------------------------------------------//

    /**
     * @var GET请求
     * @param string $url 请求地址
     * @param null $data 请求数据
     * @param bool $autoToken 是否自动获取
     * @param bool $useCert 是否需要证书
     * @return bool|void
     */
    public function httpGet($url, $data = NULL, $autoToken = false, $useCert = false){
        return $this->httpRequest($url, Curl::METHOD_GET, $data, $autoToken, false, $useCert);
    }
    /**
     * @var curl POST 请求
     * @param $url
     * @param null $data
     * @param bool $autoToken
     * @param bool $useCert
     * @return bool|void
     */
    public function httpPost($url, $data = NULL, $autoToken = false, $isFile = false, $useCert = false){
        return $this->httpRequest($url, Curl::METHOD_POST, $data, $autoToken, $isFile, $useCert);
    }
    /**
     * @var curl请求
     * @param string $url 请求地址
     * @param string $method 请求方法
     * @param null   $data 请求数据
     * @param bool   $autoToken  是否自动获取 AccessToken
     * @param bool   $useCert 是否增加安全证书
     * @return bool|void
     */
    public function httpRequest($url, $method, $data = NULL, $autoToken = false, $isFile = false, $useCert = false){
        $event = new Event();
        $event->data['url'] = $url;
        $event->data['method'] = $method;
        $event->data['data'] = $data;
        $this->trigger(self::EVENT_lOG, $event);

        $this->parseRequestData($data, $method, $isFile);
        $token = '';
        if ($autoToken && !$token = $this->accessToken) {
            return false;
        }

        $url .= $token;
        $curl = Curl::getInstall();
        !$useCert OR $curl->setSslCert($this->sslCertPath, $this->sslKeyPath, 'PEM', false);

        $result = $curl->request($url, $data, $method);
        if($result === false){
            return $this->setErrors(-1, $curl->getError());
        }

        if(!$result = $this->responseResult($result)){
            if($this->getErrorCode() == 42001){
                $this->setAccessToken('');
                $this->trigger(self::EVENT_ACCESS_TOKEN_ERROR, $event);
                if($this->getAccessToken() && $autoToken){
                    return $this->httpRequest($url, $method, $data, $autoToken, $isFile, $useCert);
                }
            }

            return false;
        }

        return $result;
    }

    private function parseRequestData(&$data, $method, $isFile){
        if(!empty($data) && $method == Curl::METHOD_POST && $isFile == false){
            $data = $isFile ? $data : (is_array($data) ? json_encode($data, JSON_UNESCAPED_UNICODE) : $data);
        }
    }

    private function responseResult($result){
        $result = json_decode($result, JSON_UNESCAPED_UNICODE);
        if($result === null){
            return $result;
        }

        if(isset($result['errcode']) && $result['errcode'] != 0){
            $errCode = $result['errcode'];
            $errMsg = $result['errmsg'] ?? '未知错误';
            if(isset(self::$_errorsMessage[$errCode])){
                $errMsg = self::$_errorsMessage[$errCode];
            }

            return $this->setErrors($errCode, $errMsg);
        }

        return $result;
    }
    /**
     * For weixin server validation
     */
    private function checkSignature($str = '') {
        $signature = isset($_GET["signature"]) ? $_GET["signature"] : '';
        $signature = isset($_GET["msg_signature"]) ? $_GET["msg_signature"] : $signature; //如果存在加密验证则用加密验证段
        $timestamp = isset($_GET["timestamp"]) ? $_GET["timestamp"] : '';
        $nonce = isset($_GET["nonce"]) ? $_GET["nonce"] : '';

        $token = $this->token;
        $tmpArr = [$token, $timestamp, $nonce, $str];
        sort($tmpArr, SORT_STRING);
        $tmpStr = implode($tmpArr);
        $tmpStr = sha1($tmpStr);
        if ($tmpStr == $signature) {
            return true;
        } else {
            return false;
        }
    }
    /**
     * XML编码
     * @param mixed $data 数据
     * @param string $root 根节点名
     * @param string $item 数字索引的子节点名
     * @param string $attr 根节点属性
     * @param string $id   数字索引子节点key转换的属性名
     * @param string $encoding 数据编码
     * @return string
     */
    public function xml_encode($data, $root = 'xml', $item = 'item', $attr = '', $id = 'id', $encoding = 'utf-8') {
        if (is_array($attr)) {
            $_attr = array();
            foreach ($attr as $key => $value) {
                $_attr[] = "{$key}=\"{$value}\"";
            }
            $attr = implode(' ', $_attr);
        }

        $attr = trim($attr);
        $attr = empty($attr) ? '' : " {$attr}";
        $xml = "<{$root}{$attr}>";
        $xml .= self::data_to_xml($data, $item, $id);
        $xml .= "</{$root}>";

        return $xml;
    }
    /**
     * 数据XML编码
     * @param mixed $data 数据
     * @return string
     */
    public static function data_to_xml($data) {
        $xml = '';
        foreach ($data as $key => $val) {
            is_numeric($key) && $key = "item id=\"$key\"";
            $xml .= "<$key>";
            $xml .= ( is_array($val) || is_object($val)) ? self::data_to_xml($val) : self::xmlSafeStr($val);
            list($key, ) = explode(' ', $key);
            $xml .= "</$key>";
        }
        return $xml;
    }
    /**
     * @param $str
     * @return string
     */
    public static function xmlSafeStr($str) {
        return '<![CDATA[' . preg_replace("/[\\x00-\\x08\\x0b-\\x0c\\x0e-\\x1f]/", '', $str) . ']]>';
    }
    /**
     * xml格式加密，仅请求为加密方式时再用
     */
    private function generate($encrypt, $signature, $timestamp, $nonce) {
        //格式化加密信息
        $format = "<xml>
<Encrypt><![CDATA[%s]]></Encrypt>
<MsgSignature><![CDATA[%s]]></MsgSignature>
<TimeStamp>%s</TimeStamp>
<Nonce><![CDATA[%s]]></Nonce>
</xml>";
        return sprintf($format, $encrypt, $signature, $timestamp, $nonce);
    }
    /**
     * 过滤文字回复\r\n换行符
     * @param string $text
     * @return string|mixed
     */
    private function _auto_text_filter($text) {
        if (!$this->_text_filter)
            return $text;
        return str_replace("\r\n", "\n", $text);
    }

    private $_msg = null;
    private $_funcflag = false;
    private $_text_filter = true;
    private $postxml;
    private $_receive = [];
    private static $_errorsMessage = [
        1 => '系统繁忙，请稍后重试!',
        40001 => 'AppSecret 错误，或者access_token无效',
        40002 => '不合法的凭证类型',
        40003 => '该用户是否已关注公众号',
        40004 => '不合法的媒体文件类型',
        40005 => '不合法的文件类型',
        40006 => '不合法的文件大小',
        40007 => '不合法的媒体文件 id',
        40008 => '不合法的消息类型',
        40009 => '不合法的图片文件大小',
        40010 => '不合法的语音文件大小',
        40011 => '不合法的视频文件大小',
        40012 => '不合法的缩略图文件大小',
        40013 => '不合法的 AppID',
        40014 => '不合法的 access_token',
        40015 => '不合法的菜单类型',
        40016 => '不合法的按钮个数',
        40017 => '不合法的按钮个数',
        40018 => '不合法的按钮名字长度',
        40019 => '不合法的按钮 KEY 长度',
        40020 => '不合法的按钮 URL 长度',
        40021 => '不合法的菜单版本号',
        40022 => '不合法的子菜单级数',
        40023 => '不合法的子菜单按钮个数',
        40024 => '不合法的子菜单按钮类型',
        40025 => '不合法的子菜单按钮名字长度',
        40026 => '不合法的子菜单按钮 KEY 长度',
        40027 => '不合法的子菜单按钮 URL 长度',
        40028 => '不合法的自定义菜单使用用户',
        40029 => '无效的 oauth_code',
        40030 => '不合法的 refresh_token',
        40031 => '不合法的 openid 列表',
        40032 => '不合法的 openid 列表长度',
        40033 => '不合法的请求字符，不能包含 \uxxxx 格式的字符',
        40035 => '不合法的参数',
        40038 => '不合法的请求格式',
        40039 => '不合法的 URL 长度',
        40048 => '无效的url',
        40050 => '不合法的分组 id',
        40051 => '分组名字不合法',
        40060 => '指定的 article_idx 不合法',
        40066 => '部门列表为空，或者至少存在一个部门ID不存在于通讯录中',
        40071 => '标签名字已经存在或者不合法',
        40113 => '不支持的文件类型',
        40117 => '分组名字不合法',
        40118 => 'media_id 大小不合法',
        40119 => 'button 类型错误',
        40120 => 'button 类型错误',
        40121 => '不合法的 media_id 类型',
        40125 => '无效的appsecret',
        40132 => '微信号不合法',
        40137 => '不支持的图片格式',
        40155 => '请勿添加其他公众号的主页链接',
        40163 => 'oauth_code已使用',
        40164 => 'IP白名单未设置, 请将当前服务器的IP添加进白名单。',
        41001 => '缺少 access_token 参数',
        41002 => '缺少 appid 参数',
        41003 => '缺少 refresh_token 参数',
        41004 => '缺少 appsecret 参数',
        41005 => '缺少多媒体文件数据',
        41006 => '缺少 media_id 参数',
        41007 => '缺少子菜单数据',
        41008 => '缺少 oauth code',
        41009 => '缺少 openid',
        42001 => 'access_token 超时，请检查 access_token 的有效期',
        42002 => 'refresh_token 超时',
        42003 => 'oauth_code 超时',
        42007 => '用户修改微信密码，需要重新授权',
        43001 => '需要 GET 请求',
        43002 => '需要 POST 请求',
        43003 => '需要 HTTPS 请求',
        43004 => '需要接收者关注',
        43005 => '需要好友关系',
        43019 => '需要将接收者从黑名单中移除',
        44001 => '多媒体文件为空',
        44002 => 'POST 的数据包为空',
        44003 => '图文消息内容为空',
        44004 => '文本消息内容为空',
        45001 => '多媒体文件大小超过限制',
        45002 => '消息内容超过限制',
        45003 => '标题字段超过限制',
        45003 => '标题字段超过限制',
        45004 => '描述字段超过限制',
        45005 => '链接字段超过限制',
        45006 => '图片链接字段超过限制',
        45007 => '语音播放时间超过限制',
        45008 => '图文消息超过限制',
        45009 => '微信接口，本日使用次数已达上限!',
        45010 => '创建菜单个数超过限制',
        45011 => 'API 调用太频繁，请稍候再试',
        45015 => '用户长时间未互动!',
        45016 => '系统分组，不允许修改',
        45017 => '分组名字过长',
        45018 => '分组数量超过上限',
        45047 => '客服接口下行条数超过上限',
        45056 => '创建的标签数过多，请注意不能超过100个',
        45058 => '微信默认标签，禁止操作',
        45157 => '标签名非法，请注意不能和其他标签重名',
        45158 => '标签名长度超过30个字节',
        46001 => '不存在媒体数据',
        46002 => '不存在的菜单版本',
        46003 => '不存在的菜单数据',
        46004 => '不存在的用户',
        47001 => '解析 JSON/XML 内容错误',
        48001 => 'api 功能未授权,请确认公众号已获得该接口!',
        48002 => '粉丝关闭了接收消息',
        48004 => 'api 接口被封禁',
        48005 => 'api 禁止删除被自动回复和自定义菜单引用的素材',
        48006 => 'api 禁止清零调用次数，因为清零次数达到上限',
        48008 => '没有该类型消息的发送权限',
        50001 => '用户未授权该 api',
        50002 => '用户受限，可能是违规后接口被封禁',
        50005 => '用户未关注公众号',
        60001 => '部门名称不能为空且长度不能超过32个字',
        60003 => '部门ID不存在',
        61451 => '参数错误 ',
        61452 => '无效客服账号 ',
        61453 => '客服帐号已存在 ',
        61454 => '客服帐号名长度超过限制 ( 仅允许 10 个英文字符，不包括 @ 及 @ 后的公众号的微信号 )',
        61455 => '客服帐号名包含非法字符 ( 仅允许英文 + 数字 )',
        61456 => '客服帐号个数超过限制 (10 个客服账号 )',
        61457 => '无效头像文件类型 ',
        61450 => '系统错误 ',
        61500 => '日期格式错误',
        63001 => '部分参数为空',
        63002 => '无效的签名',
        65301 => '不存在此 menuid 对应的个性化菜单',
        65302 => '没有相应的用户',
        65303 => '没有默认菜单，不能创建个性化菜单',
        65304 => 'MatchRule 信息为空',
        65305 => '个性化菜单数量受限',
        65306 => '不支持个性化菜单的帐号',
        65307 => '个性化菜单信息为空',
        65308 => '包含没有响应类型的 button',
        65309 => '个性化菜单开关处于关闭状态',
        65310 => '填写了省份或城市信息，国家信息不能为空',
        65311 => '填写了城市信息，省份信息不能为空',
        65312 => '不合法的国家信息',
        65313 => '不合法的省份信息',
        65314 => '不合法的城市信息',
        65316 => '该公众号的菜单设置了过多的域名外跳（最多跳转到 3 个域名的链接）',
        65317 => '不合法的 URL',
        87009 => '无效的签名',
        88000 => '无评论特权',
        9001001 => 'POST 数据参数不合法',
        9001002 => '微信服务不可用',
        9001003 => 'Ticket 不合法',
        9001004 => '获取摇周边用户信息失败',
        9001005 => '获取商户信息失败',
        9001006 => '获取 OpenID 失败',
        9001007 => '上传文件缺失',
        9001008 => '上传素材的文件类型不合法',
        9001009 => '上传素材的文件尺寸不合法',
        9001010 => '上传失败',
        9001020 => '帐号不合法',
        9001021 => '已有设备激活率低于 50% ，不能新增设备',
        9001022 => '设备申请数不合法，必须为大于 0 的数字',
        9001023 => '已存在审核中的设备 ID 申请',
        9001024 => '一次查询设备 ID 数量不能超过 50',
        9001025 => '设备 ID 不合法',
        9001026 => '页面 ID 不合法',
        9001027 => '页面参数不合法',
        9001028 => '一次删除页面 ID 数量不能超过 10',
        9001029 => '页面已应用在设备中，请先解除应用关系再删除',
        9001030 => '一次查询页面 ID 数量不能超过 50',
        9001031 => '时间区间不合法',
        9001032 => '保存设备与页面的绑定关系参数错误',
        9001033 => '门店 ID 不合法',
        9001034 => '设备备注信息过长',
        9001035 => '设备申请参数不合法',
        9001036 => '查询起始值 begin 不合法',
    ];

    const API_BASE_URL = 'https://api.weixin.qq.com/';
    const API_BASE_URL_1 = 'https://open.weixin.qq.com/';

    const API_TOKEN_URL = self::API_BASE_URL . 'cgi-bin/token?'; //公众号token获取地址

    //菜单
    const API_MENU_CREATE = self::API_BASE_URL . 'cgi-bin/menu/create?access_token='; //普通菜单创建接口
    const API_MENU_SELECT = self::API_BASE_URL . 'cgi-bin/get_current_selfmenu_info?access_token='; //菜单 查询接口
    const API_MENU_DELETE = self::API_BASE_URL . 'cgi-bin/menu/delete?access_token='; //菜单全部删除接口
    const API_MENU_CREATE_PER = self::API_BASE_URL . 'cgi-bin/menu/addconditional?access_token='; //个性菜单创建接口
    const API_MENU_DELETE_PER = self::API_BASE_URL . 'cgi-bin/menu/delconditional?access_token='; //删除个性化删除接口
    const API_MENU_MATCH_PER = self::API_BASE_URL . 'cgi-bin/menu/trymatch?access_token='; //测试个性化菜单匹配结果
    const API_MENU_SELECT_PER = self::API_BASE_URL . 'cgi-bin/menu/get?access_token='; //测试个性化菜单匹配结果

    //获取用户信息
    const API_USER_OAUTH2 =  self::API_BASE_URL_1 . 'connect/oauth2/authorize?'; //微信网页授权
    const API_OAUTH2_ACCESS_TOKEN =  self::API_BASE_URL . 'sns/oauth2/access_token?'; //获取用户授权网页授权access_token
    const API_OAUTH2_COMPONENT_ACCESS_TOKEN =  self::API_BASE_URL . 'sns/oauth2/component/access_token?'; //获取用户授权网页授权access_token
    const API_OAUTH2_REFRESH_TOKEN =  self::API_BASE_URL . 'sns/oauth2/refresh_token?'; //刷新用户授权网页授权access_token
    const API_OAUTH2_USER_INFO =  self::API_BASE_URL . 'sns/userinfo?access_token='; //拉取用户授权信息

    const API_MATERIAL_TEMPORARY_UPLOAD = self::API_BASE_URL . 'cgi-bin/media/upload?access_token='; //临时素材上传
    const API_MATERIAL_TEMPORARY_GET = self::API_BASE_URL . 'cgi-bin/media/get?'; //获取临时素材
    const API_MATERIAL_TEMPORARY_GET_JSDK = self::API_BASE_URL . 'cgi-bin/media/get/jssdk?'; //获取JSDK上传的临时素材

    const API_MATERIAL_DRAFT_ADD = self::API_BASE_URL . 'cgi-bin/draft/add?access_token='; //新增草稿
    const API_MATERIAL_DRAFT_GET = self::API_BASE_URL . 'cgi-bin/draft/get?access_token='; //获取草稿
    const API_MATERIAL_DRAFT_DEL = self::API_BASE_URL . 'cgi-bin/draft/delete?access_token='; //删除草稿
    const API_MATERIAL_DRAFT_UPDATE = self::API_BASE_URL . 'cgi-bin/draft/update?access_token='; //修改草稿
    const API_MATERIAL_DRAFT_COUNT = self::API_BASE_URL . 'cgi-bin/draft/count?access_token='; //获取草稿总数
    const API_MATERIAL_DRAFT_LIST = self::API_BASE_URL . 'cgi-bin/draft/batchget?access_token='; //获取草稿列表

    const API_FREEPUBLISH_SUBMIT = self::API_BASE_URL . 'cgi-bin/freepublish/submit?access_token='; //发布接口
    const API_FREEPUBLISH_GET = self::API_BASE_URL . 'cgi-bin/freepublish/get?access_token='; //发布状态轮询接口
    const API_FREEPUBLISH_DELETE = self::API_BASE_URL . 'cgi-bin/freepublish/delete?access_token='; //删除发布
    const API_FREEPUBLISH_FIND = self::API_BASE_URL . 'cgi-bin/freepublish/getarticle?access_token='; //通过 article_id 获取已发布文章
    const API_FREEPUBLISH_LIST = self::API_BASE_URL . 'cgi-bin/freepublish/batchget?access_token='; //获取成功发布列表


    const API_MATERIAL_UPLOAD_IMG = self::API_BASE_URL . 'cgi-bin/media/uploadimg?access_token='; //上传图文消息内的图片获取URL
    const API_MATERIAL_UPLOAD = self::API_BASE_URL . 'cgi-bin/material/add_material?access_token='; //永久素材上传
    const API_MATERIAL_DEL = self::API_BASE_URL . 'cgi-bin/material/del_material?access_token='; //删除不再需要的永久素材
    const API_MATERIAL_GET_COUNT = self::API_BASE_URL . 'cgi-bin/material/get_materialcount?access_token='; //永久素材的总数，也会计算公众平台官网素材管理中的素材 2.图片和图文消息素材（包括单图文和多图文）的总数上限为5000，其他素材的总数上限为1000 3.调用该接口需https协议
    const API_MATERIAL_BATCH_GET = self::API_BASE_URL . 'cgi-bin/material/batchget_material?access_token='; //获取永久素材的列表

    const API_COMMENT_OPEN = self::API_BASE_URL . 'cgi-bin/comment/open?access_token='; // 打开已群发文章评论
    const API_COMMENT_CLOSE = self::API_BASE_URL . 'cgi-bin/comment/close?access_token='; // 关闭已群发文章评论
    const API_COMMENT_LIST = self::API_BASE_URL . 'cgi-bin/comment/list?access_token='; // 查看指定文章的评论数据
    const API_COMMENT_MARKELECT = self::API_BASE_URL . 'cgi-bin/comment/markelect?access_token='; //将评论标记精选
    const API_COMMENT_UN_MARKELECT = self::API_BASE_URL . 'cgi-bin/comment/unmarkelect?access_token='; //  将评论取消精选
    const API_COMMENT_DELETE = self::API_BASE_URL . 'cgi-bin/comment/delete?access_token='; //  将评论取消精选
    const API_COMMENT_REPLY = self::API_BASE_URL . 'cgi-bin/comment/reply?access_token='; //  回复评论
    const API_COMMENT_DEL_REPLY = self::API_BASE_URL . 'cgi-bin/comment/reply/delete?access_token='; // 删除回复评论

    const API_TAG_CREATE = self::API_BASE_URL . 'cgi-bin/tags/create?access_token='; // 创建标签
    const API_TAG_LSIT = self::API_BASE_URL . 'cgi-bin/tags/get?access_token='; // 获取标签列表
    const API_TAG_UPDATE = self::API_BASE_URL . 'cgi-bin/tags/update?access_token='; // 修改标签
    const API_TAG_DELETE = self::API_BASE_URL . 'cgi-bin/tags/delete?access_token='; // 删除标签

    const API_TAG_USER_LIST = self::API_BASE_URL . 'cgi-bin/user/tag/get?access_token='; //获取标签下粉丝列表
    const API_TAG_USER_ADDS = self::API_BASE_URL . 'cgi-bin/tags/members/batchtagging?access_token='; //批量为用户打标签
    const API_TAG_USER_UNADDS = self::API_BASE_URL . 'cgi-bin/tags/members/batchuntagging?access_token='; //批量为用户取消标签
    const API_TAG_USER_GET = self::API_BASE_URL . 'cgi-bin/tags/getidlist?access_token='; // 获取用户身上的标签列表

    const API_USER_REMARK_SET = self::API_BASE_URL . 'cgi-bin/user/info/updateremark?access_token='; //对指定用户设置备注名
    const API_USER_UNIONN_ID_INFO = self::API_BASE_URL . 'cgi-bin/user/info?'; //获取用户基本信息（包括UnionID机制）
    const API_USER_UNIONN_ID_INFO_LSIT = self::API_BASE_URL . 'cgi-bin/user/info/batchget?access_token='; //获取用户基本信息（包括UnionID机制）
    const API_USER_OPENID_LIST = self::API_BASE_URL . 'cgi-bin/user/get?'; //公众号可通过本接口来获取帐号的关注者列表，
    const API_USER_BLACK_SET = self::API_BASE_URL . 'cgi-bin/tags/members/batchblacklist?access_token='; //拉黑用户
    const API_USER_BLACK_OPENID_LIST = self::API_BASE_URL . 'cgi-bin/tags/members/getblacklist?access_token='; //获取公众号的黑名单列表
    const API_USER_UN_BLACK_SET = self::API_BASE_URL . 'cgi-bin/tags/members/batchunblacklist?access_token='; //取消拉黑用户

    const API_CUSTOM_SERVICE_ACCOUNT_ADD = self::API_BASE_URL . 'customservice/kfaccount/add?access_token='; //添加客服帐号
    const API_CUSTOM_SERVICE_ACCOUNT_UPDATE = self::API_BASE_URL . 'customservice/kfaccount/update?access_token='; //添加客服帐号
    const API_CUSTOM_SERVICE_ACCOUNT_DEL = self::API_BASE_URL . 'customservice/kfaccount/del?access_token='; //删除客服帐号
    const API_CUSTOM_SERVICE_ACCOUNT_HEAD = self::API_BASE_URL . 'customservice/kfaccount/uploadheadimg?access_token='; //添加客服帐号
    const API_CUSTOM_SERVICE_ACCOUNT_LIST = self::API_BASE_URL . 'cgi-bin/customservice/getkflist?access_token='; //添加客服帐号

    const API_MESSAGE_CUSTOM_SEND = self::API_BASE_URL . 'cgi-bin/message/custom/send?access_token='; //客服接口-发消息
    const API_MESSAGE_MASS = self::API_BASE_URL . 'cgi-bin/message/mass/sendall?access_token='; //消息群发-发消息
    const API_MESSAGE_MASS_DEL = self::API_BASE_URL . 'cgi-bin/message/mass/delete?access_token='; //消息群发-删除群发
    const API_MESSAGE_MASS_PREVIEW = self::API_BASE_URL . 'cgi-bin/message/mass/preview?access_token='; //消息群发-删除群发
    const API_MESSAGE_MASS_GET = self::API_BASE_URL . 'cgi-bin/message/mass/get?access_token='; //消息群发-查询群发消息发送状态

    const API_INDUSTRY_SET = self::API_BASE_URL . 'cgi-bin/template/api_set_industry?access_token='; //行业设置
    const API_INDUSTRY_GET = self::API_BASE_URL . 'cgi-bin/template/get_industry?access_token='; //行业设置

    const API_TEMPLATE_ADD = self::API_BASE_URL . 'cgi-bin/template/api_add_template?access_token='; //模板添加
    const API_TEMPLATE_GET = self::API_BASE_URL . 'cgi-bin/template/get_all_private_template?access_token='; //模板列表
    const API_TEMPLATE_DEL = self::API_BASE_URL . 'cgi-bin/template/del_private_template?access_token='; //删除模板
    const API_TEMPLATE_SEND = self::API_BASE_URL . 'cgi-bin/message/template/send?access_token='; //发送模板消息

    const API_TEMPLATE_MINI_SENDS = self::API_BASE_URL . 'cgi-bin/message/wxopen/template/uniform_send?access_token='; //统一服务消息发送
    const API_TEMPLATE_MINI_ADD = self::API_BASE_URL . 'wxaapi/newtmpl/addtemplate?access_token='; //添加小程序订阅模板
    const API_TEMPLATE_MINI_DEL = self::API_BASE_URL . 'wxaapi/newtmpl/deltemplate?access_token='; //删除小程序订阅模板
    const API_TEMPLATE_MINI_GET_CATEGORY = self::API_BASE_URL . 'wxaapi/newtmpl/getcategory?access_token='; //添加小程序订阅模板
    const API_TEMPLATE_MINI_GET_LIST = self::API_BASE_URL . 'wxaapi/newtmpl/gettemplate?access_token='; //添加小程序订阅模板
    const API_TEMPLATE_MINI_SEND = self::API_BASE_URL . 'cgi-bin/message/subscribe/send?access_token='; //添加小程序订阅模板

    const API_CLEAR_COUNT = self::API_BASE_URL . 'cgi-bin/clear_quota?access_token='; //公众号调用或第三方平台帮公众号调用对公众号的所有api调用（包括第三方帮其调用）次数进行清零：
    const API_QRCODE_URL = self::API_BASE_URL . 'cgi-bin/qrcode/create?access_token='; //创建二维码ticket
    const API_QRCODE_IMG_URL = 'https://mp.weixin.qq.com/cgi-bin/showqrcode?'; //获取二维码地址
    const API_QRCODE_GET_MINI_RUL = 'cgi-bin/wxaapp/createwxaqrcode?access_token='; //获取二维码地址
    const API_QRCODE_GET_MINI_LIN_RUL = 'wxa/getwxacodeunlimit?access_token='; //获取二维码地址

    const API_CARD_CREATE_URL = self::API_BASE_URL . '/card/create?access_token='; //卡卷创建
    const API_CARD_PAY_CALL_SET_URL = self::API_BASE_URL . '/card/paycell/set?access_token='; //卡卷设置买单接口
    const API_CARD_SELF_CONSUME_CALL_SET_URL = self::API_BASE_URL . '/card/selfconsumecell/set?access_token='; //卡卷设置买单接口
    const API_CARD_QRCODE_URL = self::API_BASE_URL . 'card/qrcode/create?access_token='; //卡卷二维码接口
    const API_CARD_LANDINGOAGE_URL = self::API_BASE_URL . 'card/landingpage/create?access_token='; //卡卷二维码接口
    const API_CARD_CODE_IMPORT_URL = self::API_BASE_URL . 'card/code/deposit?access_token='; //卡卷二维码导入
    const API_CARD_CODE_COUNT_URL = self::API_BASE_URL . 'card/code/getdepositcount?access_token='; //卡卷二维码导入查询
    const API_CARD_CODE_EIEXTS_URL = self::API_BASE_URL . 'card/code/checkcode?access_token='; //卡卷二维码导入查询

    const API_SHOR_URL = self::API_BASE_URL . 'cgi-bin/shorturl?access_token='; //长链接转短连接
    const API_OCR_ID_CARD = self::API_BASE_URL . 'cv/ocr/idcard?'; //身份证OCR识别接口
    const API_OCR_BANK_CARD_URL = self::API_BASE_URL . 'cv/ocr/bankcard?'; //银行卡OCR识别接口
    const API_OCR_DRIVING_URL = self::API_BASE_URL . 'cv/ocr/driving?'; //行驶证OCR识别接口

    const API_JS_SDK_TICKET_URL = self::API_BASE_URL . 'cgi-bin/ticket/getticket?type=jsapi&access_token='; //jsapi_ticket

    const API_MINI_SESSION_CODE_URL = self::API_BASE_URL . 'sns/jscode2session?'; //获取该用户的 UnionI
    const API_MINI_UNIONLD_URL = self::API_BASE_URL . 'wxa/getpaidunionid?'; //获取该用户的 UnionI

    const API_PAY_USER_STATE = 'https://api.mch.weixin.qq.com/v3/payscore/user-service-state?'; //查询用户授权状态API
    const API_PAY_DEAUTHORIZE = 'https://api.mch.weixin.qq.com/payscore/'; //查询用户授权状态API
    /**
     * @var 第三方授权相关的API
     */
    const BASE_API_URL = 'https://api.weixin.qq.com/cgi-bin/component/'; //基础请求路由
    const ACCESS_TOKEN_URL = self::BASE_API_URL . 'api_component_token';  //获取accessToken;
    const API_CREATE_PREAUTHCODE_URL = self::BASE_API_URL . 'api_create_preauthcode?component_access_token='; //获取预授权码pre_auth_code
    const API_QUERY_AUTH_URL = self::BASE_API_URL . 'api_query_auth?component_access_token='; //使用授权码换取公众号或小程序的接口调用凭据和授权信息
    const API_AUTHORIZER_TOKEN_URL = self::BASE_API_URL . 'api_authorizer_token?component_access_token='; // 获取（刷新）授权公众号或小程序的接口调用凭据（令牌）
    const API_GET_AUTHORIZER_INFO = self::BASE_API_URL . 'api_get_authorizer_info?component_access_token='; //获取授权方的帐号基本信息
    const API_GET_AUTHORIZER_OPTION = self::BASE_API_URL . 'api_get_authorizer_option?component_access_token='; //获取授权方选项信息
    const API_SET_AUTHORIZER_OPTION = self::BASE_API_URL . 'api_set_authorizer_option?component_access_token='; //设置授权方选项信息
    const API_GET_AUTHORIZER_LIST = self::BASE_API_URL . 'api_get_authorizer_list?component_access_token='; //拉取所有已授权的帐号信息
}
/**
 * Prpcrypt class
 *
 * 提供接收和推送给公众平台消息的加解密接口.
 */
class Prpcrypt {

    public $key;

    function __construct($k) {
        $this->key = base64_decode($k . "=");
    }

    /**
     * 兼容老版本php构造函数，不能在 __construct() 方法前边，否则报错
     */
    function Prpcrypt($k) {
        $this->key = base64_decode($k . "=");
    }

    /**
     * 对明文进行加密
     * @param string $text 需要加密的明文
     * @return string 加密后的密文
     */
    public function encrypt($text, $appid) {
        try {
            //获得16位随机字符串，填充到明文之前
            $random = $this->getRandomStr(); //"aaaabbbbccccdddd";
            $text = $random . pack("N", strlen($text)) . $text . $appid;
            // 网络字节序
            $size = mcrypt_get_block_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC);
            $module = mcrypt_module_open(MCRYPT_RIJNDAEL_128, '', MCRYPT_MODE_CBC, '');
            $iv = substr($this->key, 0, 16);
            //使用自定义的填充方式对明文进行补位填充
            $pkc_encoder = new PKCS7Encoder;
            $text = $pkc_encoder->encode($text);
            mcrypt_generic_init($module, $this->key, $iv);
            //加密
            $encrypted = mcrypt_generic($module, $text);
            mcrypt_generic_deinit($module);
            mcrypt_module_close($module);

            //			print(base64_encode($encrypted));
            //使用BASE64对加密后的字符串进行编码
            return array(ErrorCode::$OK, base64_encode($encrypted));
        } catch (Exception $e) {
            //print $e;
            return array(ErrorCode::$EncryptAESError, null);
        }
    }

    /**
     * 对密文进行解密
     * @param string $encrypted 需要解密的密文
     * @return string 解密得到的明文
     */
    public function decrypt($encrypted, $appid) {

        try {
            //使用BASE64对需要解密的字符串进行解码
            $ciphertext_dec = base64_decode($encrypted);
            $module = mcrypt_module_open(MCRYPT_RIJNDAEL_128, '', MCRYPT_MODE_CBC, '');
            $iv = substr($this->key, 0, 16);
            mcrypt_generic_init($module, $this->key, $iv);
            //解密
            $decrypted = mdecrypt_generic($module, $ciphertext_dec);
            mcrypt_generic_deinit($module);
            mcrypt_module_close($module);
        } catch (Exception $e) {
            return array(ErrorCode::$DecryptAESError, null);
        }


        try {
            //去除补位字符
            $pkc_encoder = new PKCS7Encoder;
            $result = $pkc_encoder->decode($decrypted);
            //去除16位随机字符串,网络字节序和AppId
            if (strlen($result) < 16)
                return "";
            $content = substr($result, 16, strlen($result));
            $len_list = unpack("N", substr($content, 0, 4));
            $xml_len = $len_list[1];
            $xml_content = substr($content, 4, $xml_len);
            $from_appid = substr($content, $xml_len + 4);
            if (!$appid)
                $appid = $from_appid;
            //如果传入的appid是空的，则认为是订阅号，使用数据中提取出来的appid
        } catch (Exception $e) {
            //print $e;
            return array(ErrorCode::$IllegalBuffer, null);
        }
        if ($from_appid != $appid)
            return array(ErrorCode::$ValidateAppidError, null);
        //不注释上边两行，避免传入appid是错误的情况
        return array(0, $xml_content, $from_appid); //增加appid，为了解决后面加密回复消息的时候没有appid的订阅号会无法回复
    }

    /**
     * 随机生成16位字符串
     * @return string 生成的字符串
     */
    function getRandomStr() {

        $str = "";
        $str_pol = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz";
        $max = strlen($str_pol) - 1;
        for ($i = 0; $i < 16; $i++) {
            $str .= $str_pol[mt_rand(0, $max)];
        }
        return $str;
    }

}
