<?php

use paginator\Home;

return [
    // 应用Trace
    'app_trace' => false,

    'public_title' => '',
    'login_tile'   => '',
    'name'         => '',

    //分页配置
    'paginate'     => [
        'type'      => Home::class,
        'var_page'  => 'page',
        'list_rows' => 10,
    ],

    // 登录验证码
    'login_verify' => false,
];
