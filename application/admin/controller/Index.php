<?php

namespace app\admin\controller;

use Exception;
use app\common\controller\AdminController;

/**
 * 首页 模块
 */
class Index extends AdminController
{
    protected $no_check = true;

    /**
     * 初始化方法
     * @return void
     * @throws Exception
     */
    public function _initialize()
    {
        parent::_initialize();
    }

    /**
     * 后台首页
     * @return mixed
     * @throws Exception
     */
    public function index()
    {
        return $this->fetch_view();
    }
}
