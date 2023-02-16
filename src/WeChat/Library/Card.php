<?php
namespace Kang\Libs\WeChat\Library;

trait Card{
    //------------------------------卡卷管理--------------------------------//
    /**
     * @param array $data
     * @return bool|string
     */
    public function cardByCreate(array $data){
        if(!$result = $this->httpPost(self::API_CARD_CREATE_URL, $data, true)){
            return false;
        }

        return $result['card_id'];
    }
    /**
     * @var 创建团购券
     * @param array $baseInfo [
    'logo_url' => '', //卡券的商户logo，建议像素为300*300。
    'code_type' => '', //码型： "CODE_TYPE_TEXT"文 本 ； "CODE_TYPE_BARCODE"一维码 "CODE_TYPE_QRCODE"二维码 "CODE_TYPE_ONLY_QRCODE",二维码无 code 显示； "CODE_TYPE_ONLY_BARCODE",一维码无 code 显示；CODE_TYPE_NONE， 不显示 code 和条形码类型
    'brand_name' => '', //商户名字,字数上限为12个汉字。
    'title' => '', //卡券名，字数上限为9个汉字。
    'title' => '', //卡券名，字数上限为9个汉字。(建议涵盖卡券属性、服务及金额)。
    'color' => '', //券颜色。按色彩规范标注填写Color010-Color100。
    'notice' => '', //卡券使用提醒，字数上限为16个汉字。
    'description' => '', //卡券使用说明，字数上限为1024个汉字。
    'sku' => [
    'quantity' => 10000, //卡券库存的数量，上限为100000000。
    ], //商品信息。
    'date_info' => [
    "type": "DATE_TYPE_FIX_TIME_RANGE", //DATE_TYPE_FIX TIME_RANGE 表示固定日期区间，DATE_TYPE FIX_TERM 表示固定时长 （自领取后按天算。
    "begin_timestamp" => 1397577600, //type为DATE_TYPE_FIX_TIME_RANGE时专用，表示起用时间。从1970年1月1日00:00:00至起用时间的秒数，最终需转换为字符串形态传入。
    "end_timestamp" => 1472724261 //表示结束时间 ， 建议设置为截止日期的23:59:59过期 。 （ 东八区时间,UTC+8，单位为秒 ）
    "fixed_term" => 1472724261 //type为DATE_TYPE_FIX_TERM时专用，表示自领取后多少天内有效，不支持填写0。
    "fixed_begin_term" => 1472724261 //type为DATE_TYPE_FIX_TERM时专用，表示自领取后多少天开始生效，领取后当天生效填写0。（单位为天）
    "end_time s tamp" => 1472724261 //可用于DATE_TYPE_FIX_TERM时间类型，表示卡券统一过期时间 ， 建议设置为截止日期的23:59:59过期 。 （ 东八区时间,UTC+8，单位为秒 ），设置了fixed_term卡券，当时间达到end_timestamp时卡券统一过期
    ], //使用日期，有效期的信息。

    //以下非必须
    'use_custom_code' => '', //是否自定义 Code 码 。填写 true 或false，默认为false。 通常自有优惠码系统的开发者选择 自定义 Code 码，并在卡券投放时带入 Code码，详情见 是否自定义 Code 码 。
    'get_custom_code_mode' => '', //填入 GET_CUSTOM_CODE_MODE_DEPOSIT 表示该卡券为预存 code 模式卡券， 须导入超过库存数目的自定义 code 后方可投放， 填入该字段后，quantity字段须为0,须导入code 后再增加库存
    'bind_openid' => '', //是否指定用户领取，填写 true 或false 。默认为false。通常指定特殊用户群体 投放卡券或防止刷券时选择指定用户领取。
    'service_phone' => '', //客服电话。
    'location_id_list' => '', //门店位置poiid。 调用 POI门店管理接 口 获取门店位置poiid。具备线下门店 的商户为必填。。
    'use_all_locations' => '', //设置本卡券支持全部门店，与location_id_list互斥
    'center_title' => '', //卡券顶部居中的按钮，仅在卡券状 态正常(可以核销)时显示
    'center_sub_title' => '', //显示在入口下方的提示语 ，仅在卡券状态正常(可以核销)时显示。
    'center_url' => '', //顶部居中的url ，仅在卡券状态正常(可以核销)时显示。
    'center_app_brand_user_name' => '', //卡券跳转的小程序的user_name，仅可跳转该 公众号绑定的小程序 。
    'center_app_brand_pass' => '', //卡券跳转的小程序的path
    'custom_url_name' => '', //自定义跳转外链的入口名字。
    'custom_url' => '', //自定义跳转的URL。
    'custom_url_sub_title' => '', //显示在入口右侧的提示语。
    'custom_app_brand_user_name' => '', //卡券跳转的小程序的user_name，仅可跳转该 公众号绑定的小程序 。
    'custom_app_brand_pass' => '', //卡券跳转的小程序的path
    'promotion_url_name' => '', //营销场景的自定义入口名称。
    'promotion_url' => '', //入口跳转外链的地址链接。。
    'promotion_url_sub_title' => '', //显示在营销入口右侧的提示语。
    'promotion_app_brand_pass' => '', //卡券跳转的小程序的path。
    'get_limit' => '', //每人可领券的数量限制,不填写默认为50。
    'use_limit' => '', //每人可核销的数量限制,不填写默认为50
    'can_share' => '', //卡券领取页面是否可分享。
    'can_give_friend' => '', //卡券是否可转赠。
     * ]
     * @param string $dealDetail
     * @param array $advancedinfo [
    'use_condition' => [
    'accept_category' => '', //指定可用的商品类目，仅用于代金券类型 ，填入后将在券面拼写适用于xxx
    'reject_category' => '', //指定不可用的商品类目，仅用于代金券类型 ，填入后将在券面拼写不适用于xxxx
    'least_cost' => '', //满减门槛字段，可用于兑换券和代金券 ，填入后将在全面拼写消费满 xx 元可用。
    'object_use_for' => '', //购买 xx 可用类型门槛，仅用于兑换 ，填入后自动拼写购买 xxx 可用。
    'can_use_with_other_discount' => '', //不可以与其他类型共享门槛 ，填写 false 时系统将在使用须知里 拼写“不可与其他优惠共享”， 填写 true 时系统将在使用须知里 拼写“可与其他优惠共享”， 默认为true
    ], //使用门槛（条件）字段，若不填写使用条件则在券面拼写 ：无最低消费限制，全场通用，不限品类；并在使用说明显示： 可与其他优惠共享

    'abstract' => [
    'abstract' => '', //封面摘要简介。
    'icon_url_list' => [], //封面图片列表，仅支持填入一 个封面图片链接， 上传图片接口 上传获取图片获得链接，填写 非 CDN 链接会报错，并在此填入。 建议图片尺寸像素850*350
    ],//封面摘要结构体名称

    'text_image_list' => [
    [
    'image_url' => '', //图片链接，必须调用 上传图片接口 上传图片获得链接，并在此填入， 否则报错
    'text' => '', //图文描述
    ],

    ], //封面图片列表 建议图片尺寸像素850*350

    'business_service' => [
    'BIZ_SERVICE_DELIVER',
    'BIZ_SERVICE_FREE_PARK',
    ], //商家服务类型：BIZ_SERVICE_DELIVER 外卖服务； BIZ_SERVICE_FREE_PARK 停车位； BIZ_SERVICE_WITH_PET 可带宠物； BIZ_SERVICE_FREE_WIFI 免费wifi，可多选

    'time_limit' => [
    [
    'type' => '', //限制类型枚举值：支持填入 MONDAY 周一 TUESDAY 周二 WEDNESDAY 周三 THURSDAY 周四 FRIDAY 周五 SATURDAY 周六 SUNDAY 周日 此处只控制显示， 不控制实际使用逻辑，不填默认不显示
    'begin_hour' => '', //当前 type 类型下的起始时间（小时） ，如当前结构体内填写了MONDAY， 此处填写了10，则此处表示周一 10:00可用
    'begin_minute' => '', //当前 type 类型下的起始时间（分钟） ，如当前结构体内填写了MONDAY， begin_hour填写10，此处填写了59， 则此处表示周一 10:59可用
    'end_hour' => '', //当前 type 类型下的结束时间（小时） ，如当前结构体内填写了MONDAY， 此处填写了20， 则此处表示周一 10:00-20:00可用
    'end_minute' => '', //当前 type 类型下的结束时间（分钟） ，如当前结构体内填写了MONDAY， begin_hour填写10，此处填写了59， 则此处表示周一 10:59-00:59可用
    ],
    ]
     * @return bool|void
     */
    public function cardCreateByGroipon(string $dealDetail, array $baseInfo, array $advancedinfo = []){
        $data['card'] = [
            'card_type' => 'GROUPON',
            'groupon' => [
                'base_info' => $baseInfo,
                'advanced_info' => $advancedinfo,
                'deal_detail' => $dealDetail
            ],
        ];

        return $this->cardByCreate($data);
    }
    /**
     * @var 代金卷
     * @param integer $least_cost 代金券专用，表示起用金额（单位为分）,如果无起用门槛则填0。
     * @param integer $reduce_cost 代金券专用，表示减免金额。（单位为分）
     * @param array $baseInfo @see cardCreateByGroipon $baseInfo
     * @param array $advancedinfo @see cardCreateByGroipon $advancedinfo
     */
    public function cardCreateByCash($least_cost, $reduce_cost, array $baseInfo, array $advancedinfo = []){
        $data['card'] = [
            'card_type' => 'CASH',
            'cash' => [
                'base_info' => $baseInfo,
                'advanced_info' => $advancedinfo,
                'least_cost' => $least_cost,
                'reduce_cost' => $reduce_cost,
            ],
        ];

        return $this->cardByCreate($data);
    }
    /**
     * @var 折扣券类型。
     * @param integer $discount 折扣券专用，表示打折额度（百分比）。填30就是七折。
     * @param array $baseInfo @see cardCreateByGroipon $baseInfo
     * @param array $advancedinfo @see cardCreateByGroipon $advancedinfo
     * @return bool|void
     */
    public function cardCreateByDiscount($discount, array $baseInfo, array $advancedinfo = []){
        $data['card'] = [
            'card_type' => 'DISCOUNT',
            'discount' => [
                'base_info' => $baseInfo,
                'advanced_info' => $advancedinfo,
                'discount' => $discount,
            ],
        ];

        return $this->cardByCreate($data);
    }
    /**
     * @var 兑换券类型。
     * @param string $gift 兑换券专用，填写兑换内容的名称。
     * @param array $baseInfo @see cardCreateByGroipon $baseInfo
     * @param array $advancedinfo @see cardCreateByGroipon $advancedinfo
     * @return bool|void
     */
    public function cardCreateByGift($gift, array $baseInfo, array $advancedinfo = []){
        $data['card'] = [
            'card_type' => 'GIFT',
            'gift' => [
                'base_info' => $baseInfo,
                'advanced_info' => $advancedinfo,
                'gift' => $gift,
            ],
        ];

        return $this->cardByCreate($data);
    }
    /**
     * @var 优惠券类型。
     * @param string $default_detail 优惠券专用，填写优惠详情。
     * @param array $baseInfo @see cardCreateByGroipon $baseInfo
     * @param array $advancedinfo @see cardCreateByGroipon $advancedinfo
     * @return bool|void
     */
    public function cardCreateByGeneralCoupon($default_detail, array $baseInfo, array $advancedinfo = []){
        $data['card'] = [
            'card_type' => 'GENERAL_COUPON',
            'general_coupon' => [
                'base_info' => $baseInfo,
                'advanced_info' => $advancedinfo,
                'default_detail' => $default_detail,
            ],
        ];

        return $this->cardByCreate($data);
    }
    /**
     * @var 设置自助买单接口
     * @param string $cardId 卡券ID
     * @param bool $bool 是否开启买单功能，填true/false
     * @return bool|void
     */
    public function cardPayCellBySelfHelp($cardId, $bool = true){
        $data['card_id'] = $cardId;
        $data['is_open'] = $bool;

        if(!$this->httpPost(self::API_CARD_PAY_CALL_SET_URL, $data, true)){
            return false;
        }

        return true;
    }

    /**
     * @var 设置自助核销接口
     * @param string $cardId 卡券ID
     * @param bool $bool 是否开启自助功能，填true/false
     * @param bool $need_verify_cod 用户核销时是否需要输入验证码， 填true/false， 默认为false
     * @param bool $need_remark_amount 用户核销时是否需要备注核销金额， 填true/false， 默认为false
     * @return bool
     */
    public function cardConsumeCellBySelfHelp($cardId, $bool = true, $need_verify_cod = false, $need_remark_amount = false){
        $data['card_id'] = $cardId;
        $data['is_open'] = $bool;
        $data['need_verify_cod'] = $need_verify_cod;
        $data['need_remark_amount'] = $need_remark_amount;

        if(!$this->httpPost(self::API_CARD_SELF_CONSUME_CALL_SET_URL, $data, true)){
            return false;
        }

        return true;
    }
    /**
     * @var 创建卡卷投放二维码
     * @param string $card_id 卡券ID。
     * @param $expire_seconds 指定二维码的有效时间，范围是60 ~ 1800秒。不填默认为365天有效
     * @param string $code 卡券 Code 码,use_custom_code字段为 true 的卡券必须填写，非自定义 code 和导入 code 模式的卡券不必填写。
     * @param string $outer_str 用户首次领卡时，会通过 领取事件推送 给商户； 对于会员卡的二维码，用户每次扫码打开会员卡后点击任何url，会将该值拼入 url 中，方便开发者定位扫码来源
     * @param string $openid 指定领取者的openid，只有该用户能领取。bind_openid字段为 true 的卡券必须填写，非指定 openid 不必填写
     * @param bool $is_unique_code 指定下发二维码，生成的二维码随机分配一个code，领取后不可再次扫描。填写 true 或false。默认false，注意填写该字段时，卡券须通过审核且库存不为0。
     * @return false | ['ticket' => '获取的二维码ticket，凭借此 ticket 调用 通过 ticket 换取二维码接口 可以在有效时间内换取二维码。', 'expire_seconds' => , 'url' => '二维码图片解析后的地址，开发者可根据该地址自行生成需要的二维码图片', 'show_qrcode_url' => '二维码显示地址，点击后跳转二维码页面']
     */
    public function cardQrcodeOneByCreate($card_id, $expire_seconds = '', $code = '', $outer_str = '', $openid = '', $is_unique_code = true){
        $data['action_name'] = 'QR_CARD';
        $data['expire_seconds'] = $expire_seconds;
        $data['action_info'] = [
            'card' => [
                'card_id' => $card_id,
                'code' => $card_id,
                'openid' => $card_id,
                'is_unique_code' => $card_id,
                'outer_str' => $card_id,
            ],
        ];

        return $this->httpPost(self::API_CARD_QRCODE_URL, $data, true);
    }
    /**
     * @var 创建卡卷投放二维码
     * @param array $card_list @see cardQrcodeOneByCreate 的 card
     * @return bool|['ticket' => '获取的二维码ticket，凭借此 ticket 调用 通过 ticket 换取二维码接口 可以在有效时间内换取二维码。', 'expire_seconds' => , 'url' => '二维码图片解析后的地址，开发者可根据该地址自行生成需要的二维码图片', 'show_qrcode_url' => '二维码显示地址，点击后跳转二维码页面']
     */
    public function cardQrcodeMulByCreate(array $card_list){
        $data['action_name'] = 'QR_MULTIPLE_CARD';
        $data['action_info']['card_list'] = $card_list;
        return $this->httpPost(self::API_CARD_QRCODE_URL, $data, true);
    }
    /**
     * @var 创建卡卷货架领取页面
     * @param string $banner 页面的 banner 图片链接，须调用，建议尺寸为640*300。
     * @param string $page_title
     * @param bool $can_share 页面是否可以分享,填入true/false
     * @param string $scene 投放页面的场景值； SCENE_NEAR_BY 附近 SCENE_MENU 自定义菜单 SCENE_QRCODE 二维码 SCENE_ARTICLE 公众号文章 SCENE_H5 h5页面 SCENE_IVR 自动回复 SCENE_CARD_CUSTOM_CELL 卡券自定义cell
     * @param array $card_list  [ ['card_id' => '卡卷ID', 'thumb_url' => '图片地址']]
     * @return bool|['url' => '货架链接。', 'page_id' => '货架ID。货架的唯一标识']
     */
    public function cardLandingpageByCreate($banner, $page_title, $can_share, $scene, array $card_list){
        $data['banner'] = $banner;
        $data['page_title'] = $page_title;
        $data['can_share'] = $can_share;
        $data['scene'] = $scene;
        $data['card_list'] = $card_list;

        return $this->httpPost(self::API_CARD_LANDINGOAGE_URL, $data, true);
    }
    /**
     * @var 导入卡卷码
     * @param string $card_id 需要进行导入 code 的卡券ID。
     * @param array $codes ['6664744'] 需导入微信卡券后台的自定义code，上限为100个。
     * @return bool| integer
     */
    public function cardCodeByImport($card_id, array $codes){
        $data['card_id'] = $card_id;
        $data['code'] = $codes;
        if($result = $this->httpPost(self::API_CARD_CODE_IMPORT_URL, $data, true)){
            return false;
        }

        return $result['succ_code'];
    }
    /**
     * @var 获取导入 code 数目接口
     * @param string $card_id 需要进行导入 code 的卡券ID。
     * @return bool|integer
     */
    public function cardCodeByCount($card_id){
        $data['card_id'] = $card_id;
        if($result = $this->httpPost(self::API_CARD_CODE_COUNT_URL, $data, true)){
            return false;
        }

        return $result['count'];
    }
    /**
     * @var 获取暂未导入的code
     * @param string $card_id 需要进行导入 code 的卡券ID。
     * @param array $codes ['6664744'] 要查询的code，上限为100个。
     * @return bool|array ['6655544', ]
     */
    public function cardCodeByExist($card_id, array $code){
        $data['card_id'] = $card_id;
        $data['code'] = $code;
        if($result = $this->httpPost(self::API_CARD_CODE_EIEXTS_URL, $data, true)){
            return false;
        }

        return $result['not_exist_code'];
    }
    //------------------------------卡卷管理--------------------------------//
}
