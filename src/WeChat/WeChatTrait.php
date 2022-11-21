<?php

namespace Kang\Libs\WeChat;

use Kang\Libs\Base\Event;
use Kang\Libs\Helper\Curl;

trait WeChatTrait{
    /**
     * @var GET请求
     * @param string $url 请求地址
     * @param null $data 请求数据
     * @param bool $autoToken 是否自动获取
     * @param bool $useCert 是否需要证书
     * @return bool|void
     */
    public function httpGet($url, $data = NULL, $autoToken = false, $useCert = false){
        if($data){
            $url .= '&' . http_build_query($data);
        }

        return $this->httpRequest($url, Curl::METHOD_GET, null, $autoToken, false, $useCert);
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
        $event->data['autoToken'] = $autoToken;
        $event->data['isFile'] = $isFile;
        $event->data['useCert'] = $useCert;

        $this->trigger(self::EVENT_lOG, $event);
        $url = self::BASE_URL . $url;
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

        return $this->responseResult($result, $event);
    }

    protected function parseRequestData(&$data, $method, $isFile){
        if(!empty($data) && $method == Curl::METHOD_POST && $isFile == false){
            $data = $isFile ? $data : (is_array($data) ? json_encode($data, JSON_UNESCAPED_UNICODE) : $data);
        }
    }

    protected function responseResult($result, Event $event){
        $result = json_decode($result, JSON_UNESCAPED_UNICODE);
        if($result === null){
            return $result;
        }

        if(isset($result['errcode']) && $result['errcode'] != 0){
            if($result['errcode'] == '42001' && $this->_retry === false){
                $this->_retry = true;
                $this->setAccessToken('');
                $this->trigger(self::EVENT_ACCESS_TOKEN_ERROR, $event);
                return $this->httpRequest($event->data['url'], $event->data['method'], $event->data['data'],  $event->data['autoToken'],  $event->data['isFile'],  $event->data['useCert']);
            }

            $errCode = $result['errcode'];
            $errMsg = $result['errmsg'] ?? '未知错误';
            if(isset(self::$_errorsMessage[$errCode])){
                $errMsg = self::$_errorsMessage[$errCode];
            }

            return $this->setErrors($errCode, $errMsg);
        }

        return $result;
    }

    protected static $_errorsMessage = [
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
    protected $_retry = false; //access_token 失效的重试请求
}
