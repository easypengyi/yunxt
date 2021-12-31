<?php

use app\common\model\SmsCode;

return [
    'sms_config' => [
        'yxs'  => [
            'class'    => tool\sms\YxsSms::class,
            'sms_open' => true,
            'api'      => 'http://api.sms.cn/sms/',
            'uid'      => 'yhcl366754',
            'pwd'      => 'e0320619a8c9f96c70c178ac5fe3d91e',
            'module'   => [
                SmsCode::BIND_PHONE              => ['id' => '550153', 'value' => ['code' => '']],
            ],
        ],
    ],

    'sms_open' => true,
    'sms_default' => 'yxs',
    'sms_aliyun' => [
        'access_key' => 'LTAI5tDGrHRSR7EdP3Ut6igF',
        'access_secret' => 'CntfoSLmInIrdluTInOaPILx3r7691',
        'sign' => '雅典娜商业管理',
        'order' => 'SMS_231442284', //下单提醒
        'stock' => 'SMS_231452220', //库存提醒
        'cash' => 'SMS_231437242'  //提现申请
    ]
];
