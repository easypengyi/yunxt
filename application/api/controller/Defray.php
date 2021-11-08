<?php

namespace app\api\controller;

use app\common\model\MemberGroupRelation;
use Exception;
use think\Log;
use tool\PaymentTool;
use tool\payment\PaymentOrder;
use app\common\controller\ApiController;

/**
 * 支付 API
 */
class Defray extends ApiController
{
    /**
     * 支付方式列表接口
     * payment_type 1-支付 2-充值
     * @return void
     */
    public function payment_list()
    {
        $payment_type = $this->get_param('payment_type', 1);
        $list['list'] = PaymentTool::instance()->paylist($payment_type);
        unset($list['list'][1]);
        output_success('', $list);
    }

    /**
     * 中心授权支付接口
     * @return void
     */
    public function order_pay()
    {
        $this->pay(PaymentOrder::TYPE_SHOP);
    }

    /**
     * 公益报单支付接口
     * @return void
     */
    public function public_pay()
    {
        $this->pay(PaymentOrder::TYPE_PUBLIC);
    }

    /**
     * 健康大使报单支付接口
     * @return void
     */
    public function healthy_pay()
    {
        $this->pay(PaymentOrder::TYPE_HEALTHY);
    }

    /**
     * 充值支付接口
     * @return void
     */
    public function recharge_pay()
    {
        $this->pay(PaymentOrder::TYPE_RECHARGE);
    }

    /**
     * 会员开通支付接口
     * @return void
     */
    public function activation_pay()
    {
        $this->pay(PaymentOrder::TYPE_ACTIVATION);
    }

    /**
     * 支付调起接口
     * @param      $type
     * @param bool $reset
     * @return void
     */
    private function pay($type, $reset = false)
    {
        $order_id     = $this->get_param('order_id');
        $payment_id   = $this->get_param('payment_id');
        $pay_password = $this->get_param('pay_password', '');
        $openid       = $this->get_param('openid', '');
        $return_url   = $this->get_param('return_url', '');

        $this->check_login();

        $config['apptype']      = $this->app_type;
        $config['return_url']   = $return_url;
        $config['openid']       = $openid;
        $config['pay_password'] = $pay_password;
        $config['product_id']   = $order_id;

        $param = ['order_id' => $order_id, 'member_id' => $this->member_id];

        $result = PaymentTool::instance($config)->pay($payment_id, $type, $param, $reset,$order_id);

        $result['status'] OR output_error($result['msg']);
        output_success($result['msg'], $result['data']);
    }

    /**
     * 支付结果查询
     * @return void
     * @throws Exception
     */
    public function query()
    {
        $payment_sn = $this->get_param('payment_sn');

        $config['apptype'] = $this->app_type;

        $result = PaymentTool::instance($config)->query($payment_sn);

        $result['status'] OR output_error($result['msg']);
        output_success($result['msg'], $result['data']);
    }
}
