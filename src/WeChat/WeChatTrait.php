<?php

namespace Kang\Libs\WeChat;

use Kang\Libs\Base\Event;
use Kang\Libs\Helper\Curl;

trait WeChatTrait{
    /**
     * @var GET����
     * @param string $url �����ַ
     * @param null $data ��������
     * @param bool $autoToken �Ƿ��Զ���ȡ
     * @param bool $useCert �Ƿ���Ҫ֤��
     * @return bool|void
     */
    public function httpGet($url, $data = NULL, $autoToken = false, $useCert = false){
        if($data){
            $url .= '&' . http_build_query($data);
        }

        return $this->httpRequest($url, Curl::METHOD_GET, null, $autoToken, false, $useCert);
    }
    /**
     * @var curl POST ����
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
     * @var curl����
     * @param string $url �����ַ
     * @param string $method ���󷽷�
     * @param null   $data ��������
     * @param bool   $autoToken  �Ƿ��Զ���ȡ AccessToken
     * @param bool   $useCert �Ƿ����Ӱ�ȫ֤��
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
            $errMsg = $result['errmsg'] ?? 'δ֪����';
            if(isset(self::$_errorsMessage[$errCode])){
                $errMsg = self::$_errorsMessage[$errCode];
            }

            return $this->setErrors($errCode, $errMsg);
        }

        return $result;
    }

    protected static $_errorsMessage = [
        1 => 'ϵͳ��æ�����Ժ�����!',
        40001 => 'AppSecret ���󣬻���access_token��Ч',
        40002 => '���Ϸ���ƾ֤����',
        40003 => '���û��Ƿ��ѹ�ע���ں�',
        40004 => '���Ϸ���ý���ļ�����',
        40005 => '���Ϸ����ļ�����',
        40006 => '���Ϸ����ļ���С',
        40007 => '���Ϸ���ý���ļ� id',
        40008 => '���Ϸ�����Ϣ����',
        40009 => '���Ϸ���ͼƬ�ļ���С',
        40010 => '���Ϸ��������ļ���С',
        40011 => '���Ϸ�����Ƶ�ļ���С',
        40012 => '���Ϸ�������ͼ�ļ���С',
        40013 => '���Ϸ��� AppID',
        40014 => '���Ϸ��� access_token',
        40015 => '���Ϸ��Ĳ˵�����',
        40016 => '���Ϸ��İ�ť����',
        40017 => '���Ϸ��İ�ť����',
        40018 => '���Ϸ��İ�ť���ֳ���',
        40019 => '���Ϸ��İ�ť KEY ����',
        40020 => '���Ϸ��İ�ť URL ����',
        40021 => '���Ϸ��Ĳ˵��汾��',
        40022 => '���Ϸ����Ӳ˵�����',
        40023 => '���Ϸ����Ӳ˵���ť����',
        40024 => '���Ϸ����Ӳ˵���ť����',
        40025 => '���Ϸ����Ӳ˵���ť���ֳ���',
        40026 => '���Ϸ����Ӳ˵���ť KEY ����',
        40027 => '���Ϸ����Ӳ˵���ť URL ����',
        40028 => '���Ϸ����Զ���˵�ʹ���û�',
        40029 => '��Ч�� oauth_code',
        40030 => '���Ϸ��� refresh_token',
        40031 => '���Ϸ��� openid �б�',
        40032 => '���Ϸ��� openid �б���',
        40033 => '���Ϸ��������ַ������ܰ��� \uxxxx ��ʽ���ַ�',
        40035 => '���Ϸ��Ĳ���',
        40038 => '���Ϸ��������ʽ',
        40039 => '���Ϸ��� URL ����',
        40048 => '��Ч��url',
        40050 => '���Ϸ��ķ��� id',
        40051 => '�������ֲ��Ϸ�',
        40060 => 'ָ���� article_idx ���Ϸ�',
        40066 => '�����б�Ϊ�գ��������ٴ���һ������ID��������ͨѶ¼��',
        40071 => '��ǩ�����Ѿ����ڻ��߲��Ϸ�',
        40113 => '��֧�ֵ��ļ�����',
        40117 => '�������ֲ��Ϸ�',
        40118 => 'media_id ��С���Ϸ�',
        40119 => 'button ���ʹ���',
        40120 => 'button ���ʹ���',
        40121 => '���Ϸ��� media_id ����',
        40125 => '��Ч��appsecret',
        40132 => '΢�źŲ��Ϸ�',
        40137 => '��֧�ֵ�ͼƬ��ʽ',
        40155 => '��������������ںŵ���ҳ����',
        40163 => 'oauth_code��ʹ��',
        40164 => 'IP������δ����, �뽫��ǰ��������IP��ӽ���������',
        41001 => 'ȱ�� access_token ����',
        41002 => 'ȱ�� appid ����',
        41003 => 'ȱ�� refresh_token ����',
        41004 => 'ȱ�� appsecret ����',
        41005 => 'ȱ�ٶ�ý���ļ�����',
        41006 => 'ȱ�� media_id ����',
        41007 => 'ȱ���Ӳ˵�����',
        41008 => 'ȱ�� oauth code',
        41009 => 'ȱ�� openid',
        42001 => 'access_token ��ʱ������ access_token ����Ч��',
        42002 => 'refresh_token ��ʱ',
        42003 => 'oauth_code ��ʱ',
        42007 => '�û��޸�΢�����룬��Ҫ������Ȩ',
        43001 => '��Ҫ GET ����',
        43002 => '��Ҫ POST ����',
        43003 => '��Ҫ HTTPS ����',
        43004 => '��Ҫ�����߹�ע',
        43005 => '��Ҫ���ѹ�ϵ',
        43019 => '��Ҫ�������ߴӺ��������Ƴ�',
        44001 => '��ý���ļ�Ϊ��',
        44002 => 'POST �����ݰ�Ϊ��',
        44003 => 'ͼ����Ϣ����Ϊ��',
        44004 => '�ı���Ϣ����Ϊ��',
        45001 => '��ý���ļ���С��������',
        45002 => '��Ϣ���ݳ�������',
        45003 => '�����ֶγ�������',
        45003 => '�����ֶγ�������',
        45004 => '�����ֶγ�������',
        45005 => '�����ֶγ�������',
        45006 => 'ͼƬ�����ֶγ�������',
        45007 => '��������ʱ�䳬������',
        45008 => 'ͼ����Ϣ��������',
        45009 => '΢�Žӿڣ�����ʹ�ô����Ѵ�����!',
        45010 => '�����˵�������������',
        45011 => 'API ����̫Ƶ�������Ժ�����',
        45015 => '�û���ʱ��δ����!',
        45016 => 'ϵͳ���飬�������޸�',
        45017 => '�������ֹ���',
        45018 => '����������������',
        45047 => '�ͷ��ӿ�����������������',
        45056 => '�����ı�ǩ�����࣬��ע�ⲻ�ܳ���100��',
        45058 => '΢��Ĭ�ϱ�ǩ����ֹ����',
        45157 => '��ǩ���Ƿ�����ע�ⲻ�ܺ�������ǩ����',
        45158 => '��ǩ�����ȳ���30���ֽ�',
        46001 => '������ý������',
        46002 => '�����ڵĲ˵��汾',
        46003 => '�����ڵĲ˵�����',
        46004 => '�����ڵ��û�',
        47001 => '���� JSON/XML ���ݴ���',
        48001 => 'api ����δ��Ȩ,��ȷ�Ϲ��ں��ѻ�øýӿ�!',
        48002 => '��˿�ر��˽�����Ϣ',
        48004 => 'api �ӿڱ����',
        48005 => 'api ��ֹɾ�����Զ��ظ����Զ���˵����õ��ز�',
        48006 => 'api ��ֹ������ô�������Ϊ��������ﵽ����',
        48008 => 'û�и�������Ϣ�ķ���Ȩ��',
        50001 => '�û�δ��Ȩ�� api',
        50002 => '�û����ޣ�������Υ���ӿڱ����',
        50005 => '�û�δ��ע���ں�',
        60001 => '�������Ʋ���Ϊ���ҳ��Ȳ��ܳ���32����',
        60003 => '����ID������',
        61451 => '�������� ',
        61452 => '��Ч�ͷ��˺� ',
        61453 => '�ͷ��ʺ��Ѵ��� ',
        61454 => '�ͷ��ʺ������ȳ������� ( ������ 10 ��Ӣ���ַ��������� @ �� @ ��Ĺ��ںŵ�΢�ź� )',
        61455 => '�ͷ��ʺ��������Ƿ��ַ� ( ������Ӣ�� + ���� )',
        61456 => '�ͷ��ʺŸ����������� (10 ���ͷ��˺� )',
        61457 => '��Чͷ���ļ����� ',
        61450 => 'ϵͳ���� ',
        61500 => '���ڸ�ʽ����',
        63001 => '���ֲ���Ϊ��',
        63002 => '��Ч��ǩ��',
        65301 => '�����ڴ� menuid ��Ӧ�ĸ��Ի��˵�',
        65302 => 'û����Ӧ���û�',
        65303 => 'û��Ĭ�ϲ˵������ܴ������Ի��˵�',
        65304 => 'MatchRule ��ϢΪ��',
        65305 => '���Ի��˵���������',
        65306 => '��֧�ָ��Ի��˵����ʺ�',
        65307 => '���Ի��˵���ϢΪ��',
        65308 => '����û����Ӧ���͵� button',
        65309 => '���Ի��˵����ش��ڹر�״̬',
        65310 => '��д��ʡ�ݻ������Ϣ��������Ϣ����Ϊ��',
        65311 => '��д�˳�����Ϣ��ʡ����Ϣ����Ϊ��',
        65312 => '���Ϸ��Ĺ�����Ϣ',
        65313 => '���Ϸ���ʡ����Ϣ',
        65314 => '���Ϸ��ĳ�����Ϣ',
        65316 => '�ù��ںŵĲ˵������˹�������������������ת�� 3 �����������ӣ�',
        65317 => '���Ϸ��� URL',
        87009 => '��Ч��ǩ��',
        88000 => '��������Ȩ',
        9001001 => 'POST ���ݲ������Ϸ�',
        9001002 => '΢�ŷ��񲻿���',
        9001003 => 'Ticket ���Ϸ�',
        9001004 => '��ȡҡ�ܱ��û���Ϣʧ��',
        9001005 => '��ȡ�̻���Ϣʧ��',
        9001006 => '��ȡ OpenID ʧ��',
        9001007 => '�ϴ��ļ�ȱʧ',
        9001008 => '�ϴ��زĵ��ļ����Ͳ��Ϸ�',
        9001009 => '�ϴ��زĵ��ļ��ߴ粻�Ϸ�',
        9001010 => '�ϴ�ʧ��',
        9001020 => '�ʺŲ��Ϸ�',
        9001021 => '�����豸�����ʵ��� 50% �����������豸',
        9001022 => '�豸���������Ϸ�������Ϊ���� 0 ������',
        9001023 => '�Ѵ�������е��豸 ID ����',
        9001024 => 'һ�β�ѯ�豸 ID �������ܳ��� 50',
        9001025 => '�豸 ID ���Ϸ�',
        9001026 => 'ҳ�� ID ���Ϸ�',
        9001027 => 'ҳ��������Ϸ�',
        9001028 => 'һ��ɾ��ҳ�� ID �������ܳ��� 10',
        9001029 => 'ҳ����Ӧ�����豸�У����Ƚ��Ӧ�ù�ϵ��ɾ��',
        9001030 => 'һ�β�ѯҳ�� ID �������ܳ��� 50',
        9001031 => 'ʱ�����䲻�Ϸ�',
        9001032 => '�����豸��ҳ��İ󶨹�ϵ��������',
        9001033 => '�ŵ� ID ���Ϸ�',
        9001034 => '�豸��ע��Ϣ����',
        9001035 => '�豸����������Ϸ�',
        9001036 => '��ѯ��ʼֵ begin ���Ϸ�',
    ];
    protected $_retry = false; //access_token ʧЧ����������
}
