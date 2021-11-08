<?php

use paginator\Api;
use app\common\Constant;

return [
    // 应用Trace
    'app_trace' => false,

    //分页配置
    'paginate'  => [
        'type'      => Api::class,
        'var_page'  => 'page',
        'list_rows' => 8,
    ],

    'api_timeout'       => 60,

    // 必须使用post传递数据
    'must_post'         => false,
    // 必须进行客户端类型验证
    'must_apptype'      => true,
    // 必须进行版本验证
    'must_appversion'   => true,
    // 必须进行接口验证
    'must_verification' => true,
    // 使用消息
    'allow_message'     => true,

    // 接口允许的客户端类型
    'allow_apptype'     => [Constant::CLIENT_WEB, Constant::CLIENT_WAP, Constant::CLIENT_WX],
];
