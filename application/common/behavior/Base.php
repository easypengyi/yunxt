<?php

namespace app\common\behavior;

use Exception;
use think\Config;
use think\Log;
use think\Request;

/**
 * 初始化基础配置行为
 * 将扩展的全局配置本地化
 */
class Base
{
    /**
     * 执行方法
     * @param $dispatch
     * @throws Exception
     */
    public function run($dispatch)
    {
        // 获取当前模块名称
        $module     = $dispatch['module'][0];
        $controller = $dispatch['module'][1];
        empty($module) AND $module = Config::get('default_module');
        empty($controller) AND $controller = Config::get('default_controller');

        $module     = strtolower($module);
        $controller = strtolower($controller);

        $request = Request::instance();
        $base    = $request->root();
        $root    = strpos($base, '.') ? ltrim(dirname($base), DS) : $base;
        if ('' != $root) {
            $root = '/' . ltrim($root, '/');
        }
        $view_replace_str = [
            // 模块资源目录
            '__MODULE_CSS__'     => $root . '/static/css/' . $module,
            '__MODULE_JS__'      => $root . '/static/js/' . $module,
            '__MODULE_IMG__'     => $root . '/static/img/' . $module,
            // 控制器资源目录
            '__CONTROLLER_CSS__' => $root . '/static/css/' . $module . '/' . $controller,
            '__CONTROLLER_JS__'  => $root . '/static/js/' . $module . '/' . $controller,
            '__CONTROLLER_IMG__' => $root . '/static/img/' . $module . '/' . $controller,
            // 公共资源目录
            '__IMG__'            => $root . '/static/img',
        ];

        config('view_replace_str', array_merge(config('view_replace_str'), $view_replace_str));
    }
}
