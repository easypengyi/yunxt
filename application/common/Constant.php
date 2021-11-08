<?php

namespace app\common;

/**
 * 常量
 */
class Constant
{
    // 客户端类型
    // ios
    const CLIENT_IOS = 1;
    // android
    const CLIENT_ANDROID = 2;
    // web
    const CLIENT_WEB = 3;
    // wap
    const CLIENT_WAP = 4;
    // 微信浏览器
    const CLIENT_WX = 5;
    // 微信小程序
    const CLIENT_LITE = 6;

    // 客户端分类
    // 基础会员端
    const CATEGORY_MEMBER = 1;
    // 核销人员端
    const CATEGORY_STAFF = 2;

    // 性别
    // 性别未设定
    const SEX_NO = 0;
    // 性别男
    const SEX_MEN = 1;
    // 性别女
    const SEX_WOMEN = 2;

    // 增减
    // 增加
    const CHANGE_INC = 1;
    // 减少
    const CHANGE_DEC = 0;

    // 手机类型
    // 类型 Android
    const MOBILE_CATEGORY_ANDROID = 1;
    // 类型 iPhone 4s
    const MOBILE_CATEGORY_IPHONE_4S = 2;
    // 类型 iPhone 5s
    const MOBILE_CATEGORY_IPHONE_5S = 3;
    // 类型 iPhone 6
    const MOBILE_CATEGORY_IPHONE_6 = 4;
    // 类型 iPhone 6plus
    const MOBILE_CATEGORY_IPHONE_6_PLUS = 5;

    // 格雷访问类型
    // IOS
    const GREEN_TYPE_IOS = 1;
    // Android
    const GREEN_TYPE_ANDROID = 2;
    // wap 网页
    const GREEN_TYPE_WAP = 3;

    // 发票类型
    // 不开票
    const INVOICE_NO = 1;
    // 个人
    const INVOICE_PERSONAL = 2;
    // 单位
    const INVOICE_COMPANY = 3;

    /**
     * 客户端组
     */
    public static function client_group()
    {
        return [
            self::CLIENT_IOS,
            self::CLIENT_ANDROID,
            self::CLIENT_WAP,
            self::CLIENT_WEB,
            self::CLIENT_WX,
            self::CLIENT_LITE,
        ];
    }

    /**
     * 客户端分类组
     * @return array
     */
    public static function category_group()
    {
        return [self::CATEGORY_MEMBER, self::CATEGORY_STAFF];
    }

    /**
     * 性别组
     * @return array
     */
    public static function sex_group()
    {
        return [self::SEX_MEN, self::SEX_WOMEN];
    }

    /**
     * 增减数组
     */
    public static function change_group()
    {
        return [self::CHANGE_DEC, self::CHANGE_INC];
    }

    /**
     * 手机类型组
     */
    public static function mobile_category_group()
    {
        return [
            self::MOBILE_CATEGORY_ANDROID,
            self::MOBILE_CATEGORY_IPHONE_4S,
            self::MOBILE_CATEGORY_IPHONE_5S,
            self::MOBILE_CATEGORY_IPHONE_6,
            self::MOBILE_CATEGORY_IPHONE_6_PLUS,
        ];
    }

    /**
     * 客户端数组
     * @return array
     */
    public static function client_array()
    {
        return [
            self::CLIENT_IOS     => 'IOS',
            self::CLIENT_ANDROID => 'android',
            self::CLIENT_WAP     => 'wap',
            self::CLIENT_WEB     => 'web',
            self::CLIENT_WX      => '微信浏览器',
            self::CLIENT_LITE    => '微信小程序',
        ];
    }

    /**
     * 手机客户端数组
     * @return array
     */
    public static function app_client_array()
    {
        return [self::CLIENT_IOS => 'IOS', self::CLIENT_ANDROID => 'android'];
    }

    /**
     * 性别数组
     * @return array
     */
    public static function sex_array()
    {
        return [
            self::SEX_NO    => '未设置',
            self::SEX_MEN   => '男',
            self::SEX_WOMEN => '女',
        ];
    }

    /**
     * 增减数组
     * @return array
     */
    public static function change_array()
    {
        return [
            self::CHANGE_DEC => '减少',
            self::CHANGE_INC => '增加',
        ];
    }

    /**
     * 手机类型数组
     */
    public static function mobile_category_array()
    {
        return [
            self::MOBILE_CATEGORY_ANDROID       => 'Android',
            self::MOBILE_CATEGORY_IPHONE_4S     => 'iPhone 4s',
            self::MOBILE_CATEGORY_IPHONE_5S     => 'iPhone 5s',
            self::MOBILE_CATEGORY_IPHONE_6      => 'iPhone 6',
            self::MOBILE_CATEGORY_IPHONE_6_PLUS => 'iPhone 6 plus',
        ];
    }

    /**
     * 发票类型数组
     * @return array
     */
    public static function invoice_array()
    {
        return [
            self::INVOICE_NO       => '不开票',
            self::INVOICE_PERSONAL => '个人',
            self::INVOICE_COMPANY  => '单位',
        ];
    }
}