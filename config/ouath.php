<?php

use app\common\model\OauthUser;

// 第三方登录数据
return [
    ['id' => OauthUser::QQ, 'name' => 'QQ', 'image' => 'static/img/qq_login.png'],
    ['id' => OauthUser::WX, 'name' => '微信', 'image' => 'static/img/wx_login.png'],
];