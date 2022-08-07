<?php
namespace Kang\Libs\WeChat;

use Kang\Libs\Base\Component;
use Kang\Libs\Base\Event;
use Kang\Libs\Helper\Curl;
use Kang\Libs\Helper\FileCache;
use Kang\Libs\WeChat\Behavior\WeChatBehavior;

/**
 * Class WeChatBase
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

    const LANG_ZH_CN = 'zh_CN'; //简体
    const LANG_ZH_TW = 'zh_TW'; //繁体
    const LANG_EN = 'en'; //英语
    const SCOPE_BASE = 'snsapi_base'; //不弹出授权页面，直接跳转，只能获取用户openid
    const SCOPE_USER_INFO = 'snsapi_userinfo'; //不弹出授权页面，直接跳转，只能获取用户openid
    const QR_SCENE = 'QR_SCENE'; //临时的整型参数值
    const QR_STR_SCENE = 'QR_STR_SCENE'; //为临时的字符串参数值，
    const QR_LIMIT_SCENE = 'QR_LIMIT_SCENE'; //永久的整型参数值
    const QR_LIMIT_STR_SCENE = 'QR_LIMIT_STR_SCENE'; //永久的字符串参数值
    const TYPE_ALL = 0; // 普通评论&精选评论
    const TYPE_ORD = 1; // 普通评论
    const TYPE_FEATURED = 1; //精选评论
    const TYPE_IMAGE = 'image'; //图片
    const TYPE_VOICE = 'voice'; //语音
    const TYPE_VIDEO = 'video'; //视频
    const TYPE_THUMB = 'thumb'; //缩略图

    const MSGTYPE_TEXT = 'text';
    const MSGTYPE_IMAGE = 'image';
    const MSGTYPE_LOCATION = 'location';
    const MSGTYPE_LINK = 'link';
    const MSGTYPE_EVENT = 'event';
    const MSGTYPE_MUSIC = 'music';
    const MSGTYPE_NEWS = 'news';
    const MSGTYPE_VOICE = 'voice';
    const MSGTYPE_VIDEO = 'video';

    const EVENT_SUBSCRIBE = 'subscribe';       //订阅
    const EVENT_UNSUBSCRIBE = 'unsubscribe';   //取消订阅
    const EVENT_SCAN = 'SCAN';                 //扫描带参数二维码
    const EVENT_LOCATION = 'LOCATION';         //上报地理位置
    const EVENT_MENU_VIEW = 'VIEW';                     //菜单 - 点击菜单跳转链接
    const EVENT_MENU_CLICK = 'CLICK';                   //菜单 - 点击菜单拉取消息
    const EVENT_MENU_SCAN_PUSH = 'scancode_push';       //菜单 - 扫码推事件(客户端跳URL)
    const EVENT_MENU_SCAN_WAITMSG = 'scancode_waitmsg'; //菜单 - 扫码推事件(客户端不跳URL)
    const EVENT_MENU_PIC_SYS = 'pic_sysphoto';          //菜单 - 弹出系统拍照发图
    const EVENT_MENU_PIC_PHOTO = 'pic_photo_or_album';  //菜单 - 弹出拍照或者相册发图
    const EVENT_MENU_PIC_WEIXIN = 'pic_weixin';         //菜单 - 弹出微信相册发图器
    const EVENT_MENU_LOCATION = 'location_select';      //菜单 - 弹出地理位置选择器
    const EVENT_SEND_MASS = 'MASSSENDJOBFINISH';        //发送结果 - 高级群发完成
    const EVENT_SEND_TEMPLATE = 'TEMPLATESENDJOBFINISH'; //发送结果 - 模板消息发送结果
    const EVENT_KF_SEESION_CREATE = 'kfcreatesession';  //多客服 - 接入会话
    const EVENT_KF_SEESION_CLOSE = 'kfclosesession';    //多客服 - 关闭会话
    const EVENT_KF_SEESION_SWITCH = 'kfswitchsession';  //多客服 - 转接会话
    const EVENT_CARD_PASS = 'card_pass_check';          //卡券 - 审核通过
    const EVENT_CARD_NOTPASS = 'card_not_pass_check';   //卡券 - 审核未通过
    const EVENT_CARD_USER_GET = 'user_get_card';        //卡券 - 用户领取卡券
    const EVENT_CARD_USER_DEL = 'user_del_card';        //卡券 - 用户删除卡券
    const EVENT_MERCHANT_ORDER = 'merchant_order';        //微信小店 - 订单付款通知

    public function behaviors(): array{
        return [
            WeChatBehavior::class,
            'cache' =>  FileCache::getInstall(),
        ];
    }
    //--------------------------微信服务器通知--------------------------------//
    /**
     * @var 微信服务器的验证
     * @param bool $return 是否返回
     */
    public function validate($return = false) {
        $encryptStr = "";
        if ($_SERVER['REQUEST_METHOD'] == "POST") {
            $postStr = file_get_contents("php://input");
            $array = (array) simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
            $this->encrypt_type = isset($_GET["encrypt_type"]) ? $_GET["encrypt_type"] : '';
            if ($this->encrypt_type == 'aes') { //aes加密
                $event = new Event();
                $event->data['validate'] = $postStr;
                $this->trigger(self::EVENT_lOG, $event);
                $encryptStr = $array['Encrypt'];
                $pc = new Prpcrypt($this->encodingAesKey);
                $array = $pc->decrypt($encryptStr, $this->appid);
                if (!isset($array[0]) || ($array[0] != 0)) {
                    if (!$return) {
                        die('decrypt error!');
                    } else {
                        return false;
                    }
                }
                $this->postxml = $array[1];
                if (!$this->appid)
                    $this->appid = $array[2]; //为了没有appid的订阅号。
            } else {
                $this->postxml = $postStr;
            }
        } elseif (isset($_GET["echostr"])) {
            $echoStr = $_GET["echostr"];
            if ($return) {
                if ($this->checkSignature())
                    return $echoStr;
                else
                    return false;
            } else {
                if ($this->checkSignature())
                    die($echoStr);
                else
                    die('no access');
            }
        }

        if (!$this->checkSignature($encryptStr)) {
            if ($return)
                return false;
            else
                die('no access');
        }
        return true;
    }

    /**
     * @var 捕获微信服务器发来的信息
     * @return $this
     */
    public function getRevive() {
        if(empty($this->_receive)){
            $postStr = !empty($this->postxml) ? $this->postxml : file_get_contents("php://input");
            if (!empty($postStr)) {
                $this->_receive = (array) simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
            }
        }

        return $this;
    }
    /**
     * @var 获取微信服务器发来的信息
     * @return array
     */
    public function getReviveData() {
        return $this->_receive;
    }

    /**
     * 获取消息发送者
     */
    public function getRevFrom() {
        if (isset($this->_receive['FromUserName']))
            return $this->_receive['FromUserName'];
        else
            return false;
    }

    /**
     * 获取消息接受者
     */
    public function getRevTo() {
        if (isset($this->_receive['ToUserName']))
            return $this->_receive['ToUserName'];
        else
            return false;
    }

    /**
     * 获取接收消息的类型
     */
    public function getRevType() {
        if (isset($this->_receive['MsgType']))
            return $this->_receive['MsgType'];
        else
            return false;
    }

    /**
     * 获取消息ID
     */
    public function getRevID() {
        if (isset($this->_receive['MsgId']))
            return $this->_receive['MsgId'];
        else
            return false;
    }

    /**
     * 获取消息发送时间
     */
    public function getRevCtime() {
        if (isset($this->_receive['CreateTime']))
            return $this->_receive['CreateTime'];
        else
            return false;
    }

    /**
     * 获取接收消息内容正文
     */
    public function getRevContent() {
        if (isset($this->_receive['Content']))
            return $this->_receive['Content'];
        else if (isset($this->_receive['Recognition'])) //获取语音识别文字内容，需申请开通
            return $this->_receive['Recognition'];
        else
            return false;
    }

    /**
     * 获取接收消息图片
     */
    public function getRevPic() {
        if (isset($this->_receive['PicUrl']))
            return array(
                'mediaid' => $this->_receive['MediaId'],
                'picurl' => (string) $this->_receive['PicUrl'], //防止picurl为空导致解析出错
            );
        else
            return false;
    }

    /**
     * 获取接收消息链接
     */
    public function getRevLink() {
        if (isset($this->_receive['Url'])) {
            return array(
                'url' => $this->_receive['Url'],
                'title' => $this->_receive['Title'],
                'description' => $this->_receive['Description']
            );
        } else
            return false;
    }

    /**
     * 获取接收地理位置
     */
    public function getRevGeo() {
        if (isset($this->_receive['Location_X'])) {
            return array(
                'x' => $this->_receive['Location_X'],
                'y' => $this->_receive['Location_Y'],
                'scale' => $this->_receive['Scale'],
                'label' => $this->_receive['Label']
            );
        } else
            return false;
    }

    /**
     * 获取上报地理位置事件
     */
    public function getRevEventGeo() {
        if (isset($this->_receive['Latitude'])) {
            return array(
                'x' => $this->_receive['Latitude'],
                'y' => $this->_receive['Longitude'],
                'precision' => $this->_receive['Precision'],
            );
        } else
            return false;
    }

    /**
     * 获取接收事件推送
     */
    public function getRevEvent() {
        if (isset($this->_receive['Event'])) {
            $array['event'] = $this->_receive['Event'];
        }
        if (isset($this->_receive['EventKey'])) {
            $array['key'] = $this->_receive['EventKey'];
        }
        if (isset($array) && count($array) > 0) {
            return $array;
        } else {
            return false;
        }
    }

    /**
     * 获取自定义菜单的扫码推事件信息
     *
     * 事件类型为以下两种时则调用此方法有效
     * Event	 事件类型，scancode_push
     * Event	 事件类型，scancode_waitmsg
     *
     * @return: array | false
     * array (
     *     'ScanType'=>'qrcode',
     *     'ScanResult'=>'123123'
     * )
     */
    public function getRevScanInfo() {
        if (isset($this->_receive['ScanCodeInfo'])) {
            if (!is_array($this->_receive['ScanCodeInfo'])) {
                $array = (array) $this->_receive['ScanCodeInfo'];
                $this->_receive['ScanCodeInfo'] = $array;
            } else {
                $array = $this->_receive['ScanCodeInfo'];
            }
        }
        if (isset($array) && count($array) > 0) {
            return $array;
        } else {
            return false;
        }
    }

    /**
     * 获取自定义菜单的图片发送事件信息
     *
     * 事件类型为以下三种时则调用此方法有效
     * Event	 事件类型，pic_sysphoto        弹出系统拍照发图的事件推送
     * Event	 事件类型，pic_photo_or_album  弹出拍照或者相册发图的事件推送
     * Event	 事件类型，pic_weixin          弹出微信相册发图器的事件推送
     *
     * @return: array | false
     * array (
     *   'Count' => '2',
     *   'PicList' =>array (
     *         'item' =>array (
     *             0 =>array ('PicMd5Sum' => 'aaae42617cf2a14342d96005af53624c'),
     *             1 =>array ('PicMd5Sum' => '149bd39e296860a2adc2f1bb81616ff8'),
     *         ),
     *   ),
     * )
     *
     */
    public function getRevSendPicsInfo() {
        if (isset($this->_receive['SendPicsInfo'])) {
            if (!is_array($this->_receive['SendPicsInfo'])) {
                $array = (array) $this->_receive['SendPicsInfo'];
                if (isset($array['PicList'])) {
                    $array['PicList'] = (array) $array['PicList'];
                    $item = $array['PicList']['item'];
                    $array['PicList']['item'] = array();
                    foreach ($item as $key => $value) {
                        $array['PicList']['item'][$key] = (array) $value;
                    }
                }
                $this->_receive['SendPicsInfo'] = $array;
            } else {
                $array = $this->_receive['SendPicsInfo'];
            }
        }
        if (isset($array) && count($array) > 0) {
            return $array;
        } else {
            return false;
        }
    }

    /**
     * 获取自定义菜单的地理位置选择器事件推送
     *
     * 事件类型为以下时则可以调用此方法有效
     * Event	 事件类型，location_select        弹出地理位置选择器的事件推送
     *
     * @return: array | false
     * array (
     *   'Location_X' => '33.731655000061',
     *   'Location_Y' => '113.29955200008047',
     *   'Scale' => '16',
     *   'Label' => '某某市某某区某某路',
     *   'Poiname' => '',
     * )
     *
     */
    public function getRevSendGeoInfo() {
        if (isset($this->_receive['SendLocationInfo'])) {
            if (!is_array($this->_receive['SendLocationInfo'])) {
                $array = (array) $this->_receive['SendLocationInfo'];
                if (empty($array['Poiname'])) {
                    $array['Poiname'] = "";
                }
                if (empty($array['Label'])) {
                    $array['Label'] = "";
                }
                $this->_receive['SendLocationInfo'] = $array;
            } else {
                $array = $this->_receive['SendLocationInfo'];
            }
        }
        if (isset($array) && count($array) > 0) {
            return $array;
        } else {
            return false;
        }
    }

    /**
     * 获取接收语音推送
     */
    public function getRevVoice() {
        if (isset($this->_receive['MediaId'])) {
            return array(
                'mediaid' => $this->_receive['MediaId'],
                'format' => $this->_receive['Format'],
            );
        } else
            return false;
    }

    /**
     * 获取接收视频推送
     */
    public function getRevVideo() {
        if (isset($this->_receive['MediaId'])) {
            return array(
                'mediaid' => $this->_receive['MediaId'],
                'thumbmediaid' => $this->_receive['ThumbMediaId']
            );
        } else
            return false;
    }

    /**
     * 获取接收TICKET
     */
    public function getRevTicket() {
        if (isset($this->_receive['Ticket'])) {
            return $this->_receive['Ticket'];
        } else
            return false;
    }

    /**
     * 获取二维码的场景值
     */
    public function getRevSceneId() {
        if (isset($this->_receive['EventKey'])) {
            return str_replace('qrscene_', '', $this->_receive['EventKey']);
        } else {
            return false;
        }
    }

    /**
     * 获取主动推送的消息ID
     * 经过验证，这个和普通的消息MsgId不一样
     * 当Event为 MASSSENDJOBFINISH 或 TEMPLATESENDJOBFINISH
     */
    public function getRevTplMsgID() {
        if (isset($this->_receive['MsgID'])) {
            return $this->_receive['MsgID'];
        } else
            return false;
    }

    /**
     * 获取模板消息发送状态
     */
    public function getRevStatus() {
        if (isset($this->_receive['Status'])) {
            return $this->_receive['Status'];
        } else
            return false;
    }

    /**
     * 获取群发或模板消息发送结果
     * 当Event为 MASSSENDJOBFINISH 或 TEMPLATESENDJOBFINISH，即高级群发/模板消息
     */
    public function getRevResult() {
        if (isset($this->_receive['Status'])) //发送是否成功，具体的返回值请参考 高级群发/模板消息 的事件推送说明
            $array['Status'] = $this->_receive['Status'];
        if (isset($this->_receive['MsgID'])) //发送的消息id
            $array['MsgID'] = $this->_receive['MsgID'];

        //以下仅当群发消息时才会有的事件内容
        if (isset($this->_receive['TotalCount']))     //分组或openid列表内粉丝数量
            $array['TotalCount'] = $this->_receive['TotalCount'];
        if (isset($this->_receive['FilterCount']))    //过滤（过滤是指特定地区、性别的过滤、用户设置拒收的过滤，用户接收已超4条的过滤）后，准备发送的粉丝数
            $array['FilterCount'] = $this->_receive['FilterCount'];
        if (isset($this->_receive['SentCount']))     //发送成功的粉丝数
            $array['SentCount'] = $this->_receive['SentCount'];
        if (isset($this->_receive['ErrorCount']))    //发送失败的粉丝数
            $array['ErrorCount'] = $this->_receive['ErrorCount'];
        if (isset($array) && count($array) > 0) {
            return $array;
        } else {
            return false;
        }
    }

    /**
     * 获取多客服会话状态推送事件 - 接入会话
     * 当Event为 kfcreatesession 即接入会话
     * @return string | boolean  返回分配到的客服
     */
    public function getRevKFCreate() {
        if (isset($this->_receive['KfAccount'])) {
            return $this->_receive['KfAccount'];
        } else
            return false;
    }

    /**
     * 获取多客服会话状态推送事件 - 关闭会话
     * 当Event为 kfclosesession 即关闭会话
     * @return string | boolean  返回分配到的客服
     */
    public function getRevKFClose() {
        if (isset($this->_receive['KfAccount'])) {
            return $this->_receive['KfAccount'];
        } else
            return false;
    }

    /**
     * 获取多客服会话状态推送事件 - 转接会话
     * 当Event为 kfswitchsession 即转接会话
     * @return array | boolean  返回分配到的客服
     * {
     *     'FromKfAccount' => '',      //原接入客服
     *     'ToKfAccount' => ''            //转接到客服
     * }
     */
    public function getRevKFSwitch() {
        if (isset($this->_receive['FromKfAccount']))     //原接入客服
            $array['FromKfAccount'] = $this->_receive['FromKfAccount'];
        if (isset($this->_receive['ToKfAccount']))    //转接到客服
            $array['ToKfAccount'] = $this->_receive['ToKfAccount'];
        if (isset($array) && count($array) > 0) {
            return $array;
        } else {
            return false;
        }
    }

    /**
     * 获取卡券事件推送 - 卡卷审核是否通过
     * 当Event为 card_pass_check(审核通过) 或 card_not_pass_check(未通过)
     * @return string|boolean  返回卡券ID
     */
    public function getRevCardPass() {
        if (isset($this->_receive['CardId']))
            return $this->_receive['CardId'];
        else
            return false;
    }

    /**
     * 获取卡券事件推送 - 领取卡券
     * 当Event为 user_get_card(用户领取卡券)
     * @return array|boolean
     */
    public function getRevCardGet() {
        if (isset($this->_receive['CardId']))     //卡券 ID
            $array['CardId'] = $this->_receive['CardId'];
        if (isset($this->_receive['IsGiveByFriend']))    //是否为转赠，1 代表是，0 代表否。
            $array['IsGiveByFriend'] = $this->_receive['IsGiveByFriend'];
        $array['OldUserCardCode'] = $this->_receive['OldUserCardCode'];
        if (isset($this->_receive['UserCardCode']) && !empty($this->_receive['UserCardCode'])) //code 序列号。自定义 code 及非自定义 code的卡券被领取后都支持事件推送。
            $array['UserCardCode'] = $this->_receive['UserCardCode'];
        if (isset($array) && count($array) > 0) {
            return $array;
        } else {
            return false;
        }
    }

    /**
     * 获取卡券事件推送 - 删除卡券
     * 当Event为 user_del_card(用户删除卡券)
     * @return array|boolean
     */
    public function getRevCardDel() {
        if (isset($this->_receive['CardId']))     //卡券 ID
            $array['CardId'] = $this->_receive['CardId'];
        if (isset($this->_receive['UserCardCode']) && !empty($this->_receive['UserCardCode'])) //code 序列号。自定义 code 及非自定义 code的卡券被领取后都支持事件推送。
            $array['UserCardCode'] = $this->_receive['UserCardCode'];
        if (isset($array) && count($array) > 0) {
            return $array;
        } else {
            return false;
        }
    }

    /**
     * 获取订单ID - 订单付款通知
     * 当Event为 merchant_order(订单付款通知)
     * @return orderId|boolean
     */
    public function getRevOrderId() {
        if (isset($this->_receive['OrderId']))     //订单 ID
            return $this->_receive['OrderId'];
        else
            return false;
    }
    /**
     * 设置回复消息
     * Example: $obj->text('hello')->reply();
     * @param string $text
     */
    public function text($text = '') {
        $FuncFlag = $this->_funcflag ? 1 : 0;
        $msg = [
            'ToUserName' => $this->getRevFrom(),
            'FromUserName' => $this->getRevTo(),
            'MsgType' => self::MSGTYPE_TEXT,
            'Content' => $this->_auto_text_filter($text),
            'CreateTime' => time(),
            'FuncFlag' => $FuncFlag
        ];

        $this->Message($msg);
        return $this;
    }

    /**
     * 设置回复消息
     * Example: $obj->image('media_id')->reply();
     * @param string $mediaid
     */
    public function image($mediaid = '') {
        $FuncFlag = $this->_funcflag ? 1 : 0;
        $msg = [
            'ToUserName' => $this->getRevFrom(),
            'FromUserName' => $this->getRevTo(),
            'MsgType' => self::MSGTYPE_IMAGE,
            'Image' => ['MediaId' => $mediaid],
            'CreateTime' => time(),
            'FuncFlag' => $FuncFlag
        ];

        $this->Message($msg);
        return $this;
    }

    /**
     * 设置回复消息
     * Example: $obj->voice('media_id')->reply();
     * @param string $mediaid
     */
    public function voice($mediaid = '') {
        $FuncFlag = $this->_funcflag ? 1 : 0;
        $msg = [
            'ToUserName' => $this->getRevFrom(),
            'FromUserName' => $this->getRevTo(),
            'MsgType' => self::MSGTYPE_VOICE,
            'Voice' => ['MediaId' => $mediaid],
            'CreateTime' => time(),
            'FuncFlag' => $FuncFlag
        ];

        $this->Message($msg);
        return $this;
    }

    /**
     * 设置回复消息
     * Example: $obj->video('media_id','title','description')->reply();
     * @param string $mediaid
     */
    public function video($mediaid = '', $title = '', $description = '') {
        $FuncFlag = $this->_funcflag ? 1 : 0;
        $msg = [
            'ToUserName' => $this->getRevFrom(),
            'FromUserName' => $this->getRevTo(),
            'MsgType' => self::MSGTYPE_VIDEO,
            'Video' => [
                'MediaId' => $mediaid,
                'Title' => $title,
                'Description' => $description
            ],
            'CreateTime' => time(),
            'FuncFlag' => $FuncFlag
        ];

        $this->Message($msg);
        return $this;
    }

    /**
     * 设置回复音乐
     * @param string $title
     * @param string $desc
     * @param string $musicurl
     * @param string $hgmusicurl
     * @param string $thumbmediaid 音乐图片缩略图的媒体id，非必须
     */
    public function music($title, $desc, $musicurl, $hgmusicurl = '', $thumbmediaid = '') {
        $FuncFlag = $this->_funcflag ? 1 : 0;
        $msg = [
            'ToUserName' => $this->getRevFrom(),
            'FromUserName' => $this->getRevTo(),
            'CreateTime' => time(),
            'MsgType' => self::MSGTYPE_MUSIC,
            'Music' => [
                'Title' => $title,
                'Description' => $desc,
                'MusicUrl' => $musicurl,
                'HQMusicUrl' => $hgmusicurl
            ],
            'FuncFlag' => $FuncFlag
        ];

        if ($thumbmediaid) {
            $msg['Music']['ThumbMediaId'] = $thumbmediaid;
        }

        $this->Message($msg);
        return $this;
    }

    /**
     * 设置回复图文
     * @param array $newsData
     * 数组结构:
     *  array(
     *  	"0"=>array(
     *  		'Title'=>'msg title',
     *  		'Description'=>'summary text',
     *  		'PicUrl'=>'http://www.domain.com/1.jpg',
     *  		'Url'=>'http://www.domain.com/1.html'
     *  	),
     *  	"1"=>....
     *  )
     */
    public function news($newsData = array()) {
        $FuncFlag = $this->_funcflag ? 1 : 0;
        $count = count($newsData);

        $msg = [
            'ToUserName' => $this->getRevFrom(),
            'FromUserName' => $this->getRevTo(),
            'MsgType' => self::MSGTYPE_NEWS,
            'CreateTime' => time(),
            'ArticleCount' => $count,
            'Articles' => $newsData,
            'FuncFlag' => $FuncFlag
        ];

        $this->Message($msg);
        return $this;
    }

    /**
     *
     * 回复微信服务器, 此函数支持链式操作
     * Example: $this->text('msg tips')->reply();
     * @param string $msg 要发送的信息, 默认取$this->_msg
     * @param bool $return 是否返回信息而不抛出到浏览器 默认:否
     */
    public function reply($msg = [], $return = false) {
        if (empty($msg)) {
            if (empty($this->_msg))   //防止不先设置回复内容，直接调用reply方法导致异常
                return false;
            $msg = $this->_msg;
        }

        $xmldata = $this->xml_encode($msg);
        if ($this->encrypt_type == 'aes') { //如果来源消息为加密方式
            $pc = new Prpcrypt($this->encodingAesKey);
            $array = $pc->encrypt($xmldata, $this->appid);
            $ret = $array[0];
            if ($ret != 0) {
                return false;
            }

            $timestamp = time();
            $nonce = rand(77, 999) * rand(605, 888) * rand(11, 99);
            $encrypt = $array[1];
            $tmpArr = array($this->token, $timestamp, $nonce, $encrypt); //比普通公众平台多了一个加密的密文
            sort($tmpArr, SORT_STRING);
            $signature = implode($tmpArr);
            $signature = sha1($signature);
            $xmldata = $this->generate($encrypt, $signature, $timestamp, $nonce);
        }

        if ($return)
            return $xmldata;
        else
            echo $xmldata;
    }

    /**
     * 设置发送消息
     * @param array $msg 消息数组
     * @param bool $append 是否在原消息数组追加
     */
    public function Message($msg = '', $append = false) {
        if (is_null($msg)) {
            $this->_msg = [];
        } elseif (is_array($msg)) {
            if ($append)
                $this->_msg = array_merge($this->_msg, $msg);
            else
                $this->_msg = $msg;
            return $this->_msg;
        } else {
            return $this->_msg;
        }
    }
    //--------------------------微信服务器通知--------------------------------//
    //--------------------------自定义菜单--------------------------------//

    /**
     * @var 创建菜单
     * @param array $button 菜单按钮 [['name' => '菜单名称','sub_button' => [子菜单],
     'type' => '菜单的响应动作类型，view表示网页类型，click表示点击类型，miniprogram表示小程序类型',
     'key' => 'click等点击类型必须	菜单 KEY 值，用于消息接口推送，不超过128字节',
     'url' => 'view、miniprogram类型必须	网页 链接，用户点击菜单可打开链接，不超过1024字节。 type为 miniprogram 时，不支持小程序的老版本客户端将打开本url',
     'media_id' => 'media_id类型和view_limited类型必须,调用新增永久素材接口返回的合法media_id',
     'appid' => 'miniprogram类型必须	小程序的appid（仅认证公众号可配置）',
     'pagepath' => 'miniprogram类型必须	小程序的页面路径',
     'article_id' => 'article_id类型和article_view_limited类型必须	发布后获得的合法 article_id',
     ]]
     * @param array $matchrule 个性化菜单匹配规则--"tag_id": 用户标签的id ,"client_platform_type": 客户端版本,当前只具体到系统型号：IOS(1), Android(2),Others(3)
     * @return bool
     */
    public function menuByCreate(array $button, $matchrule = []){
        $menu['button'] = $button;
        $url = self::API_MENU_CREATE;
        if(!empty($matchrule)){
            $menu['matchrule'] = $matchrule;
            $url = self::API_MENU_CREATE_PER;
        }

        if(!$result = $this->httpPost($url, $menu, true)){
            return false;
        }

        return true;
    }
    /**
     * @var 菜单查询
     * @param bool $isCustom true 返回通过接口创建的菜单 false 返回接口或者其他方式创建的菜单
     * @return bool|void
     */
    public function menuByfind($isCustom = true){
        $url = $isCustom ? self::API_MENU_SELECT_PER :  self::API_MENU_SELECT;
        return $this->httpGet($url, null, true);
    }
    /**
     * @var 删除菜单
     * @param null $menuid 个体化菜单ID
     * @return bool|void
     */
    public function menuByDel($menuid = null){
        if($menuid){
            $data = ['menuid' => $menuid];
            return $this->httpPost(self::API_MENU_DELETE_PER, $data, true);
        }

        return $this->httpGet(self::API_MENU_DELETE, null, true);
    }
    /**
     * @var 个性化菜单用户匹配结果
     * @param $userId 可以是粉丝的OpenID，也可以是粉丝的微信号。
     * @return bool|array
     */
    public function menuByMatch($userId){
        $data['user_id'] = $userId;
        return $this->httpPost(self::API_MENU_MATCH_PER, $data, true);
    }
    //--------------------------自定义菜单--------------------------------//

    //--------------------------模板消息接口--------------------------------//
    /**
     * @var 添加模板
     * @param string $template_id_short
     * @return bool|mixed
     */
    public function templateByCreate(string $template_id_short){
        $data['template_id_short'] = $template_id_short;
        if(!$result = $this->httpPost(self::API_TEMPLATE_ADD, $data, true)){
            return false;
        }

        return $result['template_id'];
    }
    /**
     * @var 获取模板列表
     * @return false | array [[template_id => '模板ID',title => '模板标题',primary_industry => '模板所属行业的一级行业',deputy_industry => '模板所属行业的二级行业',content => '模板内容',example	 => '模板示例',]]
     */
    public function templateBySelect(){
        if(!$result = $this->httpGet(self::API_TEMPLATE_GET, null, true)){
            return false;
        }

        return $result['template_list'];
    }
    /**
     * @var 删除模板
     * @param string $template_id
     * @return bool
     */
    public function templateByDel(string $template_id){
        $data['template_id'] = $template_id;
        if(!$this->httpPost(self::API_TEMPLATE_DEL, $data, true)){
            return false;
        }

        return true;
    }
    /**
     * @var 发送模板消息
     * @param $touser 用户的openid
     * @param $template_id 模板ID
     * @param array $data ['first' => ['value' => '', 'color']]
     * @param string $url
     * @param array $miniprogram ['appid' => '所需跳转到的小程序appid', 'pagepath' => '所需跳转到小程序的具体页面路径']
     */
    public function templateBySend($touser, $template_id, array $body, $url = '', array $miniprogram = []){
        $data['touser'] = $touser;
        $data['template_id'] = $template_id;
        $data['url'] = $url;
        $data['miniprogram'] = $miniprogram;
        $data['data'] = $body;
        if(!$this->httpPost(self::API_TEMPLATE_SEND, $data, true)){
            return false;
        }

        return true;
    }
    //--------------------------模板消息接口--------------------------------//

    //--------------------------订阅通知接口--------------------------------//
    /**
     * @var 添加小程序的模板
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
     * @var 删除小程序的模板
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

    //------------------------------客服消息--------------------------------//
    /**
     * @var 创建客服
     * @param string $account 账户
     * @param string $password 密码
     * @param string $nickname 昵称
     * @return bool
     */
    public function kfAccountByCreate($kfAccount, $nickname, $password){
        $data['kf_account'] = $kfAccount;
        $data['nickname']   = $nickname;
        $data['password']   = $password;
        if(!$this->httpPost(self::API_CUSTOM_SERVICE_ACCOUNT_ADD, $data, true)){
            return false;
        }

        return true;
    }
    /**
     * @var 修改客服
     * @param string $account 账户
     * @param string $password 密码
     * @param string $nickname 昵称
     */
    public function kfAccountByModify($account, $password, $nickname){
        $data['kf_account'] = $account;
        $data['nickname']   = $nickname;
        $data['password']   = $password;
        if(!$this->httpPost(self::API_CUSTOM_SERVICE_ACCOUNT_UPDATE, $data, true)){
            return false;
        }

        return true;
    }
    /**
     * @var 删除客服
     * @param string $account 账户
     */
    public function kfAccountByDel($account){
        $data['kf_account'] = $account;
        if(!$this->httpPost(self::API_CUSTOM_SERVICE_ACCOUNT_DEL, $data, true)){
            return false;
        }

        return true;
    }
    /**
     * @var 上传客户头像
     * @param string $account
     * @param string $imgPath
     */
    public function kfAccountHeadimgByUpload($account, $imgPath){
        $imgPath = realpath($imgPath);
        if($imgPath == false){
            return $this->setErrors('图片文件不存在!');
        }

        $imgType = pathinfo($imgPath)['extension'];
        $data['headimg'] = class_exists('\CURLFile') ? new \CURLFile($imgPath, $imgType, $account . '.' . $imgType) : '@' . $imgPath;
        $data['kf_account'] = $account;
        if(!$this->httpPost(self::API_CUSTOM_SERVICE_ACCOUNT_HEAD, $data, true, true)){
            return false;
        }

        return true;
    }
    /**
     * @var 获取客服列表
     * @return bool|array
     */
    public function kfAccountBySelect(){
        if(!$result = $this->httpGet(self::API_CUSTOM_SERVICE_ACCOUNT_LIST, null, true)){
            return false;
        }

        return $result['kf_list'];
    }
    /**
     * @var 发送客户消息
     * @param array $data
     * @return bool|void
     */
    public function kfMessageBySend(array $data){
        if(!$this->httpPost(self::API_MESSAGE_CUSTOM_SEND, $data, true)){
            return false;
        }

        return true;
    }
    /**
     * @var 发送文本消息
     * @param $touser
     * @param $content <a href="http://www.qq.com" data-miniprogram-appid="appid" data-miniprogram-path="pages/index/index">点击跳小程序</a>
     */
    public function kfMessageBySendTxt($touser, $content){
        $data['touser'] = $touser;
        $data['msgtype'] = 'text';
        $data['text']['content'] = $content;

        return $this->kfMessageBySend($data);
    }
    /**
     * @var 发送图文消息
     * @param string $touser
     * @param array $articles 1篇图文消息 ["title":"Happy Day",
     "description":"Is Really A Happy Day","url":"URL","picurl":"PIC_URL"]
     */
    public function kfMessageBySendNews($touser, array $articles){
        $data['touser'] = $touser;
        $data['msgtype'] = 'news';
        $data['news']['articles'][] = $articles;

        return $this->sendKfAccount($data);
    }
    /**
     * @var 发送图文消息
     * @param string $touser
     * @param string $media_id
     */
    public function kfMessageBySendNewsMp($touser, $media_id){
        $data['touser'] = $touser;
        $data['msgtype'] = 'mpnews';
        $data['mpnews']['media_id'] = $media_id;

        return $this->sendKfAccount($data);
    }
    /**
     * @var 发送菜单消息
     * @param string $touser
     * @param array $list  //[['id' => 101, 'content' => '满意', ], ['id' => 102, 'content' => '不满意', ]]
     * @param string $head_content //您对本次服务是否满意呢?
     * @param string $tail_content //欢迎再次光临
     */
    public function kfMessageBySendMenu($touser, array $list, $head_content, $tail_content){
        $data['touser'] = $touser;
        $data['msgtype'] = 'msgmenu';
        $data['msgmenu']['head_content'] = $head_content;
        $data['msgmenu']['list'] = $list;
        $data['msgmenu']['tail_content'] = $tail_content;

        return $this->sendKfAccount($data);
    }
    /**
     * @var 发送图片消息
     * @param string $touser
     * @param string $media_id
     */
    public function kfMessageBySendImage($touser, $media_id){
        $data['touser'] = $touser;
        $data['msgtype'] = 'image';
        $data['image']['media_id'] = $media_id;

        return $this->sendKfAccount($data);
    }
    /**
     * @var 发送语音消息
     * @param $touser
     * @param $media_id
     */
    public function kfMessageBySendVoice($touser, $media_id){
        $data['touser'] = $touser;
        $data['msgtype'] = 'voice';
        $data['voice']['media_id'] = $media_id;

        return $this->sendKfAccount($data);
    }
    /**
     * @var 发送视频消息
     * @param string $touser
     * @param string $media_id
     * @param string $thumb_media_id
     * @param string $title
     * @param string $description
     * @return bool|void
     */
    public function kfMessageBySendVodeo($touser, $media_id, $thumb_media_id, $title = '', $description = ''){
        $data['touser'] = $touser;
        $data['msgtype'] = 'video';
        $data['video']['media_id'] = $media_id;
        $data['video']['thumb_media_id'] = $thumb_media_id;
        $data['video']['title'] = $title;
        $data['video']['description'] = $description;

        return $this->sendKfAccount($data);
    }
    /**
     * @var 发送卡券 仅支持非自定义Code码和导入code模式的卡券的卡券
     * @param string $touser
     * @param string $card_id
     * @return bool|void
     */
    public function kfMessageBySendCard($touser, $card_id){
        $data['touser'] = $touser;
        $data['msgtype'] = 'wxcard';
        $data['wxcard']['card_id'] = $card_id;

        return $this->sendKfAccount($data);
    }
    /**
     * @var 发送小程序卡片（要求小程序与公众号已关联）
     * @param string $touser
     * @param string $appid
     * @param string $pagepath
     * @param string $thumb_media_id
     * @param string $title
     */
    public function kfMessageBySendMiniprogrampage($touser, $appid, $pagepath, $thumb_media_id, $title = ''){
        $data['touser'] = $touser;
        $data['msgtype'] = 'miniprogrampage';
        $data['miniprogrampage'] = [
            'title' => $title,
            'appid' => $appid,
            'pagepath' => $pagepath,
            'thumb_media_id' => $thumb_media_id
        ];

        return $this->sendKfAccount($data);
    }
    /**
     * @var 小程序图文消息
     * @param string $title 消息标题
     * @param string $description 图文链接消息
     * @param string $url 图文链接消息被点击后跳转的链接
     * @param string $thumb_url 图文链接消息的图片链接，支持 JPG、PNG 格式，较好的效果为大图 640 X 320，小图 80 X 80
     */
    public function kfMessageBySendLink($title, $description, $url, $thumb_url){
        $data['title'] = $title;
        $data['description'] = $description;
        $data['url'] = $url;
        $data['thumb_url'] = $thumb_url;

        return $this->sendKfAccount($data);
    }
    //------------------------------客服消息--------------------------------//

    //------------------------------用户信息相关--------------------------------//
    /**
     * @var 代公众号授权
     * @param string $redirectUrl 回调地址
     * @param bool $isReturn 是否返回跳转地址
     * @param string $state 自定义携带参数
     * @param string $scope 授权作用域 snsapi_base寂寞授权 snsapi_userinfo 用户授权
     * @return array 用户信息
     */
    public function oAuth2($redirectUrl = '', $state = 'STATE', $isReturn = true, $scope = self::SCOPE_USER_INFO){
        $url = self::API_USER_OAUTH2;
        $url .= 'appid=' . $this->appid;
        $url .= '&redirect_uri=' . urlencode(HelpFunc::getRedirectUrl($redirectUrl));
        $url .= '&response_type=code';
        $url .= '&scope=' . $scope;
        $url .= '&state=' . $state;
        $url .= '#wechat_redirect';
        if ($isReturn) {
            return $url;
        }

        header('location:' . $url);
        exit;
    }
    /**
     * @var 获取用户授权的信息
     * @param string $lang 语言
     * @param bool $isUnionId 是否通过UnionId记者获取
     * @return bool|void
     */
    public function oAuth2UserByFindInfo($isUnionId = false, $lang = self::LANG_ZH_CN){
        if (!$userAccessToken = $this->oAuth2UserByUserAccessToken()) {
            return false;
        }

        if($isUnionId && $info = $this->userInfoByUnionID($userAccessToken['openid'])){
            if($info['subscribe']){
                return $info;
            }
        }

        $url = self::API_OAUTH2_USER_INFO . $userAccessToken['access_token'];
        $url .= '&openid=' . $userAccessToken['openid'];
        $url .= '&lang=' . $lang;
        return $this->httpGet($url);
    }
    /**
     * 获取用户的AccessToken
     * @param string $code 用户授权得到的 $code;
     * @return ok ["access_token":"ACCESS_TOKEN","expires_in":7200,"refresh_token":"REFRESH_TOKEN","openid":"OPENID","scope":"SCOPE"]
     */
    public function oAuth2UserByUserAccessToken($code = null){
        $code = $code ? $code : $_GET['code'];
        $url = self::API_OAUTH2_ACCESS_TOKEN;
        $url .= 'access_token=' . $this->getAccessToken();
        $url .= '&appid=' . $this->appid;
        $url .= '&secret=' . $this->appsecret;
        $url .= '&code=' . $code;
        $url .= '&grant_type=authorization_code';
        return $this->httpGet($url);
    }
    /**
     * @var 通过UnionID机制获取用户信息
     * @param string $openid
     * @param string $lang
     * @return bool|void
     */
    public function userInfoUnionIDByFind($openid, $lang = self::LANG_ZH_CN){
        $url = self::API_USER_UNIONN_ID_INFO;
        $url .= 'openid=' . $openid;
        $url .= '&lang=' . $lang;
        $url .= '&access_token=';

        return $this->httpGet($url, null, true);
    }
    /**
     * @var 批量获取用户信息
     * @param array $openids [['openid' => '', 'lang' => '']]  lang 可不传
     */
    public function userInfoUnionIDBySelect(array $openids){
        if(count($openids) > 100){
            return $this->setErrors(-1, '单次最大拉取100用户信息');
        }

        $data['user_list'] = $openids;
        if(!$list = $this->httpPost(self::API_USER_UNIONN_ID_INFO_LSIT, $data, true)){
            return false;
        }

        return $list['user_info_list'];
    }
    /**
     * @var 拉取关注者的openid列表
     * @param $next_openid
     * @return [
     *     'total' => '',
     *     'count' => '',
     *     'data' => [
     *          'openid' => [
     *
     *          ],
     *      ],
     *      'next_openid' => '',
     * ]
     */
    public function userOpenidBySelect($next_openid = ''){
        $url = self::API_USER_OPENID_LIST;
        $url .= 'next_openid=' . $next_openid;
        $url .= '&access_token=';
        return $this->httpGet($url, null, true);
    }
    /**
     * @var 拉取黑名单列表
     * @param string $begin_openid
     */
    public function userBlackOpenidsBySelect($begin_openid = ''){
        $data['begin_openid'] = $begin_openid;
        return $this->httpPost(self::API_USER_BLACK_OPENID_LIST, $data, true);
    }
    /**
     * @var 批量拉黑用户
     * @param array $openids
     */
    public function userBlackOpenidsBySet($openids = []){
        $data['openid_list'] = $openids;
        if(!$this->httpPost(self::API_USER_BLACK_SET, $data, true)){
            return false;
        }

        return true;
    }
    /**
     * @var 批量取消拉黑用户
     * @param array $openids
     */
    public function userBlackOpenidsByUnSet($openids = []){
        $data['openid_list'] = $openids;
        if(!$this->httpPost(self::API_USER_UN_BLACK_SET, $data, true)){
            return false;
        }

        return true;
    }
    /**
     * @var 获取某个用户打上去的标签
     * @param string $openid
     */
    public function userTagByFind($openid){
        $data['openid'] = $openid;
        if(!$result = $this->httpPost(self::API_TAG_USER_GET ,$data, true)){
            return false;
        }

        return $result['tagid_list'];
    }
    /**
     * @var 获取标签下粉丝列表
     * @param $tag_id
     * @param null $next_openid
     * @return array count next_openid data['openid']=>[]
     */
    public function userTagBySelect($tag_id, $next_openid = ''){
        $data['tagid'] = $tag_id;
        $data['next_openid'] = $next_openid;
        if(!$result = $this->httpPost(self::API_TAG_USER_LIST, $data, true)){
            return false;
        }

        return $result;
    }
    /**
     * @var 批量为用户打标签
     * @param array $openid_list [openid, openid]
     * @param $tagid
     * @return bool
     */
    public function usersTagBySet($openid_list = [], $tagid){
        $data['openid_list'] = $openid_list;
        $data['tagid'] = $tagid;
        if(!$this->httpPost(self::API_TAG_USER_ADDS, $data, true)){
            return false;
        }

        return true;
    }
    /**
     * @var 批量为用户取消标签
     * @param array $openid_list 最多50个
     * @param $tagid
     */
    public function usersTagByUnSet($openid_list = [], $tagid){
        $data['openid_list'] = $openid_list;
        $data['tagid'] = $tagid;
        return $this->httpPost(self::API_TAG_USER_UNADDS, $data, true);
    }
    //------------------------------用户信息相关--------------------------------//
    
    //------------------------------标签相关--------------------------------//
    /**
     * @var 创建标签
     * @param string $name 标签名称
     * @return bool|void
     */
    public function tagByCreate($name){
        $data['tag']['name'] = $name;
        if(!$result = $this->httpPost(self::API_TAG_CREATE, $data, true)){
            return false;
        }

        return $result['tag']['id'];
    }
    /**
     * @var 获取标签列表
     * @return bool|void
     */
    public function tagBySelect(){
        if(!$result = $this->httpGet(self::API_TAG_LSIT, null, true)){
            return false;
        }

        return $result['tags'];
    }
    /**
     * @var 修改标签
     * @param string $tag_id
     * @param string $name
     */
    public function tagByModify($tag_id, $name){
        $data['tag']['id'] = $tag_id;
        $data['tag']['name'] = $name;
        if(!$this->httpPost(self::API_TAG_UPDATE, $data, true)){
            return false;
        }

        return true;
    }
    /**
     * @var 删除标签
     * @param $tag_id
     */
    public function tagByDel($tag_id){
        $data['tag']['id'] = $tag_id;
        if(!$this->httpPost(self::API_TAG_DELETE, $data, true)){
            return false;
        }

        return true;
    }

    //------------------------------标签相关--------------------------------//

    //------------------------------素材管理--------------------------------//
    /**
     * @var 上传--图片视频语音缩略图
     * @param string $imgPath
     * @param string $name
     * @return bool | url
     */
    public function materialImgUrlByUpload($imgPath, $name = '图片'){
        $imgPath = realpath($imgPath);
        if($imgPath == false){
            return $this->setErrors(-1, '图片文件不存在!');
        }

        $imgType = pathinfo($imgPath)['extension'];
        $data['media'] = class_exists('\CURLFile') ? new \CURLFile($imgPath, $imgType, $name . '.' . $imgType) : '@' . $imgPath;
        if(!$result = $this->httpPost(self::API_MATERIAL_UPLOAD_IMG, $data, true, true)){
            return false;
        }

        return $result['url'];
    }
    /**
     * @var 上传--图片视频语音缩略图
     * @param string $imgPath 文件地址
     * @param string $type 图片（image）、语音（voice）、视频（video）和缩略图（thumb）
     * @param string $name 文件名称
     * @param string $introduction 视频描述
     * @return array media_id新增的永久素材的media_id url新增的图片素材的图片URL（仅新增图片素材时会返回该字段）
     */
    public function materialMediaIdByUpload($imgPath, $name = '图片', $type = self::TYPE_IMAGE, $introduction = null){
        $imgPath = realpath($imgPath);
        if($imgPath == false){
            return $this->setErrors(-1, '文件不存在!');
        }

        $imgType = pathinfo($imgPath)['extension'];
        $data['media'] = class_exists('\CURLFile') ? new \CURLFile($imgPath, $imgType, $name . '.' . $imgType) : '@' . $imgPath;
        $data['type'] = $type;
        if($type == self::TYPE_VIDEO){
            $data['title'] = $name;
            $data['introduction'] = $introduction;
        }

        $url = self::API_MATERIAL_UPLOAD;
        if(!$result = $this->httpPost($url, $data, true, true)){
            return false;
        }

        return $result;
    }
    /**
     * @var 删除素材
     * @param $media_id
     * @return bool
     */
    public function materialMediaIdByDel($media_id){
        $data['media_id'] = $media_id;
        if(!$result = $this->httpPost(self::API_MATERIAL_DEL, $data, true)){
            return false;
        }

        return true;
    }
    /**
     * @var 获取永久素材的总数，也会计算公众平台官网素材管理中的素材 2.图片和图文消息素材（包括单图文和多图文）的总数上限为5000，其他素材的总数上限为1000
     * @return bool|array voice_count 语音总数量 video_count	视频总数量 image_count	图片总数量 news_count	图文总数量
     */
    public function materialMediaIdByTotal(){
        return $this->httpGet(self::API_MATERIAL_GET_COUNT, null, true);
    }
    /**
     * @var 获取素材列表
     * @param string $type 素材的类型，图片（image）、视频（video）、语音 （voice）、图文（news）
     * @param int $offset 从全部素材的该偏移位置
     * @param int $count 每次拉取多少条数据， 最大20
     */
    public function  materialMediaIdBySelect($type = self::TYPE_IMAGE, $offset = 0, $count = 20){
        $data['type'] = $type;
        $data['offset'] = $offset;
        $data['count'] = $count;

        return $this->httpPost(self::API_MATERIAL_BATCH_GET, $data, true);
    }
    /**
     * 上传草稿素材
     * @param array $articles[] = []二维数组
     *[
        title	是	图文消息的标题
        author	否	图文消息的作者
        digest	否	图文消息的描述，如本字段为空，则默认抓取正文前64个字
        content	是	图文消息页面的内容，支持HTML标签
        content_source_url	否	在图文消息页面点击“阅读原文”后的页面，受安全限制，如需跳转Appstore，可以使用itun.es或appsto.re的短链服务，并在短链后增加 #wechat_redirect 后缀。
        thumb_media_id: 是	图文消息缩略图的media_id，可以在素材管理-新增素材中获得
        need_open_comment	否	Uint32 是否打开评论，0不打开，1打开
        only_fans_can_comment	否	Uint32 是否粉丝才可评论，0所有人可评论，1粉丝才可评论
    ]
     * @return bool| media_id
     */
    public function materialDraftByCreate($articles = []){
        $data['articles'] = $articles;
        if(!$result = $this->httpPost(self::API_MATERIAL_DRAFT_ADD, $data, true)){
            return false;
        }

        return $result['media_id'];
    }
    /**
     * @var 获取某个草稿详情
     * @param $media_id
     * @return bool|mixed
     */
    public function materialDraftByFind($media_id){
        $data['media_id'] = $media_id;
        if(!$result = $this->httpPost(self::API_MATERIAL_DRAFT_GET, $data, true)){
            return false;
        }

        return $result['news_item'];
    }
    /**
     * @var 删除草稿
     * @param $media_id
     * @return bool
     */
    public function materialDraftByDel($media_id){
        $data['media_id'] = $media_id;
        if(!$result = $this->httpPost(self::API_MATERIAL_DRAFT_DEL, $data, true)){
            return false;
        }

        return true;
    }
    /**
     * @var 草稿修改
     * @param string $media_id 要修改的图文消息的id
     * @param $index 要更新的文章在图文消息中的位置（多图文消息时，此字段才有意义），第一篇为0
     * @param array $articles
     * @return bool
     */
    public function materialDraftByModify($media_id, $index, array $articles){
        $data['media_id'] = $media_id;
        $data['index'] = $index;
        $data['articles'] = $articles;
        if(!$result = $this->httpPost(self::API_MATERIAL_DRAFT_UPDATE, $data, true)){
            return false;
        }

        return true;
    }
    /**
     * @var 获取草稿总数
     * @return bool|mixed
     */
    public function materialDraftByCount(){
        if(!$result = $this->httpGet(self::API_MATERIAL_DRAFT_COUNT, null, true)){
            return false;
        }

        return $result['total_count'];
    }
    /**
     * @param int $offset 从全部素材的该偏移位置开始返回，0表示从第一个素材返回
     * @param int $count 返回素材的数量，取值在1到20之间
     * @param int $no_content 1 表示不返回 content 字段，0 表示正常返回，默认为 0
     * @return bool|void|null
     */
    public function materialDraftBySelect($offset = 0, $count = 20, $no_content = 0){
        $data['offset'] = $offset;
        $data['count'] = $count;
        $data['no_content'] = $no_content;
        if(!$result = $this->httpPost(self::API_MATERIAL_DRAFT_LIST, $data, true)){
            return false;
        }

        return $result;
    }
    /**
     * @var 发布草稿
     * @param string $media_id
     * @return bool|publish_id 发布任务id
     */
    public function materialDraftReleaseBySend($media_id){
        $data['media_id'] = $media_id;
        if(!$result = $this->httpPost(self::API_FREEPUBLISH_SUBMIT, $data, true)){
            return false;
        }

        return $result['publish_id'];
    }
    /**
     * @var 查询发表状态
     * @param string $media_id
     * @return [
        publish_id	发布任务id
        publish_status	发布状态，0:成功, 1:发布中，2:原创失败, 3: 常规失败, 4:平台审核不通过, 5:成功后用户删除所有文章, 6: 成功后系统封禁所有文章
        article_id	当发布状态为0时（即成功）时，返回图文的 article_id，可用于“客服消息”场景
        count	当发布状态为0时（即成功）时，返回文章数量
        idx	当发布状态为0时（即成功）时，返回文章对应的编号
        article_url	当发布状态为0时（即成功）时，返回图文的永久链接
        fail_idx	当发布状态为2或4时，返回不通过的文章编号，第一篇为 1；其他发布状态则为空
     ]
     */
    public function materialDraftReleaseByStatus($media_id){
        $data['media_id'] = $media_id;
        if(!$result = $this->httpPost(self::API_FREEPUBLISH_GET, $data, true)){
            return false;
        }

        return $result;
    }
    /**
     * @var 删除发布
     * @param string $article_id 成功发布时返回的 article_id
     * @param null $index 要删除的文章在图文消息中的位置，第一篇编号为1，该字段不填或填0会删除全部文章
     * @return bool
     */
    public function materialDraftReleaseByDel($article_id, $index = null){
        $data['article_id'] = $article_id;
        $data['index'] = $index;
        if(!$result = $this->httpPost(self::API_FREEPUBLISH_DELETE, $data, true)){
            return false;
        }

        return true;
    }
    /**
     * @var 通过 article_id 获取已发布文章
     * @param string $article_id 要获取的草稿的article_id
     * @return bool|void|null
     */
    public function materialDraftReleaseByFind($article_id){
        $data['article_id'] = $article_id;
        if(!$result = $this->httpPost(self::API_FREEPUBLISH_FIND, $data, true)){
            return false;
        }

        return $result;
    }
    /**
     * @var 获取成功发布列表
     * @param int $offset 从全部素材的该偏移位置开始返回，0表示从第一个素材返回
     * @param int $count 返回素材的数量，取值在1到20之间
     * @param int $no_content  表示不返回 content 字段，0 表示正常返回，默认为 0
     * @return bool|void|null
     */
    public function materialDraftReleaseBySelect($offset = 0, $count = 20, $no_content = 0){
        $data['offset'] = $offset;
        $data['count'] = $count;
        $data['no_content'] = $no_content;
        if(!$result = $this->httpPost(self::API_FREEPUBLISH_LIST, $data, true)){
            return false;
        }

        return $result;
    }
    //------------------------------素材管理--------------------------------//

    //------------------------------素材评论--------------------------------//
    /**
     * @var 打开已群发文章评论
     * @param $msg_data_id 群发返回的msg_data_id
     * @param null $index 多图文时，用来指定第几篇图文，从0开始，不带默认返回该msg_data_id的第一篇图文
     * @return bool
     */
    public function commentByOpen($msg_data_id, $index = null){
        $data['msg_data_id'] = $msg_data_id;
        !$index OR $data['index'] = $index;
        if(!$this->httpPost(self::API_COMMENT_OPEN, $data, true)){
            return false;
        }

        return true;
    }
    /**
     * @var 关闭已群发文章评论
     * @param $msg_data_id 群发返回的msg_data_id
     * @param null $index 多图文时，用来指定第几篇图文，从0开始，不带默认返回该msg_data_id的第一篇图文
     * @return bool
     */
    public function commentByClose($msg_data_id, $index = null){
        $data['msg_data_id'] = $msg_data_id;
        !$index OR $data['index'] = $index;

        if(!$this->httpPost(self::API_COMMENT_CLOSE, $data, true)){
            return false;
        }

        return true;
    }
    /**
     * @var 获取评论列表
     * @param $msg_data_id 群发返回的msg_data_id
     * @param int $begin 起始位置
     * @param int $type type=0 普通评论&精选评论 type=1 普通评论 type=2 精选评论
     * @param int $index 多图文时，用来指定第几篇图文，从0开始，不带默认返回该msg_data_id的第一篇图文
     * @param int $count 获取数目（>=50会被拒绝）
     * @return false | array "total": TOTAL          //总数，非 comment 的size around
    "comment": [{
    "user_comment_id" : USER_COMMENT_ID  	//用户评论id
    "openid": OPENID        				//openid
    "create_time": CREATE_TIME    			//评论时间
    "content": CONTENT            			//评论内容
    "comment_type": IS_ELECTED   			//是否精选评论，0为即非精选，1为true，即精选
    "reply": {
    "content": CONTENT       		//作者回复内容
    "create_time" : CREATE_TIME  	//作者回复时间
    }
    }]
    }
     */
    public function commentBySelect($msg_data_id, $begin = 0, $type = self::TYPE_ALL, $index = 0, $count = 49){
        $data['msg_data_id'] = $msg_data_id;
        $data['index'] = $index;
        $data['begin'] = $begin;
        $data['type'] = $type;

        return $this->httpPost(self::API_COMMENT_LIST, $data, true);
    }
    /**
     * @var 评价加精
     * @param $msg_data_id 群发返回的msg_data_id
     * @param $user_comment_id 评论id
     * @param int $index 多图文时，用来指定第几篇图文，从0开始，不带默认返回该msg_data_id的第一篇图文
     * @return bool
     */
    public function commentFeaturedBySet($msg_data_id, $user_comment_id, $index = 0){
        $data['msg_data_id'] = $msg_data_id;
        $data['index'] = $index;
        $data['user_comment_id'] = $user_comment_id;
        if(!$this->httpPost(self::API_COMMENT_MARKELECT, $data, true)){
            return false;
        }

        return true;
    }
    /**
     * @var 评价取精
     * @param $msg_data_id 群发返回的msg_data_id
     * @param $user_comment_id 评论id
     * @param int $index 多图文时，用来指定第几篇图文，从0开始，不带默认返回该msg_data_id的第一篇图文
     * @return bool|void
     */
    public function commentFeaturedByUnSet($msg_data_id, $user_comment_id, $index = 0){
        $data['msg_data_id'] = $msg_data_id;
        $data['index'] = $index;
        $data['user_comment_id'] = $user_comment_id;
        if(!$this->httpPost(self::API_COMMENT_UN_MARKELECT, $data, true)){
            return false;
        }

        return true;
    }
    /**
     * @var 删除评论
     * @param $msg_data_id 群发返回的msg_data_id
     * @param $user_comment_id 评论id
     * @param int $index 多图文时，用来指定第几篇图文，从0开始，不带默认返回该msg_data_id的第一篇图文
     * @return bool|void
     */
    public function commentByDel($msg_data_id, $user_comment_id, $index = 0){
        $data['msg_data_id'] = $msg_data_id;
        $data['index'] = $index;
        $data['user_comment_id'] = $user_comment_id;
        if(!$this->httpPost(self::API_COMMENT_DELETE, $data, true)){
            return false;
        }

        return true;
    }
    /**
     * @var 回复评论
     * @param $content
     * @param $msg_data_id 群发返回的msg_data_id
     * @param $user_comment_id 评论id
     * @param int $index 多图文时，用来指定第几篇图文，从0开始，不带默认返回该msg_data_id的第一篇图文
     */
    public function commentReplyByCreate($content, $msg_data_id, $user_comment_id, $index = 0){
        $data['content'] = $content;
        $data['msg_data_id'] = $msg_data_id;
        $data['index'] = $index;
        $data['user_comment_id'] = $user_comment_id;
        return $this->httpPost(self::API_COMMENT_REPLY, $data, true);
    }
    /**
     * @var 删除回复评论
     * @param $msg_data_id 群发返回的msg_data_id
     * @param $user_comment_id 评论id
     * @param int $index 多图文时，用来指定第几篇图文，从0开始，不带默认返回该msg_data_id的第一篇图文
     */
    public function commentReplyByDel($msg_data_id, $user_comment_id, $index = 0){
        $data['msg_data_id'] = $msg_data_id;
        $data['index'] = $index;
        $data['user_comment_id'] = $user_comment_id;
        if(!$this->httpPost(self::API_COMMENT_DEL_REPLY, $data, true)){
            return false;
        }

        return true;
    }
    //------------------------------素材评论--------------------------------//

    //------------------------------群发管理--------------------------------//
    /**
     * @var 消息群发
     * @param array $data
     * @return bool|array msg_id 消息发送任务的ID msg_data_id 消息的数据ID，该字段只有在群发图文消息时，才会出现。可以用于在图文分析数据接口中，获取到对应的图文消息的数据，是图文分析数据接口中的 msgid 字段中的前半部分，详见图文分析数据接口中的 msgid 字段的介绍。
     */
    public function massBySend(array $data){
        if(!$result = $this->httpPost(self::API_MESSAGE_MASS, $data, true)){
            return false;
        }

        return $result;
    }
    /**
     * @var 群发图文消息
     * @param $media_id 图文消息的media_id
     * @param int $send_ignore_reprint 当判断转载的时候是否继续群发 0 停止， 1继续
     * @param null $tag_id 要发送的用户标签群体
     */
    public function massBySendNewsMp($media_id, $send_ignore_reprint = 1, $tag_id = null){
        $data['filter']['is_to_all'] = $tag_id ? false : true;
        $data['filter']['tag_id'] = $tag_id;
        $data['mpnews']['media_id'] = $media_id;
        $data['msgtype']= 'mpnews';
        $data['send_ignore_reprint'] = $send_ignore_reprint;

        return $this->massBySend($data);
    }
    /**
     * @var 群发文本消息
     * @param string$content 文本内容
     * @param null $tag_id 要发送的用户标签群体
     */
    public function massBySendText($content,  $tag_id = null){
        $data['filter']['is_to_all'] = $tag_id ? false : true;
        $data['filter']['tag_id'] = $tag_id;
        $data['text']['content'] = $content;
        $data['msgtype']= 'text';

        return $this->massBySend($data);
    }
    /**
     * @var 群发卡劵消息
     * @param string$content 文本内容
     * @param null $tag_id 要发送的用户标签群体
     */
    public function massBySendCard($card_id,  $tag_id = null){
        $data['filter']['is_to_all'] = $tag_id ? false : true;
        $data['filter']['tag_id'] = $tag_id;
        $data['msgtype']= 'wxcard';
        $data['wxcard']['card_id'] = $card_id;

        return $this->massBySend($data);
    }
    /**
     * @var 删除群发消息
     * @param string $msg_id 发送出去的消息ID
     * @param null $article_idx 要删除的文章在图文消息中的位置，第一篇编号为1，该字段不填或填0会删除全部文章
     */
    public function massSendByDel($msg_id,  $article_idx = null){
        $data['msg_id'] = $msg_id;
        $data['article_idx'] = $article_idx;
        if(!$this->httpPost(self::API_MESSAGE_MASS_DEL, $data, true)){
            return false;
        }

        return true;
    }
    /**
     * @var 查询群发消息发送状态
     * @param $msg_id
     */
    public function getSendMass($msg_id){
        $data['msg_id'] = $msg_id;

        return $this->httpPost(self::API_MESSAGE_MASS_GET, $data, true);
    }
    /**
     * @var 发送消息预览
     * @param array $data
     * @return bool|void
     */
    public function massPreviewBySend(array $data){
        if(!$this->httpPost(self::API_MESSAGE_MASS_PREVIEW, $data, true)){
            return false;
        }

        return true;
    }
    /**
     * @var 预览图文消息
     * @param string $touser
     * @param string $media_id
     * @return bool|void
     */
    public function massPreviewBySendNewsMp($touser, $media_id){
        $data['touser'] = $touser;
        $data['mpnews']['media_id'] = $media_id;
        $data['msgtype'] = 'mpnews';
        return $this->massPreviewBySend($data);
    }
    /**
     * @var 预览文本消息
     * @param string $touser
     * @param string $content
     * @return bool|void
     */
    public function massPreviewBySendText($touser, $content){
        $data['touser'] = $touser;
        $data['text']['content'] = $content;
        $data['msgtype'] = 'text';

        return $this->massPreviewBySend($data);
    }

    //------------------------------群发管理--------------------------------//
    //------------------------------二维码--------------------------------//
    /**
     * @var 获取二维码
     * @param bool $actionName 二维码类型， QR_SCENE QR_STR_SCENE QR_LIMIT_SCENE QR_LIMIT_STR_SCENE
     * QR_SCENE为临时的整型参数值，QR_STR_SCENE为临时的字符串参数值，
     * QR_LIMIT_SCENE为永久的整型参数值，QR_LIMIT_STR_SCENE为永久的字符串参数值
     * @param $scene 二维码场景值
     * @param int $expireTime 临时二维码时长
     */
    public function qrcodeByCreate($actionName, $scene = null, $expireTime = 2592000){
        $data['action_name'] = $actionName;
        switch($actionName){
            case self::QR_SCENE :
                $data['action_info']['scene']['scene_id'] = intval($scene);
                $data['expire_seconds'] = $expireTime;
                break;
            case self::QR_STR_SCENE:
                $data['action_info']['scene']['scene_str'] = $scene;
                $data['expire_seconds'] = $expireTime;
                break;
            case self::QR_LIMIT_SCENE :
                $data['action_info']['scene']['scene_id'] = intval($scene);
                break;
            case self::QR_LIMIT_STR_SCENE :
                $data['action_info']['scene']['scene_str'] = $scene;
                break;
            default:
                return $this->setErrors(-3, '二维码类型错误!');
        }

        return $this->httpPost(self::API_QRCODE_URL, $data, true);
    }
    /**
     * @var 获取二维码图片
     * @param string $ticket 传入由getQRCode方法生成的ticket参数
     * @return string url 返回https地址
     */
    public function qrcodeUrlByShow($ticket) {
        return self::API_QRCODE_IMG_URL . urlencode($ticket);
    }
    //------------------------------二维码--------------------------------//
    //------------------------------接口归零-智能接口--------------------------------//
    /**
     * @var 接口调用数进行清零
     * @param string $appid
     * @return bool|void
     */
    public function clearApiCount($appid = ''){
        $data['appid'] = $appid ? $appid : $this->appid;
        if(!$this->httpPost(self::API_CLEAR_COUNT, $data, true)){
            return false;
        }

        return true;
    }
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
     * @param \Closure|null $callable
     * @return bool
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
     * @param \Closure|null $callable
     * @return bool
     */
    public function refreshAccessToken(){
        $event = new Event();
        $event->sender = $this;
        $this->trigger(self::EVENT_BEFORE_REFRESH_ACCESS_TOKEN, $event);
        if($event->handled === true){
            return $event->data['access_token'] ?? '';
        }

        $url    = self::API_TOKEN_URL;
        $url   .= 'appid=' . $this->appid;
        $url   .= '&secret=' . $this->appsecret;
        $url   .= '&grant_type=client_credential';
        $result = $this->httpGet($url);
        if(!$result || !isset($result['access_token'])){
            return false;
        }

        $this->trigger(self::EVENT_AFTER_REFRESH_ACCESS_TOKEN, $result['access_token']);
        return $result['access_token'];
    }
    //--------------------------自定义菜单--------------------------------//
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
            if($this->getErrorCode() == 42001 || $this->getErrorCode() == 40001){
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