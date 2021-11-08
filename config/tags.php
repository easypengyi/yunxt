<?php

use app\common\behavior\Base;
use app\common\behavior\LogWeb;
use app\common\behavior\RouteInt;

// 应用行为扩展定义文件
return [
    // 应用初始化
    'app_init'     => [RouteInt::class],
    // 应用调度
    'app_dispatch' => [],
    // 应用开始
    'app_begin'    => [Base::class],
    // 模块初始化
    'module_init'  => [],
    // 操作开始执行
    'action_begin' => [LogWeb::class],
    // 视图内容过滤
    'view_filter'  => [],
    // 日志写入
    'log_write'    => [],
    // 应用结束
    'app_end'      => [],
];
