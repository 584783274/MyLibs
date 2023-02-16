<?php
/**
 * https://ai.qq.com/ 腾讯 API
 * https://ai.qq.com/doc/faceverify.shtml 文档
 */

namespace Kang\Libs\Tencent;

use Kang\Libs\Base\Component;

class Tencent extends Component
{
    /**
     * @var 接口一级地址
     * */
    const API_URL = 'https://api.ai.qq.com/';
    /**
     * @var 身份证OCR
     * */
    const ID_CARD_ORC_URL = 'fcgi-bin/ocr/ocr_idcardocr';
    /**
     * @var 车牌OCR
     * */
    const PLATEOCR_ORC_URL = 'fcgi-bin/ocr/ocr_plateocr';
    /**
     * @var 营业执照OCR
     * */
    const BIZLOCENSEOCR_ORC_URL = 'fcgi-bin/ocr/ocr_bizlicenseocr';
    /**
     * @var 银行卡OCR
     * */
    const CREDITCARDOCR_ORC_URL = 'fcgi-bin/ocr/ocr_creditcardocr';
    /**
     * @var 个体库创建
     * */
    const FACE_NEW_PERSON_URL = 'fcgi-bin/face/face_newperson';
    /**
     * @var 个体库删除
     * */
    const FACE_DEL_PERSON_URL = 'fcgi-bin/face/face_delperson';
    /**
     * @var 个体人脸新增
     * */
    const FACE_ADD_PERSON_URL = 'fcgi-bin/face/face_addface';
    /**
     * @var 个体人脸新增
     * */
    const FACE_DELI_PERSON_URL = 'fcgi-bin/face/face_delface';
    /**
     * @var 个体人脸验证
     * */
    const FACE_FACEVEERIFY_PERSON_URL = 'fcgi-bin/face/face_faceverify';
    /**
     * @var 智能闲聊
     * */
    const SMART_CHAT_URL = 'fcgi-bin/nlp/nlp_textchat';
    /**
     * @var AI文本翻译
     * */
    const TRANSLATION_AI_URL = 'fcgi-bin/nlp/nlp_texttrans';
    /**
     * @var 翻译君文本翻译
     * */
    const TRANSLATION_DON_URL = 'fcgi-bin/nlp/nlp_texttranslate';
    /**
     * @var 语音合成AI Lab
     * */
    const VOICE_SYNTHESIZE_AI_URL = 'fcgi-bin/aai/aai_tts';
    /**
     * @var 语音合成优图
     * */
    const VOICE_SYNTHESIZE_MAP_URL = 'fcgi-bin/aai/aai_tta';

    public $appid = 2116691551;
    public $appkey = 'vNGQtPIxB0W8qy5h';

    /**
     *身份识别 OCR
     * @param string $idCardImg 图片路径
     * @param int $cardType 0-正面，1-反面
     * */
    public function ORCIdCard($idCardImg, $cardType = 0)
    {
        $this->params['card_type'] = $cardType;
        $this->params['image'] = base64_encode(file_get_contents($idCardImg));

        return $this->curl(self::ID_CARD_ORC_URL);
    }

    /**
     * 车牌识别
     * @param string $image 本地图片路径
     * @param string $imageUrl 图片路由
     * */
    public function ORCPlateocr($image, $imageUrl = null)
    {
        if ($image && is_file($image)) {
            $this->params['image'] = base64_encode(file_get_contents($image));
        }

        if ($imageUrl) {
            $this->params['image_url'] = $imageUrl;
        }

        if (!$this->params['image'] && !$this->params['image_url']) {
            return $this->setErrors('请传人图片');
        }

        return $this->curl(self::PLATEOCR_ORC_URL);
    }

    /**
     * 营业执照
     * @param string $image 本地图片路径
     * */
    public function ORCBizlicenseocr($imgPath)
    {
        $this->params['image'] = base64_encode(file_get_contents($imgPath));

        return $this->curl(self::BIZLOCENSEOCR_ORC_URL);
    }

    /**
     * 银行卡
     * @param string $image 本地图片路径
     * */
    public function ORCBankCard($imgPath)
    {
        $this->params['image'] = base64_encode(file_get_contents($imgPath));

        return $this->curl(self::BIZLOCENSEOCR_ORC_URL);
    }

    /**
     * 个体库创建
     * @param string $imgPath 本地图片路径
     * @param string $person_id 识别ID 一般建议存入 用户账户表的ID
     * @param string $person_name 名字
     * @param string $group_ids 分组ID
     * @param string $tag 标签
     * */
    public function personCreate($imgPath, $person_id, $person_name, $group_ids, $tag = '')
    {
        $this->params['image'] = base64_encode(file_get_contents($imgPath));
        $this->params['person_id'] = $person_id;
        $this->params['person_name'] = $person_name;
        $this->params['group_ids'] = $group_ids;
        $this->params['tag'] = $tag;

        return $this->curl(self::FACE_NEW_PERSON_URL);
    }

    /**
     * 个体库删除
     * @param string $image 本地图片路径
     * @param string $person_id 识别ID 一般建议存入 用户账户表的ID
     * @param string $person_name 名字
     * @param string $group_ids 分组ID
     * @param string $tag 标签
     * */
    public function personDel($person_id)
    {
        $this->params['person_id'] = $person_id;

        return $this->curl(self::FACE_DEL_PERSON_URL);
    }

    /**
     * 个体人家增加
     * @param string $image 本地图片路径
     * @param string $person_id 识别ID 一般建议存入 用户账户表的ID
     * @param string $tag 标签
     * */
    public function personFaceAdd($imgPath, $person_id, $tag = '')
    {
        $this->params['image'] = base64_encode(file_get_contents($imgPath));
        $this->params['person_id'] = $person_id;
        $this->params['tag'] = $tag;

        return $this->curl(self::FACE_ADD_PERSON_URL);
    }

    /**
     * 个体人家删除
     * @param string $image 本地图片路径
     * @param string $person_id 识别ID 一般建议存入 用户账户表的ID
     * @param string $tag 标签
     * */
    public function personFaceDel($person_id, $face_ids)
    {
        $this->params['person_id'] = $person_id;
        $this->params['face_ids'] = $face_ids;

        return $this->curl(self::FACE_DELI_PERSON_URL);
    }

    /**
     * 个体人脸验证
     * @param string $image 本地图片路径
     * @param string $person_id 识别ID 一般建议存入 用户账户表的ID
     * @param string $image 要验证的人脸图片
     * */
    public function personFaceValidate($person_id, $imgPath)
    {
        $this->params['image'] = base64_encode(file_get_contents($imgPath));
        $this->params['person_id'] = $person_id;

        return $this->curl(self::FACE_FACEVEERIFY_PERSON_URL);
    }

    protected function curl($url)
    {
        $this->setCommonParams();
        $url = self::API_URL . $url;
        return $this->doHttpPost($url, $this->params);
    }

    /**
     * 智能闲聊
     * @param string $session 会话标识（应用内唯一） UTF-8编码，非空且长度上限32字节
     * @param string $question 用户输入的聊天内容 UTF-8编码，非空且长度上限300字节
     * */
    public function smartChat($session, $question)
    {
        $this->params['session'] = $session;
        $this->params['question'] = $question;

        return $this->curl(self::SMART_CHAT_URL);
    }

    /**
     * 文本翻译
     * @param integer $type 翻译类型 正整数  0    自动识别（中英文互转）1    中文翻译成英文 2    英文翻译成中文 3    中文翻译成西班牙文 4    西班牙文翻译成中文 5    中文翻译成法文 6    法文翻译成中文 7    英文翻译成越南语 8    越南语翻译成英文 9    中文翻译成粤语 10    粤语翻译成中文 11    中文翻译成韩文 13    英文翻译成德语 14    德语翻译成英文 15    中文翻译成日文 16    日文翻译成中文
     * @param string $question 待翻译文本 UTF-8编码，非空且长度上限1024字节
     * */
    public function translationAi($text, $type = 0)
    {
        $this->params['type'] = $type;
        $this->params['text'] = $text;

        return $this->curl(self::TRANSLATION_AI_URL);
    }

    /**
     * 语音合成
     * @param integer $text 待合成文本 UTF-8编码，非空且长度上限150字节
     * @param int $speaker 发音人编码 普通话男声 1 静琪女声    5 欢馨女声    6 碧萱女声    7
     * @param int $format 合成语音格式编码  PCM    1 WAV    2 MP3    3
     * @param int $volume 合成语音音量，取值范围[-10, 10]
     * @param int $speed 合成语音语速，默认100，取值范围[50, 200]
     * @param int $aht 即改变音高，取值范围[[-24, 24]
     * @param int $apc 改变说话人的音色，取值范围[[0, 100]
     * */
    public function voiceSynthesis($text, $speaker = 1, $format = 2, $volume = 0, $speed = 100, $aht = 0, $apc = 58)
    {
        $this->params['text'] = $text;
        $this->params['speaker'] = $speaker;
        $this->params['format'] = $format;
        $this->params['volume'] = $volume;
        $this->params['speed'] = $speed;
        $this->params['aht'] = $aht;
        $this->params['apc'] = $apc;

        return $this->curl(self::VOICE_SYNTHESIZE_AI_URL);
    }

    /**
     * 语音合成优图
     * @param integer $text 待合成文本 UTF-8编码，非空且长度上限300字节
     * @param int $speed 合成语音语速，默认100，取值范围[-2, 2] 0.6倍速    -2 0.8倍速    -1 正常速度    0 1.2倍速    1 1.5倍速    2
     * @param int $model_type 发音模型 取值范围[0, 2] 女生    0 女生纯英文    1 男生    2
     * */
    public function voiceSynthesisMap($text, $speed = 0, $model_type = 0)
    {
        $this->params['model_type'] = $model_type;
        $this->params['text'] = $text;
        $this->params['speed'] = $speed;

        return $this->curl(self::VOICE_SYNTHESIZE_MAP_URL);
    }

    /**
     * @param $TemplatelId 模板ID
     * @param $mobile 要接收的手机号码
     * @param array $params 格式样式  是 你申请的短信正文 里面 {} 对应的自填信息 : 例如：填写的是您的短信验证码：{1}  那 $params = [你的验证码]
     * @param string $sign //一般是模板模板名称
     * @param string $nationcode 手机号码 国家区号
     * @var 指定短息模板发送消息
     */
    public function snedSMSToTemplate($TemplatelId, $mobile, $params = [], $sign = '', $nationcode = '86')
    {
        $random = rand(100000, 999999);
        $data['ext'] = '';
        $data['extend'] = '';
        $data['params'] = $params;
        $data['sign'] = $sign;
        $data['tel'] = [
            'mobile' => $mobile,
            'nationcode' => $nationcode,
        ];
        $data['time'] = time();
        $data['tpl_id'] = $TemplatelId;

        $data['sig'] = $this->getSMSSig($random, $data['time'], $mobile);
        $url = 'https://yun.tim.qq.com/v5/tlssmssvr/sendsms?sdkappid=' . $this->appid . '&random=' . $random;
        $data = json_encode($data, true);

        $curl = new Curl($url, $data, Curl::METHOD_POST);
        if (!$result = $curl->run()) {
            return $this->setErrors($curl->getError());
        }

        return json_decode($result, JSON_UNESCAPED_UNICODE);
    }

    public static function doHttpPost($url, $params)
    {
        $curl = curl_init();
        $response = false;
        do {
            // 1. 设置HTTP URL (API地址)
            curl_setopt($curl, CURLOPT_URL, $url);

            // 2. 设置HTTP HEADER (表单POST)
            $head = array(
                'Content-Type: application/x-www-form-urlencoded'
            );
            curl_setopt($curl, CURLOPT_HTTPHEADER, $head);

            // 3. 设置HTTP BODY (URL键值对)
            $body = http_build_query($params);
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $body);

            // 4. 调用API，获取响应结果
            curl_setopt($curl, CURLOPT_HEADER, false);
            curl_setopt($curl, CURLOPT_NOBODY, false);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            $response = curl_exec($curl);
            self::$_http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            if (self::$_http_code != 200) {
                $msg = curl_error($curl);
                $response = json_encode(array('ret' => -1, 'msg' => "sdk http post err: {$msg}", 'http_code' => self::$_http_code));
                break;
            }
        } while (0);

        curl_close($curl);
        return $response;
    }

    protected function getSMSSig($random, $time, $mobile)
    {
        $hash = "appkey={$this->appkey}&random={$random}&time={$time}&mobile={$mobile}";
        return hash('sha256', $hash);
    }

    protected function setCommonParams()
    {
        $this->params['time_stamp'] = time();
        $this->params['nonce_str'] = Helper::getRandString();
        $this->params['app_id'] = $this->appid;
        $this->params['sign'] = $this->getSign($this->params);
    }

    protected function getSign($params = [])
    {
        ksort($params);
        $str = '';
        foreach ($params as $key => $value) {
            if ($value !== '') {
                $str .= $key . '=' . urlencode($value) . '&';
            }
        }

        $str .= 'app_key=' . $this->appkey;
        $sign = strtoupper(md5($str));

        return $sign;
    }

    protected static $_http_code;
    protected $params = [];
    protected $result = null;
}