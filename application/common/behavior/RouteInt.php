<?php

namespace app\common\behavior;

use UserAgent;
use think\Cache;
use think\Route;
use app\common\model\Route as RouteModel;

/**
 * 路由加载
 */
class RouteInt
{
    /**
     * 运行方法
     * @return bool
     */
    public function run()
    {
        $routes = $this->route_list();
        foreach ($routes as $key => $route) {
            Route::rule(...$route);
        }
        return true;
    }

    /**
     * 路由列表
     * @return array
     */
    private function route_list()
    {
        $routes = Cache::get(__CLASS__ . __FUNCTION__);
        if (empty($routes)) {
            $routes = [];
            $data   = RouteModel::route_array();
            foreach ($data as $key => $rule) {
                $routes[] = [$key, $rule, 'post|get', [], []];
            }
            Cache::tag(RouteModel::getCacheTag())->set(__CLASS__ . __FUNCTION__, $routes);
        }
        return $routes;
    }
}
