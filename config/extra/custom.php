<?php

// 定义配置

return [
    // 分享地址
    'share_url'             => 'download/Index/index',
    // logo图片地址
    'logo_image'            => 'static/img/zfb.png',
    // 前端文件刷新版本号
    'view_file_version'     => '9',
    // 邀请码验证 是否必须正确
    'invitation_code_check' => true,
    // 第三方登录是否必须绑定号码
    'oauth_must_bind'       => true,
    // 首次绑定手机号码 设置密码
    'bind_mobile_set_pwd'   => true,
    // 第三方登录是否账号合并
    'oauth_merge_account'   => true,
];
