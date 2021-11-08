<?php

namespace app\common\behavior;

use Exception;
use UserAgent;
use think\Log;
use think\Config;
use think\Request;
use Zhuzhichao\IpLocationZh\Ip;
use app\common\model\LogWeb as LogWebModel;

/**
 * 日志类
 */
class LogWeb
{
    /**
     * 运行方法
     * @param $call
     * @return bool
     */
    public function run($call)
    {
        if (!Config::get('log_web.enable')) {
            return true;
        }

        $request = Request::instance();

        $module = $request->module();

        //不记录的模块
        $not_log_module = Config::get('log_web.not_log_module') ?: [];

        if (in_array($module, $not_log_module)) {
            return true;
        }

        $controller = $request->controller();

        //不记录的控制器 'module/controller'
        $not_log_controller = Config::get('log_web.not_log_controller') ?: [];

        if (in_array($module . '/' . $controller, $not_log_controller)) {
            return true;
        }

        $action = $request->action();

        //不记录的操作方法 'module/controller/action'
        $not_log_action = Config::get('log_web.not_log_action') ?: [];

        if (in_array($module . '/' . $controller . '/' . $action, $not_log_action)) {
            return true;
        }

        $method = $request->method();

        //不记录的请求类型
        $not_log_request_method = Config::get('log_web.not_log_request_method') ?: [];

        if (in_array($method, $not_log_request_method)) {
            return true;
        }

        $controller_class = $call[0];

        //不记录data的操作方法 'module/controller/action'
        $not_log_data = Config::get('log_web.not_log_data') ?: [];

        try {
            if (method_exists($controller_class, 'operator_info')) {
                $arr = call_user_func([$call[0], 'operator_info']);
            }

            $operator_id = $arr['operator_id'] ?? 0;
            $type        = $arr['type'] ?? LogWebModel::TYPE_NO;

            if (in_array($module . '/' . $controller . '/' . $action, $not_log_data)) {
                $requestData = '保密数据';
            } else {
                $requestData = $request->param();
            }

            $data = [
                'ip'          => $request->ip(),
                'operator_id' => $operator_id,
                'type'        => $type,
                'location'    => implode(' ', array_filter(array_unique(IP::find($request->ip())))),
                'os'          => UserAgent::instance()->platform(),
                'browser'     => UserAgent::instance()->browser(),
                'url'         => $request->url(),
                'module'      => $module,
                'controller'  => $controller,
                'action'      => $action,
                'method'      => $request->isAjax() ? 'Ajax' : ($request->isPjax() ? 'Pjax' : $method),
                'data'        => serialize($requestData),
                'create_time' => time(),
            ];
            LogWebModel::create($data);
        } catch (Exception $e) {
            Log::write($e->getMessage(), 'error');
        }
        return true;
    }
}
