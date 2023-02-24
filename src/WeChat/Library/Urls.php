<?php

namespace Kang\Libs\WeChat\Library;


class Urls{
    const BASE_URL = 'https://api.weixin.qq.com';
    const BASE_URL_1 = 'https://mp.weixin.qq.com';

    //Access token
    const ACCESS_TOKEN_URL = self::BASE_URL . '/cgi-bin/token?';

    //openApi管理
    const OPEN_API_CLEAR_QUOTA = self::BASE_URL . '/cgi-bin/clear_quota?access_token='; //清空 api 的调用quota
    const OPEN_API_GET_QUOTA = self::BASE_URL . '/cgi-bin/openapi/quota/get?access_token='; //查询 openAPI 调用quota
    const OPEN_API_RID_QUOTA = self::BASE_URL . '/cgi-bin/openapi/rid/get?access_token='; //查询 rid 信息

    //自定义菜单
    const MENU_BASE_CREATE = self::BASE_URL . '/cgi-bin/menu/create?access_token='; //普通菜单创建接口
    const MENU_BASE_GET = self::BASE_URL . '/cgi-bin/get_current_selfmenu_info?access_token='; //普通菜单查询接口
    const MENU_BASE_DEL = self::BASE_URL . '/cgi-bin/menu/delete?access_token='; //普通菜单删除接口

    const MENU_CONDITIONAL_CREATE = self::BASE_URL . '/cgi-bin/menu/addconditional?access_token='; //创建个性化菜单
    const MENU_CONDITIONAL_DEL = self::BASE_URL . '/cgi-bin/menu/delconditional?access_token='; //个性化菜单删除接口
    const MENU_CONDITIONAL_MATCH = self::BASE_URL . '/cgi-bin/menu/trymatch?access_token='; //个性化菜单匹配结果


    //模板消息
    const MESSAGE_TEMPLATE_SET_INDUSTRY = self::BASE_URL . '/cgi-bin/template/api_set_industry?access_token='; //设置所属行业
    const MESSAGE_TEMPLATE_GET_INDUSTRY = self::BASE_URL . '/cgi-bin/template/get_industry?access_token='; //设置所属行业

    const MESSAGE_TEMPLATE_CREATE = self::BASE_URL . '/cgi-bin/template/api_add_template?access_token='; //获得模板ID
    const MESSAGE_TEMPLATE_GET = self::BASE_URL . '/cgi-bin/template/get_all_private_template?access_token='; //获取模板列表
    const MESSAGE_TEMPLATE_DEL = self::BASE_URL . '/cgi-bin/template/del_private_template?access_token='; //删除模板
    const MESSAGE_TEMPLATE_SEND = self::BASE_URL . '/cgi-bin/message/template/send?access_token='; //发送模板消息

    //客服
    const MESSAGE_KF_ACCOUNT_CREATE = self::BASE_URL . '/customservice/kfaccount/add?access_token='; //添加客服帐号
    const MESSAGE_KF_ACCOUNT_MODIFY = self::BASE_URL . '/customservice/kfaccount/update?access_token='; //修改客服帐号
    const MESSAGE_KF_ACCOUNT_DEL = self::BASE_URL . '/customservice/kfaccount/del?access_token='; //删除客服帐号
    const MESSAGE_KF_ACCOUNT_HEAD = self::BASE_URL . '/customservice/kfaccount/uploadheadimg?access_token='; //设置客服帐号的头像
    const MESSAGE_KF_ACCOUNT_SELECT = self::BASE_URL . '/cgi-bin/customservice/getkflist?access_token='; //获取所有客服账号
    const MESSAGE_KF_ACCOUNT_SEND = self::BASE_URL . '/cgi-bin/message/custom/send?access_token='; //客服接口 - 发消息

    //消息群发
    const MESSAGE_MASS_SEND = self::BASE_URL . '/cgi-bin/message/mass/sendall?access_token='; //消息群发
    const MESSAGE_MASS_GET = self::BASE_URL . '/cgi-bin/message/mass/get?access_token='; //查询群发消息发送状态
    const MESSAGE_MASS_DEL = self::BASE_URL . '/cgi-bin/message/mass/delete?access_token='; //删除群发
    const MESSAGE_MASS_PREVIEW = self::BASE_URL . '/cgi-bin/message/mass/preview?access_token='; //预览接口
    const MESSAGE_MASS_SPEED = self::BASE_URL . '/cgi-bin/message/mass/speed/set?access_token='; //设置群发速度
    const MESSAGE_MASS_UPLOAD_VIDEO = self::BASE_URL . '/cgi-bin/media/uploadvideo?access_token='; //视频上传

    //二维码
    const QRCODE_CREATE = self::BASE_URL . '/cgi-bin/qrcode/create?access_token='; //生成带参数的二维码
    const QRCODE_SHOW = 'https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket='; //通过 ticket 换取二维码

    //标签
    const TAG_CREATE = self::BASE_URL . '/cgi-bin/tags/create?access_token='; //上传图片获取URL
    const TAG_GET = self::BASE_URL . '/cgi-bin/tags/get?access_token='; // 获取公众号已创建的标签
    const TAG_MODIFY = self::BASE_URL . '/cgi-bin/tags/update?access_token='; // 编辑标签
    const TAG_DELETE = self::BASE_URL . '/cgi-bin/tags/delete?access_token='; // 删除标签
    const TAG_USERS_SELECT = self::BASE_URL . '/cgi-bin/user/tag/get?access_token='; // 获取标签下粉丝列表
    const TAG_USERS_SET = self::BASE_URL . '/cgi-bin/tags/members/batchtagging?access_token='; //批量为用户打标签
    const TAG_USERS_UN_SET = self::BASE_URL . '/cgi-bin/tags/members/batchuntagging?access_token='; //批量为用户取消标签

    //用户
    const USER_OAUTH2 = 'https://open.weixin.qq.com/connect/oauth2/authorize?'; //用户同意授权
    const USER_OAUTH2_ACCESS_TOKEN = self::BASE_URL . '/sns/oauth2/access_token?'; //通过 code 换取网页授权access_token
    const USER_OAUTH2_INFO = self::BASE_URL . '/sns/userinfo?access_token='; //拉取用户信息(需 scope 为 snsapi_userinfo)
    const USER_UNIONN_ID_INFO = self::BASE_URL . '/cgi-bin/user/info?access_token='; //拉取用户信息(需 scope 为 snsapi_userinfo)
    const USER_INFO_SELECT = self::BASE_URL . '/cgi-bin/user/info/batchget?access_token='; //批量获取用户基本信息
    const USER_REMARK_SET = self::BASE_URL . '/cgi-bin/user/info/updateremark?access_token='; //设置用户备注名
    const USER_OPENID_SELECT = self::BASE_URL . '/cgi-bin/user/get?'; //获取用户列表

    const USER_BLACK_OPENID_SELECT = self::BASE_URL . '/cgi-bin/tags/members/getblacklist?access_token='; //获取公众号的黑名单列表
    const USER_BLACK_OPENID_SET = self::BASE_URL . '/cgi-bin/tags/members/batchblacklist?access_token='; //拉黑用户
    const USER_BLACK_OPENID_UN_SET = self::BASE_URL . '/cgi-bin/tags/members/batchunblacklist?access_token='; //取消拉黑用户

    //资源-素材管理
    const FILE_UPLOAD_IMAGE = self::BASE_URL . '/cgi-bin/media/uploadimg?access_token='; //上传图片获取URL
    const MEDIA_UPLOAD_TEMPORARY = self::BASE_URL . '/cgi-bin/media/upload?access_token='; //新增临时素材
    const MEDIA_UPLOAD_LONG = self::BASE_URL . '/cgi-bin/material/add_material?access_token='; //新增其他类型永久素材
    const MEDIA_DEL = self::BASE_URL . '/cgi-bin/material/del_material?access_token='; //删除永久素材
    const MEDIA_CONST = self::BASE_URL . '/cgi-bin/material/get_materialcount?access_token='; //获取素材总数
    const MEDIA_LIST = self::BASE_URL . '/cgi-bin/material/batchget_material?access_token='; //获取素材列表

    //草稿箱
    const DRAFT_CREATE = self::BASE_URL . '/cgi-bin/draft/add?access_token='; //新建草稿
    const DRAFT_GET = self::BASE_URL . '/cgi-bin/draft/get?access_token='; //获取草稿
    const DRAFT_DEL = self::BASE_URL . '/cgi-bin/draft/delete?access_token='; //删除草稿
    const DRAFT_MODIFY = self::BASE_URL . '/cgi-bin/draft/update?access_token='; //修改草稿
    const DRAFT_COUNT = self::BASE_URL . '/cgi-bin/draft/count?access_token='; //获取草稿总数
    const DRAFT_LIST = self::BASE_URL . '/cgi-bin/draft/batchget?access_token='; //获取草稿列表

    //草稿发表
    const DRAFT_PUSH = self::BASE_URL . '/cgi-bin/freepublish/submit?access_token='; //发布
    const DRAFT_PUSH_STATUS = self::BASE_URL . '/cgi-bin/freepublish/get?access_token='; //发布状态轮询接口
    const DRAFT_PUSH_DEL = self::BASE_URL . '/cgi-bin/freepublish/delete?access_token='; //删除发布
    const DRAFT_PUSH_ARTICLE = self::BASE_URL . '/cgi-bin/freepublish/getarticle?access_token='; //通过 article_id 获取已发布文章
    const DRAFT_PUSH_SUCCESS_ARTICLE = self::BASE_URL . '/cgi-bin/freepublish/batchget?access_token='; //获取成功发布列表

    //评论
    const COMMENT_OPEN = self::BASE_URL . '/cgi-bin/comment/open?access_token='; //打开已群发文章评论（新增接口）
    const COMMENT_CLOSE = self::BASE_URL . '/cgi-bin/comment/close?access_token='; //关闭已群发文章评论
    const COMMENT_LIST = self::BASE_URL . '/cgi-bin/comment/list?access_token='; //查看指定文章的评论数据
    const COMMENT_SET_FEATURED = self::BASE_URL . '/cgi-bin/comment/markelect?access_token='; //将评论标记精选
    const COMMENT_UN_SET_FEATURED = self::BASE_URL . '/cgi-bin/comment/unmarkelect?access_token='; //将评论取消精选
    const COMMENT_DEL = self::BASE_URL . '/cgi-bin/comment/delete?access_token='; //删除评论
    const COMMENT_REPLY = self::BASE_URL . '/cgi-bin/comment/reply/add?access_token='; //回复评论
    const COMMENT_REPLY_DEL = self::BASE_URL . '/cgi-bin/comment/reply/delete?access_token='; //删除回复

    //OCR接口

    const OCR_ID_CARD = self::BASE_URL  . '/cv/ocr/idcard?'; //身份证 OCR 识别接口
    const OCR_BANK_CARD_URL = self::BASE_URL  . '/cv/ocr/bankcard?'; //银行卡 OCR 识别接口
    const OCR_DRIVING_URL = self::BASE_URL  . '/cv/ocr/driving?'; //银行卡 OCR 行驶证
    const OCR_DRIVING_LICENSE_URL = self::BASE_URL  . '/cv/ocr/drivinglicense?'; //驾驶证 OCR 行驶证
    const OCR_BIZLICENSE_URL = self::BASE_URL  . '/cv/ocr/drivinglicense?'; //营业执照 OCR 行驶证
    const OCR_PLATENUM_URL = self::BASE_URL  . '/cv/ocr/platenum?'; //车牌识别接口 OCR 行驶证
    const OCR_MENU_URL = self::BASE_URL  . '/cv/ocr/menu?'; //菜单识别接口 OCR 行驶证

}
