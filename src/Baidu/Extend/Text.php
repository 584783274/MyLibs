<?php

namespace Kang\Libs\Baidu\Extend;

use Kang\Libs\Baidu\Baidu;
use Kang\Libs\Base\Component;

class Text extends Component {
    public function __construct($config = []){
        parent::__construct($config);
    }

    /**
     * @var Baidu
     */
    public $sender;

    /**
     * @var 高精度图片文字识别
     * @param string $imagePath
     * @param string $pdfFilePath
     * @param string $imageUrl
     * @return bool|array
     */
    public function textOcrByPrecision($imagePath = '', $pdfFilePath = '', $imageUrl = ''){
        if($imagePath && file_exists($imagePath)){
            $data['image'] = base64_encode(file_get_contents($imagePath));
        }elseif ($pdfFilePath && file_exists($pdfFilePath)){
            $data['pdf_file'] = base64_encode(file_get_contents($pdfFilePath));
        }elseif ($imageUrl){
            $data['url'] = $imageUrl;
        }

        return $this->sender->post('rest/2.0/ocr/v1/accurate_basic?access_token=', $data, true);
    }
    /**
     * @var 身份证识别
     * @param string $imagePath 图片地址要求base64编码和urlencode后大小不超过4M，最短边至少15px，最长边最大4096px,支持jpg/jpeg/png/bmp格式
     * @param string $imageUrl 图片完整URL，URL长度不超过1024字节，URL对应的图片base64编码后大小不超过4M，最短边至少15px，最长边最大4096px,支持jpg/jpeg/png/bmp格式，当image字段存在时url字段失效</br>请注意关闭URL防盗链
     * @param string $id_card_side -front：身份证含照片的一面</br>-back：身份证带国徽的一面</br>自动检测身份证正反面，如果传参指定方向与图片相反，支持正常识别，返回参数image_status字段为"reversed_side"
     * @return bool|array
     */
    public function idcard($imagePath = '',  $imageUrl = '', $id_card_side = 'front'){
        if($imagePath && file_exists($imagePath)){
            $data['image'] = base64_encode(file_get_contents($imagePath));
        }elseif ($imageUrl){
            $data['url'] = $imageUrl;
        }

        $data['id_card_side'] = $id_card_side;
        return $this->sender->post('/rest/2.0/ocr/v1/idcard?access_token=', $data, true);
    }
    /**
     * @var 银行卡识别
     * @param string $imagePath 图片地址要求base64编码和urlencode后大小不超过4M，最短边至少15px，最长边最大4096px,支持jpg/jpeg/png/bmp格式
     * @param string $imageUrl 图片完整URL，URL长度不超过1024字节，URL对应的图片base64编码后大小不超过4M，最短边至少15px，最长边最大4096px,支持jpg/jpeg/png/bmp格式，当image字段存在时url字段失效</br>请注意关闭URL防盗链
     * @return bool|array
     */
    public function bankcard($imagePath = '',  $imageUrl = ''){
        if($imagePath && file_exists($imagePath)){
            $data['image'] = base64_encode(file_get_contents($imagePath));
        }elseif ($imageUrl){
            $data['url'] = $imageUrl;
        }

        return $this->sender->post('/rest/2.0/ocr/v1/bankcard?access_token=', $data, true);
    }
    /**
     * @var 支持对不同版式营业执照的证件编号、社会信用代码、单位名称、地址、法人、类型、成立日期、有效日期、经营范围等关键字段进行结构化识别。
     * @param string $imagePath 图片地址要求base64编码和urlencode后大小不超过4M，最短边至少15px，最长边最大4096px,支持jpg/jpeg/png/bmp格式
     * @param string $imageUrl 图片完整URL，URL长度不超过1024字节，URL对应的图片base64编码后大小不超过4M，最短边至少15px，最长边最大4096px,支持jpg/jpeg/png/bmp格式，当image字段存在时url字段失效</br>请注意关闭URL防盗链
     * @return bool|array
     */
    public function businessLicense($imagePath = '',  $imageUrl = ''){
        if($imagePath && file_exists($imagePath)){
            $data['image'] = base64_encode(file_get_contents($imagePath));
        }elseif ($imageUrl){
            $data['url'] = $imageUrl;
        }

        return $this->sender->post('/rest/2.0/ocr/v1/business_license?access_token=', $data, true);
    }
    /**
     * @var 查询具体返回企业全维度信息，包括工商基本信息、分支机构信息、企业变更信息、纳税信息、联系信息、企业高管信息、经营异常信息、动产抵押信息、曾用名信息、股东信息、行政处罚信息、行政许可信息、股权出资信息、失信信息、被执行信息等。
     * @param string $verifynum 企业名称、注册号、社会统一信用代码中的任意一种
     * @param boolean $isPrecision 是否使用高精度查询
     * @return bool|mixed
     */
    public function businesslicenseVerificationDetailed($verifynum, $isPrecision = false){
        $data['verifynum'] = $verifynum;
        $url = $isPrecision ? '/rest/2.0/ocr/v1/businesslicense_verification_detailed?access_token='
            : '/rest/2.0/ocr/v1/businesslicense_verification_standard?access_token=';

        return $this->sender->post($url, $data, true);
    }
    /**
     * @var 通过核验企业名称、统一社会信用代码、法人姓名一致性，快速核验企业资质。
     * @param string $name 法人姓名
     * @param string $company 企业名称
     * @param string $regnum 社会统一信用代码
     * @return bool|mixed
     */
    public function businessVerification($name, $company, $regnum){
        $data['name'] = $name;
        $data['company'] = $company;
        $data['regnum'] = $regnum;

        return $this->sender->post('/rest/2.0/ocr/v1/three_factors_verification?access_token=', $data, true);
    }

    /**
     * @var 驾驶证-支持对机动车驾驶证正页及副页所有15个字段进行结构化识别，包括证号、姓名、性别、国籍、住址、出生日期、初次领证日期、准驾车型、有效期限、发证单位、档案编号等。
     * @param string $imagePath 图像数据，base64编码后进行urlencode，要求base64编码和urlencode后大小不超过10M，最短边至少15px，最长边最大8192px，支持jpg/jpeg/png/bmp格式 优先级：image > url > pdf_file，当image字段存在时，url、pdf_file字段失效
     * @param string $imageUrl 图片完整url，url长度不超过1024字节，url对应的图片base64编码后大小不超过10M，最短边至少15px，最长边最大8192px，支持jpg/jpeg/png/bmp格式 优先级：image > url > pdf_file，当image字段存在时，url字段失效 请注意关闭URL防盗链
     * @return bool|mixed
     */
    public function drivingLicense($imagePath = '',  $imageUrl = ''){
        if($imagePath && file_exists($imagePath)){
            $data['image'] = base64_encode(file_get_contents($imagePath));
        }elseif ($imageUrl){
            $data['url'] = $imageUrl;
        }

        return $this->sender->post('/rest/2.0/ocr/v1/driving_license?access_token=', $data, true);
    }

    /**
     * @var 支持识别中国大陆机动车蓝牌、黄牌（单双行）、绿牌、大型新能源（黄绿）、领使馆车牌、警牌、武警牌（单双行）、军牌（单双行）、港澳出入境车牌、农用车牌、民航车牌的地域编号和车牌号，并能同时识别图像中的多张车牌。
     * @param string $imagePath 图像数据，base64编码后进行urlencode，要求base64编码和urlencode后大小不超过4M，最短边至少15px，最长边最大4096px,支持jpg/jpeg/png/bmp格式
     * @param string $imageUrl 图片完整URL，URL长度不超过1024字节，URL对应的图片base64编码后大小不超过4M，最短边至少15px，最长边最大4096px,支持jpg/jpeg/png/bmp格式，当image字段存在时url字段失效</br>请注意关闭URL防盗链
     * @param bool $multi_detect
     * @param bool $multi_scale
     * @return bool|mixed
     */
    public function licensePlate($imagePath = '',  $imageUrl = '', $multi_detect = false, $multi_scale = false){
        if($imagePath && file_exists($imagePath)){
            $data['image'] = base64_encode(file_get_contents($imagePath));
        }elseif ($imageUrl){
            $data['url'] = $imageUrl;
        }

        $data['multi_scale'] = $multi_scale;
        $data['multi_detect'] = $multi_detect;
        return $this->sender->post('/rest/2.0/ocr/v1/license_plate?access_token=', $data, true);
    }
    /**
     * @var 通用发票识别-针对票据字体做了专项优化的通用文字识别版本，支持对医疗票据、银行兑票、购物小票等各类票据的票面内容进行识别，并按行返回结果。
     * @param string $imagePath
     * @param string $imageUrl
     * @param string $recognize_granularity 是否定位单字符位置，big：不定位单字符位置，默认值；small：定位单字符位置
     * @param bool $probability 是否返回识别结果中每一行的置信度
     * @param string $accuracy normal：使用快速服务；缺省或其它值：使用高精度服务
     * @param bool $detect_direction 是否检测图像朝向，默认不检测，即：false。可选值包括：</br>- true：检测朝向；</br>- false：不检测朝向，朝向是指输入图像是正常方向、逆时针旋转90/180/270度
     */
    public function receipt($imagePath = '',  $imageUrl = '', $recognize_granularity = 'small', $probability = false, $accuracy = '', $detect_direction = false){
        if($imagePath && file_exists($imagePath)){
            $data['image'] = base64_encode(file_get_contents($imagePath));
        }elseif ($imageUrl){
            $data['url'] = $imageUrl;
        }

        $data['recognize_granularity'] = $recognize_granularity;
        $data['probability'] = $probability;
        $data['accuracy'] = $accuracy;
        $data['detect_direction'] = $detect_direction;

        return $this->sender->post('/rest/2.0/ocr/v1/receipt?access_token=', $data, true);
    }
    /**
     * @var 增值税发票识别-支持对增值税普票、专票、全电发票（新版全国统一电子发票，专票/普票）、卷票、区块链发票的所有字段进行结构化识别，包括发票基本信息、销售方及购买方信息、商品信息、价税信息等，其中五要素字段的识别准确率超过 99.9%； 同时，支持对增值税卷票的 21 个关键字段进行识别，包括发票类型、发票代码、发票号码、机打号码、机器编号、收款人、销售方名称、销售方纳税人识别号、开票日期、购买方名称、购买方纳税人识别号、项目、单价、数量、金额、税额、合计金额(小写)、合计金额(大写)、校验码、省、市，四要素字段的识别准确率可达95%
     * @param string $imagePath
     * @param string $pdfFilePath
     * @param string $imageUrl
     * @param integer $pdf_file_num 需要识别的PDF文件的对应页码，当 pdf_file 参数有效时，识别传入页码的对应页面内容，若不传入，则默认识别第 1 页
     * @param string $type 进行识别的增值税发票类型，默认为 normal，可缺省</br>- **normal：**可识别增值税普票、专票、电子发票</br>- **roll：**可识别增值税卷票
     * @param bool $seal_tag 是否开启印章判断功能，并返回印章内容的识别结果</br>- **true：**开启</br>- **false：**不开启
     * @return bool|mixed
     */
    public function vatInvoice($imagePath = '', $pdfFilePath = '', $imageUrl = '', $pdf_file_num = 1, $type = 'normal', $seal_tag = false){
        if($imagePath && file_exists($imagePath)){
            $data['image'] = base64_encode(file_get_contents($imagePath));
        }elseif ($pdfFilePath && file_exists($pdfFilePath)){
            $data['pdf_file'] = base64_encode(file_get_contents($pdfFilePath));
        }elseif ($imageUrl){
            $data['url'] = $imageUrl;
        }

        $data['pdf_file_num'] = $pdf_file_num;
        $data['type'] = $type;
        $data['seal_tag'] = $seal_tag;

        return $this->sender->post('/rest/2.0/ocr/v1/vat_invoice?access_token=', $data, true);
    }
    /**
     * @var 火车票识别-支持对红、蓝火车票的13个关键字段进行结构化识别，包括车票号码、始发站、目的站、车次、日期、票价、席别、姓名、座位号、身份证号、售站、序列号、时间。
     * @param string $imagePath
     * @param string $pdfFilePath
     * @param string $imageUrl
     * @param int $pdf_file_num
     * @return bool|mixed
     */
    public function trainTicket($imagePath = '', $pdfFilePath = '', $imageUrl = '', $pdf_file_num = 1){
        if($imagePath && file_exists($imagePath)){
            $data['image'] = base64_encode(file_get_contents($imagePath));
        }elseif ($pdfFilePath && file_exists($pdfFilePath)){
            $data['pdf_file'] = base64_encode(file_get_contents($pdfFilePath));
        }elseif ($imageUrl){
            $data['url'] = $imageUrl;
        }

        return $this->sender->post('/rest/2.0/ocr/v1/train_ticket?access_token=', $data, true);
    }
}