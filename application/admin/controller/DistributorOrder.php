<?php


namespace app\admin\controller;


use app\common\controller\AdminController;
use app\common\model\OrderShop as OrderShopModel;
use Exception;
use helper\TimeHelper;

class DistributorOrder extends AdminController
{
    /**
     * 初始化方法
     * @return void
     * @throws Exception
     */
    public function _initialize()
    {
        parent::_initialize();
        $this->param['payment']       = [0 => '未支付', 2 => '微信', 4 => '余额'];
        $this->param['status']        = OrderShopModel::order_status_array();
        $this->param['refund_status'] = OrderShopModel::order_refund_status_array();
    }

    /**
     * 列表
     * @return mixed
     * @throws Exception
     */
    public function index()
    {
        $where = $this->search('order_sn', '输入需查询的订单号');

        $this->search['date']   = input('date', '');
        $this->search['status'] = input('status', '');
        $where['member_id']     = input('member_id', '');
        $range_time             = TimeHelper::range_time($this->search['date']);
        empty($range_time) OR $where['order_time'] = ['between', $range_time];

        $where['status'] = ['not in', [OrderShopModel::STATUS_INVALID]];
        empty($this->search['status']) OR $where['status'] = $this->search['status'];

        $where['del']        = false;
        $where['order_type'] = OrderShopModel::TYPE_BASE;

        $order = $this->sort_order(OrderShopModel::getTableFields(), 'order_time', 'desc');

        $list = OrderShopModel::page_list($where, $order);
        if (!$list->isEmpty()) {
            $list->load(['Member']);
        }

        $this->assign('total_money', OrderShopModel::where($where)->sum('amount'));
        $this->assign('member_id',$where['member_id']);
        $this->assign('return_url', folder_url('distributor/index'));
        $this->assign($list->toArray());
        return $this->fetch_view();
    }
}