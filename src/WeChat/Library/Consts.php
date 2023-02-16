<?php

namespace Kang\Libs\WeChat\Library;

class Consts{

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
}
