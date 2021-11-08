<?php

namespace app\mobile\controller;

use Exception;
use think\Config;
use think\Log;
use think\Session;
use tool\PaymentTool;
use app\common\Constant;
use app\common\controller\MobileController;
use app\common\model\OrderShop as OrderShopModel;

/**
 * 支付处理
 */
class Payment extends MobileController
{
    /**
     * 支付
     * @return mixed
     * @throws Exception
     */
    public function payment()
    {
        $this->login_check();

        $payment_data = Session::get('payment_data');

        $type = $payment_data['type'] ?? '';

        switch ($type) {
            case 'shop':
                $action = 'order_pay';
                break;
            case 'public':
                $action = 'public_pay';
                break;
            case 'healthy':
                $action = 'healthy_pay';
                break;
            case 'recharge':
                $action = 'recharge_pay';
                break;
            case 'activation':
                $action = 'activation_pay';
                break;
            default:
                $this->redirect($this->back_url());
                $action = '';
                break;
        }

        if ($payment_data['is_payment'] ?? false) {
            $this->redirect($this->back_url($type));
        }

        $order_data = $payment_data['data'];

        if ($this->is_ajax) {
            $order_id     = input('order_id', 0);
            $payment_id   = input('payment_id', 0);
            $pay_password = md5(input('password', ''));

            if (empty($order_id) || empty($payment_id)) {
                $this->error('订单id，支付方式不能为空！');
            }

            if (intval($payment_id) === 2 && $this->is_weixin) {
                $this->success('', controller_url('weixin_payment'));
            }

            $back_url = $this->back_url($type, $order_id, true);

            $param  = [
                'order_id'     => $order_id,
                'payment_id'   => $payment_id,
                'pay_password' => $pay_password,
                'openid'       => '',
                'return_url'   => $back_url,
            ];
            $result = $this->api('Defray', $action, $param);

            Session::delete('payment_data');

            switch (intval($payment_id)) {
                case PaymentTool::WXPAY:
                    $this->success('', base64_decode($result['data']['payment']));
                    break;
                case PaymentTool::ALIPAY:
                    $this->success('', base64_decode($result['data']['payment']));
                    break;
                case PaymentTool::BALANCE:
                    sleep(1);
                    $this->success('付款成功', $this->back_url($type));
                    break;
            }
        }

        $result = $this->api('Defray', 'payment_list', ['payment_type' => 1]);
        $this->assign('payment_list', $result['data']['list']);

        $this->assign('type', $type);
        $this->assign('order_data', $order_data);
        $this->assign('return_url', $this->back_url($type));
        $this->assign('title', '支付');
        return $this->fetch();
    }

    /**
     * 微信支付
     * @return mixed
     * @throws Exception
     */
    public function weixin_payment()
    {
        $this->login_check();

        $return_url = controller_url('payment');
        if (!$this->is_weixin) {
            $this->redirect($return_url);
        }

        $payment_data = Session::get('payment_data');

        $type = $payment_data['type'] ?? '';

        switch ($type) {
            case 'shop':
                $action = 'order_pay';
                break;
            case 'public':
                $action = 'public_pay';
                break;
            case 'healthy':
                $action = 'healthy_pay';
                break;
            case 'recharge':
                $action = 'recharge_pay';
                break;
            case 'activation':
                $action = 'activation_pay';
                break;
            default:
                $this->redirect($this->back_url());
                $action = '';
                break;
        }

        if ($payment_data['is_payment'] ?? false) {
            $this->redirect($this->back_url($type));
        }

        $order_data = $payment_data['data'];

        $openid = PaymentTool::instance()->get_openid();

        $param  = [
            'order_id'   => $order_data['order_id'],
            'payment_id' => 2,
            'openid'     => $openid,
            'return_url' => '',
        ];
        $result = $this->api('Defray', $action, $param);

        $this->assign('type', $type);
        $this->assign('order_data', $order_data);
        $this->assign('data_info', $result['data']);
        $this->assign('title', '微信支付');
        $this->assign('return_url', $return_url);
        return $this->fetch();
    }

    /**
     * 支付结束
     * @throws Exception
     */
    public function payment_finish()
    {
        try {
            $payment_data = Session::get('payment_data');

            $type = $payment_data['type'];
        } catch (Exception $e) {
            $type = null;
        }

        Session::delete('payment_data');
        $this->redirect($this->back_url($type));
    }

    /**
     * 支付失败
     * @throws Exception
     */
    public function payment_fail()
    {
        try {
            $payment_data = Session::get('payment_data');

            $type = $payment_data['type'];
        } catch (Exception $e) {
            $type = null;
        }

        Session::delete('payment_data');
        $this->redirect($this->back_url($type));
    }

    /**
     * 返回
     * @param        $type
     * @param string $order_id
     * @param bool   $domain
     * @return string
     */
    private function back_url($type = null, $order_id = '', $domain = false)
    {
        switch ($type) {
            case 'shop':
                return folder_url('User/order', ['pay_order_id' => $order_id], true, $domain);
                break;
            case 'public':
                return folder_url('User/order1', ['pay_order_id' => $order_id], true, $domain);
                break;
            case 'healthy':
                return folder_url('User/index', ['pay_order_id' => $order_id], true, $domain);
                break;
            case 'activation':
                return folder_url('User/leaguer', ['pay_order_id' => $order_id], true, $domain);
                break;
            case 'recharge':
                return folder_url('User/account', ['pay_order_id' => $order_id], true, $domain);
                break;
            default:
                return folder_url('Index/index', [], true, $domain);
                break;
        }
    }
}
