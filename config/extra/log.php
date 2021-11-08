<?php

// +----------------------------------------------------------------------
// | 日志设置
// +----------------------------------------------------------------------

use think\Log;

return [
    // 日志记录方式，内置 file socket 支持扩展
    'type'        => 'File',
    // 日志保存目录
    'path'        => LOG_PATH,
    // 日志记录级别
    'level'       => [Log::DEBUG, Log::ERROR],
    // 独立日志
    'apart_level' => [Log::ERROR, Log::DEBUG, Log::SQL],
    // 最大文件数
    'max_files'   => 90,
];