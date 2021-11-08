<?php

return [
    // 应用调试模式
    'app_debug'  => false,
    // 应用Trace
    'app_trace'  => false,

    // 二维码配置
    'qr_code'    => [
        'prefix' => 'qrcode',
    ],

    // 条形码配置
    'bar_code'   => [
        'prefix' => 'barcode',
    ],

    // 地图定位配置
    // 高德地图配置
    'location'   => [
        'geomap_ak' => '',
        // 默认中心经纬度
        'longitude' => 118.089,
        'latitude'  => 24.479,
    ],

    // 缩略图配置
    'thumb'      => [
        'path' => DATA_PATH . 'thumb' . DS,
    ],
];
