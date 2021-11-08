<?php

// 访问日志配置

return [
    // 日志启用
    'enable'                 => true,
    // 不记录模块
    'not_log_module'         => ['tool', 'api', 'index', 'mobile', 'upload'],
    // 不记录控制器
    'not_log_controller'     => [
        'admin/Ajax',
        'admin/Common',
        'admin/LogWeb',
    ],
    // 不记录的操作
    'not_log_action'         => [],
    // 不记录的请求类型
    'not_log_request_method' => [],
    // 不记录数据的操作
    'not_log_data'           => [
        'admin/Login/index',
        'admin/Admin/profile',
    ],
];