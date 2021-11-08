<?php

namespace app\admin\controller;

use Exception;
use app\common\controller\AdminController;
use app\common\model\MemberAddress as MemberAddressModel;

/**
 * 收货地址 模块
 */
class MemberAddress extends AdminController
{
    protected $member_id = 0;

    /**
     * 初始化方法
     * @return void
     * @throws Exception
     */
    public function _initialize()
    {
        parent::_initialize();

        $this->member_id = input('member_id', '');
    }

    /**
     * 列表
     * @return mixed
     * @throws Exception
     */
    public function index()
    {
        $where['member_id'] = $this->member_id;

        $order = $this->sort_order(MemberAddressModel::getTableFields(), 'create_time', 'desc');

        $list = MemberAddressModel::page_list($where, $order);
        if (!$list->isEmpty()) {
            $list->load(['provinceRegion', 'cityRegion', 'districtRegion']);
        }

        $this->assign($list->toArray());
        $this->assign('return_url', folder_url('Member/index'));
        return $this->fetch_view('', ['member_id']);
    }
}
