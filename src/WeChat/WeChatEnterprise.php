<?php
namespace Kang\Libs\WeChat;

use Kang\Libs\Base\Component;
use Kang\Libs\Base\Event;
use Kang\Libs\Helper\Curl;

/**
 * @see url https://developer.work.weixin.qq.com/document/path/90664
 * Class WeChatEnterprise
 * @property string $corpid 企业ID
 * @property string $corpsecret 应用的凭证密钥
 * @property string $accessToken 应用的凭证密钥
 * @package Kang\Libs\WeChat
 */
class WeChatEnterprise extends Component{
    const EVENT_AFTER_REFRESH_ACCESS_TOKEN = 'afterRefreshAccessToken';
    const EVENT_BEFORE_REFRESH_ACCESS_TOKEN = 'beforeRefreshAccessToken';
    const EVENT_ACCESS_TOKEN_CACHE_GET = 'AccessTokenGetCache'; //获取AccessToken缓存

    //--------------------------通讯录-成员--管理--------------------------------//
    /**
     * @param array $data =
    [
        'userid' => 'zhangsan', //成员UserID。对应管理端的帐号，企业内必须唯一。长度为1~64个字节。只能由数字、字母和“_-@.”四种字符组成，且第一个字符必须是数字或字母。系统进行唯一性检查时会忽略大小写。
        'name' => '张三', //成员名称。长度为1~64个utf8字符
        'department' => [1, 2], //成员所属部门id列表，不超过100个
        //意向非必须
        'alias' => 'jackzhang', //成员别名。长度1~64个utf8字符
        'mobile' => '+86 13800000000', //手机号码。企业内必须唯一，mobile/email二者不能同时为空
        'order' => [10, 40], //部门内的排序值，默认为0，成员次序以创建时间从小到大排列。个数必须和参数department的个数一致，数值越大排序越前面。有效的值范围是[0, 2^32)
        'position' => '产品经理', //职务信息。长度为0~128个字符
        'gender' => '1', //性别。1表示男性，2表示女性
        'email' => 'zhangsan@gzdev . com', //邮箱。长度6~64个字节，且为有效的email格式。企业内必须唯一，mobile/email二者不能同时为空
        'biz_mail' => 'zhangsan@qyycs2 . wecom . work', //企业邮箱。仅对开通企业邮箱的企业有效。长度6~64个字节，且为有效的企业邮箱格式。企业内必须唯一。未填写则系统会为用户生成默认企业邮箱（由系统生成的邮箱可修改一次，2022年4月25日之后创建的成员需通过企业管理后台-协作-邮件-邮箱管理-成员邮箱修改）
        'is_leader_in_dept' => [1, 0], //个数必须和参数department的个数一致，表示在所在的部门内是否为部门负责人。1表示为部门负责人，0表示非部门负责人。在审批(自建、第三方)等应用里可以用来标识上级审批人
        'direct_leader' => ['lisi', 'wangwu'], //直属上级UserID，设置范围为企业内成员，可以设置最多5个上级
        'enable' => 1, //启用/禁用成员。1表示启用成员，0表示禁用成员
        'avatar_mediaid' => '2 - G6nrLmr5EC3MNb_ - zL1dDdzkd0p7cNliYu9V5w7o8K0', //成员头像的mediaid，通过素材管理接口上传图片获得的mediaid
        'telephone' => '020 - 123456', //座机。32字节以内，由纯数字、“-”、“+”或“,”组成。
        'address' => '广州市海珠区新港中路', //地址。长度最大128个字符
        'main_department' => 1, //主部门
        'extattr' => [ //自定义字段。自定义字段需要先在WEB管理端添加，见扩展属性添加方法，否则忽略未知属性的赋值。
        ]
     * @return bool
     */
    public function userByCreate($data){
        if(!$result = $this->httpPost(self::USER_CREATE_URL, $data, true)){
            return false;
        }

        return true;
    }
    /**
     * @var 更新成员
     * @param array $data 查看添加
     * @return bool
     */
    public function userByUpdate($data){
        if(!$result = $this->httpPost(self::USER_UPDATE_URL, $data, true)){
            return false;
        }

        return true;
    }
    /**
     * @var 获取成员信息
     * @param string $userid
     * @return bool|void
     */
    public function userByFind($userid){
        $url = self::USER_FIND_URL . '&userid=' . $userid;
        return $this->httpGet($url, null, true);
    }
    /**
     * @var 删除成员信息
     * @param string $userid
     * @return bool
     */
    public function userByDel($userid){
        $url = self::USER_DEL_URL . '&userid=' . $userid;
        if($this->httpGet($url, null, true)){
            return true;
        }

        return false;
    }

    /**
     * @var 批量删除成员信息
     * @param array $userIds ['sdfs', 'dadad']
     * @return bool
     */
    public function userByBatchDel($userIds){
        if($this->httpPost(self::USER_BATCH_DEL_URL, $userIds, true)){
            return true;
        }

        return false;
    }
    /**
     * @var 获取部门成员
     * @param string $department_id
     * @return bool|mixed
     */
    public function userByDepartmentSelect($department_id){
        $url = self::USER_DEP_LIST_URL . '&department_id=' . $department_id;
        if($result = $this->httpGet($url, null, true)){
            return $result['userlist'];
        }

        return false;
    }
    /**
     * @var 获取部门成员明细
     * @param string $department_id
     * @return bool|mixed
     */
    public function userInfoByDepartmentSelect($department_id){
        $url = self::USER_LIST_URL . '&department_id=' . $department_id;
        if($result = $this->httpGet($url, null, true)){
            return $result['userlist'];
        }

        return false;
    }
    /**
     * @var userId 转换为 openid
     * @param string $userid
     * @return bool|mixed
     */
    public function userIdToOpenid($userid){
        $data['userid'] = $userid;
        if($result = $this->httpPost(self::USER_ID_TO_OPENID_URL, $data, true)){
            return $result['openid'];
        }

        return false;
    }
    /**
     * @var openid 转换为 userId
     * @param $openid
     * @return bool|mixed
     */
    public function openIdToUserId($openid){
        $data['openid'] = $openid;
        if($result = $this->httpPost(self::OPENID_TO_USER_ID_URL, $data, true)){
            return $result['userid'];
        }

        return false;
    }
    /**
     * @var 用户二次验证 如果成员是首次加入企业，企业获取到userid，并验证了成员信息后，调用如下接口即可让成员成功加入企业
     * @param $userid
     * @return bool
     */
    public function userByAuth($userid){
        $url = self::USER_AUTH_URL . '&userid=' . $userid;
        if($this->httpGet($url, null, true)){
            return true;
        }

        return false;
    }
    /**
     * @var 企业可通过接口批量邀请成员使用企业微信，邀请后将通过短信或邮件下发通知。
     * @param array $user 成员ID列表, 最多支持1000个
     * @param array $party 部门ID列表，最多支持100个。
     * @param array $tag 标签ID列表，最多支持100个。
     * @return bool
     */
    public function userInviteByBatch($user = [], $party = [], $tag = []){
        $data['user'] = $user;
        $data['party'] = $party;
        $data['tag'] = $tag;
        if($this->httpPost(self::USER_BATCH_INVITE_URL, $data, true)){
            return true;
        }

        return false;
    }
    /**
     * @var 获取加入企业二维码
     * @param string $size_type 1: 171 x 171   2: 399 x 399   3: 741 x 741   4: 2052 x 2052
     * @return bool|mixed
     */
    public function userInviteByQrcode($size_type = 2){
        $url = self::USER_QRCODE_INVITE_URL . '&size_type' . $size_type;
        if($result = $this->httpGet($url, null, true)){
            return $result['join_qrcode'];
        }

        return false;
    }
    /**
     * @vasr 手机号获取userid
     * @param string $mobile
     * @return bool|mixed
     */
    public function userIdByMobile($mobile){
        $data['mobile'] = $mobile;
        if($result = $this->httpPost(self::USER_ID_GET_URL, $data, true)){
            return $result['userid'];
        }

        return false;
    }
    /**
     * @var 邮箱获取userid
     * @param string $email
     * @param null $email_type 邮箱类型：1-企业邮箱（默认）；2-个人邮箱
     * @return bool|mixed
     */
    public function userIdByEmail($email, $email_type = null){
        $data['email'] = $email;
        $data['email_type'] = $email_type;
        if($result = $this->httpPost(self::USER_ID_GET_EMAIL_URL, $data, true)){
            return $result['userid'];
        }

        return false;
    }

    /**
     * @var 获取成员ID列表
     * @param null $cursor 用于分页查询的游标，字符串类型，由上一次调用返回，首次调用不填
     * @param int $limit 分页，预期请求的数据量，取值范围 1 ~ 10000
     * @return bool| ['next_cursor' => '', 'dept_user' => [ [userid, 'department']]]
     */
    public function userIdsBySelect($cursor = null, $limit = 10000){
        $data['cursor'] = $cursor;
        $data['limit'] = $limit;
        if($result = $this->httpPost(self::USER_ID_SELECT_URL, $data, true)){
            return $result;
        }

        return false;
    }
    //--------------------------通讯录-成员--管理--------------------------------//

    //--------------------------通讯录-部门--管理--------------------------------//

    //--------------------------通讯录-部门--管理--------------------------------//
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

        $url    = self::GET_TOKEN;
        $url   .= 'corpid=' . $this->corpid;
        $url   .= '&corpsecret=' . $this->corpsecret;
        $result = $this->httpGet($url);
        if(!$result || !isset($result['access_token'])){
            return false;
        }

        $event->data = $result;
        $this->trigger(self::EVENT_AFTER_REFRESH_ACCESS_TOKEN, $event);
        return $result['access_token'];
    }
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
        $requestUrl = self::BASE_URL . $url;
        $event = new Event();
        $event->data['url'] = $url;
        $event->data['method'] = $method;
        $event->data['data'] = $data;
        $this->trigger(self::EVENT_lOG, $event);

        $this->parseRequestData($data, $method, $isFile);
        if ($autoToken) {
            if(!$token = $this->accessToken) {
                return false;
            }

            $requestUrl = sprintf($requestUrl, $token);
        }

        $curl = Curl::getInstall();
        !$useCert OR $curl->setSslCert($this->sslCertPath, $this->sslKeyPath, 'PEM', false);

        $result = $curl->request($requestUrl, $data, $method);
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

    public function setAccessToken($accessToken){
        $this->_config['accessToken'] = $accessToken;
        return $this;
    }
    public function setCorpid($corpid){
        $this->_config['corpid'] = $corpid;
        return $this;
    }
    public function setCorpsecret($corpsecret){
        $this->_config['corpsecret'] = $corpsecret;
        return $this;
    }

    const BASE_URL = 'https =>//qyapi.weixin.qq.com/';
    const GET_TOKEN = 'cgi-bin/gettoken?';

    const USER_CREATE_URL = 'cgi-bin/user/create?access_token=%s';
    const USER_UPDATE_URL = 'cgi-bin/user/update?access_token=%s';
    const USER_FIND_URL = 'cgi-bin/user/get?access_token=%s';
    const USER_DEL_URL = 'cgi-bin/user/delete?access_token=%s';
    const USER_BATCH_DEL_URL = 'cgi-bin/user/batchdelete?access_token=%s';
    const USER_DEP_LIST_URL = 'cgi-bin/user/simplelist?access_token=%s';
    const USER_LIST_URL = 'cgi-bin/user/list?access_token=%s';
    const USER_ID_TO_OPENID_URL = 'cgi-bin/user/convert_to_openid?access_token=%s';
    const OPENID_TO_USER_ID_URL = 'cgi-bin/user/convert_to_userid?access_token=%s';
    const USER_AUTH_URL = 'cgi-bin/user/authsucc?access_token=%s';
    const USER_BATCH_INVITE_URL = 'cgi-bin/batch/invite?access_token=%s';
    const USER_QRCODE_INVITE_URL = 'cgi-bin/corp/get_join_qrcode?access_token=%s';
    const USER_ID_GET_URL = 'cgi-bin/user/getuserid?access_token=%s';
    const USER_ID_GET_EMAIL_URL = 'cgi-bin/user/get_userid_by_email?access_token=%s';
    const USER_ID_SELECT_URL = 'cgi-bin/user/list_id?access_token=%s';
}