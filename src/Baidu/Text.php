<?php

namespace Kang\Libs\Baidu;

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

    
}