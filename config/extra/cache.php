<?php

// +----------------------------------------------------------------------
// | 缓存设置
// +----------------------------------------------------------------------

use think\Env;
use helper\TimeHelper;

return [
    // 使用复合缓存类型
    'type'     => 'complex',
    // 默认使用的缓存
    'default'  => [
        'type'   => Env::get('cache.cache_type', 'file'),
    ],
    // 文件缓存
    'file'     => [
        // 驱动方式
        'type'   => 'File',
        // 缓存保存目录
        'path'   => CACHE_PATH,
        // 缓存有效期 0表示永久缓存
        'expire' => TimeHelper::daysToSecond(7),
        // 缓存前缀
        'prefix' => Env::get('app.project_name', ''),
    ],
    // redis缓存
    'redis'    => [
        // 驱动方式
        'type'   => 'Redis',
        // 服务器地址
        'host'   => '127.0.0.1',
        // 端口号
        'port'   => 6379,
        // 缓存有效期 0表示永久缓存
        'expire' => TimeHelper::daysToSecond(7),
        // 缓存前缀
        'prefix' => Env::get('app.project_name', ''),
    ],
    // memcache缓存
    'memcache' => [
        // 驱动方式
        'type'   => 'Memcache',
        // 服务器地址
        'host'   => '127.0.0.1',
        // 端口号
        'port'   => 11211,
        // 缓存有效期 0表示永久缓存
        'expire' => TimeHelper::daysToSecond(7),
        // 缓存前缀
        'prefix' => Env::get('app.project_name', ''),
    ],
];
