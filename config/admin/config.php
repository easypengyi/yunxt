<?php

use paginator\Admin;

return [
    // 应用Trace
    'app_trace' => false,

    'public_title'  => '后台管理',
    'login_tile'    => '用户登录',
    'name'          => '',

    //分页配置
    'paginate'      => [
        'type'      => Admin::class,
        'var_page'  => 'page',
        'list_rows' => 10,
    ],

    // 登录验证码
    'login_verify'  => false,
];
