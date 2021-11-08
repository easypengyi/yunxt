<?php

namespace app\mobile\controller;

use Exception;
use think\response\Json;
use app\common\controller\MobileController;

/**
 * 区域信息
 */
class Region extends MobileController
{
    /**
     * 初始化方法
     * @return void
     */
    protected function _initialize()
    {
        parent::_initialize();

        if (!$this->check_referer()) {
            $this->error('非法请求！');
        }
    }

    /**
     * 返回链接设置
     * @return void
     */
    protected function set_return_url()
    {
        return;
    }

    /**
     * 获取全部省市区信息接口
     * @return Json
     * @throws Exception
     */
    public function area()
    {
        $result = $this->api('Region', 'area');
        return json(['data' => $result['data']['list']]);
    }


    public function level()
    {
        $result = $this->api('Region', 'level');
        return json(['data' => $result['data']['list']]);
    }
}
