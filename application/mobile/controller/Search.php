<?php

namespace app\mobile\controller;

use app\common\controller\MobileController;

/**
 * 搜索
 */
class Search extends MobileController
{
    /**
     * 搜索
     * @return mixed
     */
    public function index()
    {
        $this->assign('keyword', input('keyword', ''));
        $this->assign('title', '搜索');
        $this->assign('return_url', folder_url('Product/product_list'));
        return $this->fetch();
    }

    /**
     * 报告搜索
     * @return mixed
     */
    public function report_index()
    {
        $this->login_check();

        $this->assign('keyword', input('keyword', ''));
        $this->assign('title', '搜索');
        $this->assign('return_url', folder_url('User/report'));
        return $this->fetch('index');
    }
}