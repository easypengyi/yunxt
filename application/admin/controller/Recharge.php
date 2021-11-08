<?php

namespace app\admin\controller;

use Exception;
use tool\PaymentTool;
use app\common\controller\AdminController;
use app\common\model\OrderRecharge as OrderRechargeModel;

/**
 * 充值 模块
 */
class Recharge extends AdminController
{
    /**
     * 初始化方法
     * @return void
     * @throws Exception
     */
    public function _initialize()
    {
        parent::_initialize();
        $this->param['payment'] = PaymentTool::instance()->payment_array();
    }

    /**
     * 列表
     * @return mixed
     * @throws Exception
     */
    public function index()
    {
        $where = $this->search('order_sn', '输入需查询的订单号');

        $where['del']    = false;
        $where['status'] = OrderRechargeModel::STATUS_ALREADY_PAY;

        $order = $this->sort_order(OrderRechargeModel::getTableFields(), 'order_time', 'desc');

        $this->export($where, $order);

        $list = OrderRechargeModel::page_list($where, $order);
        if (!$list->isEmpty()) {
            $list->load(['Member']);
        }

        $this->assign($list->toArray());
        return $this->fetch_view();
    }

    /**
     * 数据导出
     * @param $where
     * @param $order
     * @throws Exception
     */
    private function export($where, $order)
    {
        $export = input('export', false);

        if (!$export) {
            $this->assign('export', true);
            return;
        }

        $list = OrderRechargeModel::all_list([], $where, $order);
        if (!$list->isEmpty()) {
            $list->load(['Member']);
            $list->each(
                function ($item) {
                    /** @var OrderRechargeModel $item */
                    $member = $item->getAttr('member');
                    $item->setAttr('payment', $this->param['payment'][$item->getAttr('payment_id')]);
                    $item->setAttr('order_time', date('Y-m-d H:i:s', $item->getAttr('order_time')));
                    $item->setAttr('member_tel', $member['member_tel']);
                    $item->setAttr('member_nickname', $member['member_nickname']);
                }
            );
        }

        $title = [
            'order_sn'        => '订单号',
            'amount'          => '充值金额',
            'payment'         => '充值方式',
            'transaction_sn'  => '支付单号',
            'member_tel'      => '充值账号',
            'member_nickname' => '充值昵称',
            'order_time'      => '充值时间',
        ];

        $this->export_excel('充值订单', $title, $list->toArray());
    }
}
