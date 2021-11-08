<?php

namespace app\api\controller;

use Exception;
use app\common\controller\ApiController;
use app\common\model\OrderRecharge as OrderRechargeModel;

/**
 * 充值 API
 */
class Recharge extends ApiController
{
    /**
     * 充值订单列表接口
     * 分页
     * @return void
     * @throws Exception
     */
    public function order_list()
    {
        $this->check_login();

        $list = OrderRechargeModel::order_list($this->member_id);
        output_success('', $list);
    }

    /**
     * 充值下单接口
     * @return void
     * @throws Exception
     */
    public function order_place()
    {
        $money = $this->get_param('money');
        $this->check_login();

        $money = max(0, $money);
        empty($money) AND output_error('充值金额不能小于0！');

        OrderRechargeModel::order_cancel_batch($this->member_id, OrderRechargeModel::TYPE_RECHARGE);

        $order = OrderRechargeModel::recharge_order($this->member_id, $money);
        empty($order) AND output_error('充值失败');

        output_success('', [
            'order_id' => $order->getAttr('recharge_id'),
            'order_sn' => $order->getAttr('order_sn'),
            'money'    => $order->getAttr('money'),
        ]);
    }
}
