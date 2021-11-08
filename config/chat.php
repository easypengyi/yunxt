<?php

use think\Env;
use think\Config;

return [
    // 服务器地址
    'websocket_server' => Config::get('app_host') . Env::get('chat.websocket_server'),
    // 加密配置
    'ace_config'       => 'chat_aes',
    // 是否启用WSS模式
    'wss'              => Env::get('chat.wss', true),
];
