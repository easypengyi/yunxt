<?php

return [
    'config' => [
        'member' => [
            'class'        => tool\push\Jpush::class,
            'appKey'       => '',
            'masterSecret' => '',
            'name'         => 'member',
            'log'          => RUNTIME_PATH . 'push_log' . DS . 'member_' . date('Ymd') . '.log',
            'enable'       => false,
        ],
    ],

    'enable' => false,

    'default' => 'member',
];
