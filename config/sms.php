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
];
