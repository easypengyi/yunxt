<?php

//上传配置

return [
    // 文件保存路径
    'save_path'  => 'upload' . DS,
    // 最大可上传大小 字节数
    'size'       => 1073741824,
    // 最多可同时上传文件数
    'max_number' => 9,

    //阿里云OSS
    'accessKeyId' =>'LTAI5tKZyL1QpTv8h5PTRkqi',
    'accessKeySecret'=>'LEWViuDWThTQDcERQG3puK70l4UKLX',
    'endpoint'=>'oss-cn-hangzhou.aliyuncs.com',
    'bucket'=>'ydn-product',

];
