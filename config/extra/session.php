<?php

// +----------------------------------------------------------------------
// | 会话设置
// +----------------------------------------------------------------------

use think\Env;
use helper\TimeHelper;

return [
    'id'             => '',
    // SESSION_ID的提交变量,解决flash上传跨域
    'var_session_id' => '',
    // SESSION 前缀
    'prefix'         => Env::get('app.project_name', 'think'),
    // 驱动方式 支持redis memcache memcached
    'type'           => Env::get('session.session_type', ''),
    // 是否自动开启 SESSION
    'auto_start'     => true,
    'httponly'       => true,
    'secure'         => false,
    // 过期时间
    'expire'         => TimeHelper::minutesToSecond(120),
];