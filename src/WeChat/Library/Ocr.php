<?php

namespace Kang\Libs\WeChat\Library;
/**
 * 识别接口
 * Trait Ocr
 * @package Kang\Libs\WeChat\Library
 */
trait Ocr{
    /**
     * @var 长链接转短连接
     * @param $longUrl
     * @return bool|void
     */
    public function getShortUrl($longUrl){
        $data['action']   = 'long2short';
        $data['long_url'] = $longUrl;
        if(!$result = $this->httpPost(Url::API_SHOR_URL, $data, true)){
            return false;
        }

        return $result['short_url'];
    }
    /**
     * @var 身份证识别
     * @param $imgUrl string 图片路由地址
     */
    public function ocrIdCard($imgUrl){
        $url  = Urls::OCR_ID_CARD;
        $url .=  'img_url=' . $imgUrl;
        $url .=  '&access_token=';
        if($result = $this->httpPost($url, null, true)){
            return $result;
        }

        return false;
    }
    /**
     * @var 银行卡OCR识别
     * @param $imgUrl string 图片路由地址
     * @param $type   照片正反 photo | MODE
     */
    public function ocrBankCard($imgUrl){
        $url = Urls::OCR_BANK_CARD_URL ;
        $url .=  '?img_url=' . $imgUrl;
        $url .=  '&access_token=';

        return $this->httpPost($url, null, true);
    }
    /**
     * @var 行驶证OCR识别接口
     * @param $imgUrl string 图片路由地址
     * @param $type   照片正反 photo | MODE
     */
    public function ocrDriving($imgUrl){
        $url  = Urls::OCR_DRIVING_URL;
        $url .=  '?img_url=' . $imgUrl;
        $url .=  '&access_token=';

        return $this->httpPost($url, null, true);
    }

    /**
     * @var 驾驶证OCR识别接口
     * @param $imgUrl string 图片路由地址
     * @param $type   照片正反 photo | MODE
     */
    public function ocrDrivingLicense($imgUrl){
        $url  = Urls::OCR_DRIVING_LICENSE_URL;
        $url .=  '?img_url=' . $imgUrl;
        $url .=  '&access_token=';

        return $this->httpPost($url, null, true);
    }

    /**
     * @var 营业执照 OCR识别接口
     * @param $imgUrl string 图片路由地址
     * @param $type   照片正反 photo | MODE
     */
    public function ocrBizlicense($imgUrl){
        $url  = Urls::OCR_BIZLICENSE_URL;
        $url .=  '?img_url=' . $imgUrl;
        $url .=  '&access_token=';

        return $this->httpPost($url, null, true);
    }
    /**
     * @var 营业执照 OCR识别接口
     * @param $imgUrl string 图片路由地址
     * @param $type   照片正反 photo | MODE
     */
    public function ocrPlatenum($imgUrl){
        $url  = Urls::OCR_PLATENUM_URL;
        $url .=  '?img_url=' . $imgUrl;
        $url .=  '&access_token=';

        return $this->httpPost($url, null, true);
    }

    /**
     * @var 营业执照 OCR识别接口
     * @param $imgUrl string 图片路由地址
     * @param $type   照片正反 photo | MODE
     */
    public function ocrMenu($imgUrl){
        $url  = Urls::OCR_MENU_URL;
        $url .=  '?img_url=' . $imgUrl;
        $url .=  '&access_token=';

        return $this->httpPost($url, null, true);
    }
}
