<?php

use tool\PaymentTool;

return [
    'list'       => [
        PaymentTool::ALIPAY  => [
            'name'       => '支付宝支付',
            'short_name' => '支付宝',
            'thumb'      => base_url('static/img/zfb.png'),
            'activation' => false,
        ],
        PaymentTool::WXPAY   => [
            'name'       => '微信支付',
            'short_name' => '微信',
            'thumb'      => base_url('static/img/weixin.png'),
            'activation' => true,
        ],
        PaymentTool::UPACPAY => [
            'name'       => '银联支付',
            'short_name' => '银联',
            'thumb'      => base_url('static/img/yl.png'),
            'activation' => false,
        ],
        PaymentTool::BALANCE => [
            'name'       => '余额支付',
            'short_name' => '余额',
            'thumb'      => base_url('static/img/yezf.png'),
            'activation' => true,
        ],
        PaymentTool::UPAY    => [
            'name'       => 'U付支付',
            'short_name' => 'U付',
            'thumb'      => base_url('static/img/yl.png'),
            'activation' => false,
        ],
    ],
    // 排序 [排序类型=>支付类型数组]
    'order'      => [
        1 => [PaymentTool::ALIPAY, PaymentTool::WXPAY, PaymentTool::BALANCE],
        2 => [PaymentTool::ALIPAY, PaymentTool::WXPAY],
    ],
    // 回调地址
    'notify_url' => 'callback/Defray/callback',
];