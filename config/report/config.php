<?php

use paginator\Api;

return [
    // 应用Trace
    'app_trace' => false,

    //分页配置
    'paginate'  => [
        'type'      => Api::class,
        'var_page'  => 'page',
        'list_rows' => 20,
    ],

    'api_timeout'       => 60,

    // 必须使用post传递数据
    'must_post'         => false,
    // 必须进行接口验证
    'must_verification' => true,
    // 必须进行ip认证
    'must_ip_check'     => false,
];
