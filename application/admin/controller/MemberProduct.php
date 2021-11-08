<?php

namespace app\admin\controller;

use Exception;
use app\common\controller\AdminController;
use app\common\model\OrderShop as OrderShopModel;

/**
 * 会员商品 模块
 */
class MemberProduct extends AdminController
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

        $where['status']        = [
            'not in',
            [
                OrderShopModel::STATUS_NO,
                OrderShopModel::STATUS_INVALID,
                OrderShopModel::STATUS_WAIT_PAY,
            ],
        ];
        $where['refund_status'] = [
            'not in',
            [OrderShopModel::REFUND_STATUS_SUCCESS, OrderShopModel::REFUND_STATUS_FINISH],
        ];

        $order = $this->sort_order(OrderShopModel::getTableFields(), 'order_time', 'desc');

        $list = OrderShopModel::page_list($where, $order);
        if (!$list->isEmpty()) {
        }

        $this->assign($list->toArray());
        $this->assign('total_money', OrderShopModel::where($where)->sum('amount'));
        $this->assign('return_url', folder_url('Member/index'));
        return $this->fetch_view('', ['member_id']);
    }
}
