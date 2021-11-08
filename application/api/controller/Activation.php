<?php

namespace app\api\controller;

use think\Db;
use Exception;
use helper\ValidateHelper;
use app\common\controller\ApiController;
use app\common\model\Member as MemberModel;
use app\common\model\MemberAddress as MemberAddressModel;
use app\common\model\MemberActivation as MemberActivationModel;

/**
 * 会员开通 API
 */
class Activation extends ApiController
{
    /**
     * 开通会员接口
     * @throws Exception
     */
    public function member_activation()
    {
        $money      = $this->get_param('money');
        $address_id = $this->get_param('address_id');
        $invoice    = $this->get_param('invoice');
        $email      = $this->get_param('email', '');

        $this->check_login();

        $money = max(0, $money);
        empty($money) AND output_error('金额不能小于0！');

        $invoice = json_decode($invoice, true);

        if (!empty($email)) {
            ValidateHelper::is_email($email) OR output_error('email格式错误！');
        }

        $result = MemberModel::check_activation($this->member_id);
        $result AND output_error('会员已激活！');

        $address = MemberAddressModel::address_detail($this->member_id, $address_id);
        empty($address) AND output_error('地址不存在！');
        $address = $address->address_info();

        MemberActivationModel::order_cancel_batch($this->member_id);

        $order = MemberActivationModel::order_place($this->member_id, $money, $address, $invoice, $email);
        empty($order) AND output_error('开通失败');

        MemberModel::change_email($this->member_id, $email);

        $data = [
            'order_id' => $order->getAttr('order_id'),
            'money'    => $order->getAttr('amount'),
            'order_sn' => $order->getAttr('order_sn'),
        ];

        output_success('', $data);
    }

    /**
     * 会员订单列表接口
     * @throws Exception
     */
    public function order_list()
    {
        $this->check_login();

        $list = MemberActivationModel::order_list($this->member_id);

        output_success('', $list);
    }

    /**
     * 绑定唾液盒编号
     * @throws Exception
     */
    public function bind_activation()
    {
        $code = $this->get_param('code');

        $this->check_login();

        empty($code) AND output_error('编号不为空！');

        $order = MemberActivationModel::order_distribution_info($this->member_id);
        empty($order) AND output_error('唾液盒编号未激活！');

        $order->getAttr('box_code') == $code OR output_error('编号不可用！');

        try {
            Db::startTrans();
            $result = MemberModel::change_code($this->member_id, $code);
            empty($result) AND output_error('绑定失败！');
            $order->setAttr('status', MemberActivationModel::STATUS_FINISH);
            $order->save();
            Db::commit();
        } catch (Exception $e) {
            Db::rollback();
            throw $e;
        }

        output_success();
    }
}
