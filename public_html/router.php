<?php
// 快速测试文件
// 用于php自带webserver支持，可用于快速测试
// 启动命令：php -S localhost:8888 router.php

if (is_file($_SERVER['DOCUMENT_ROOT'] . $_SERVER['REQUEST_URI'])) {
    return false;
} else {
    require __DIR__ . DIRECTORY_SEPARATOR . 'index.php';
}
