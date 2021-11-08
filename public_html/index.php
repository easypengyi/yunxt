<?php

// [ 应用入口文件 ]
// 加载常量配置文件
require dirname(__DIR__) . DIRECTORY_SEPARATOR . 'constants.php';

// 定义项目路径
defined('APP_PATH') or define('APP_PATH', ROOT_PATH . 'application' . DIRECTORY_SEPARATOR);

// 加载框架引导文件
require ROOT_PATH . 'thinkphp' . DIRECTORY_SEPARATOR . 'start.php';
