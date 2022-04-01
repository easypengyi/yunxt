<?php

namespace app\api\controller;

use app\common\model\Member as MemberModel;
use app\common\model\MemberGroupRelation;
use app\common\model\OrderShop as OrderShopModel;
use app\common\model\OrdersShop as OrdersShopModel;
use app\common\model\Product;
use Exception;
use think\Db;
use think\Log;
use tool\AliyunSms;
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
//        unset($list['list'][1]);
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
        if($payment_id == 4){ //直接支付测试
            $result = $this->balancePay($payment_id, $param);
        }else{
            $result = PaymentTool::instance($config)->pay($payment_id, $type, $param, $reset,$order_id);
        }

        $result['status'] OR output_error($result['msg']);
        output_success($result['msg'], $result['data']);
    }

    /**
     * @return bool
     * @throws \think\Exception
     * @throws \think\exception\DbException
     */
    public function balancePay($payment_id, $param){
        $data['payment_time']   = time();
        $data['payment_id']     = $payment_id;
        $data['status']         = OrdersShopModel::STATUS_ALREADY_PAY;

        $order_id = $param['order_id'];
        $member_id = $param['member_id'];

        try {
            Db::startTrans();
            OrdersShopModel::order_status($order_id, $data);

            $order =  OrdersShopModel::get($order_id);

            $group  = MemberGroupRelation::get_top($member_id);
            if ($group){
                $member_name = MemberModel::user_info($member_id)['member_realname'];
                $top = MemberGroupRelation::get_top($group['top_id']);
                if ($top){
    //                        if ($top['group_id'] != MemberGroupRelationModel::seven){
    //                            $this->newReward($group['top_id'],$order['unit_price'],$order['product_num'],$member_name,$group['group_id']);
                    OrdersShopModel::newReward($group, $top, $order['unit_price'], $order['product_num'],$member_name, $order_id, $member_id);
    //                        }
                }
            }
            $param = [
                'payment_id'  => $payment_id,
            ];
            $result = [
                'status' => true,
                'msg'    => '',
                'data'   => $param,
            ];
            //发送提醒
            $member = MemberModel::get($member_id);
            if(!empty($member['member_tel'])){
                $sms       = AliyunSms::instance();
                $option = ['order'=> $order['order_sn']];
                $sms->send_message($member['member_tel'], $option, 'order', $member_id);
            }
            Db::commit();
        }catch (Exception $e) {
            Db::rollback();
            $result = [
                'status' => false,
                'msg'    => $e->getMessage(),
                'data'   => null,
            ];
        }

        return $result;
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
