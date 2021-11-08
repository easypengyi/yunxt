<?php

namespace app\mobile\controller;

use app\common\model\ActivityOrderShop;
use app\common\model\Configure as ConfigureModel;
use app\common\model\Member;
use app\common\model\MemberCommission;

use app\common\model\Reword as RewordModel;
use Exception;
use think\Log;
use think\Session;
use helper\ValidateHelper;
use app\common\controller\MobileController;
use app\common\model\OrderShop as OrderShopModel;
use app\common\model\OrdersShop as OrdersShopModel;
use app\common\model\OrderssShop as OrderssShopModel;
use app\common\model\MemberCommission as MemberCommissionModel;
use app\common\model\MemberActivation as MemberActivationModel;


/**
 * 用户
 */
class User extends MobileController
{
    /**
     * 我的
     * @return mixed
     * @throws Exception
     */
    public function index()
    {

        $this->login_check();
        $result = $this->api('Message', 'message_no_read_number');
        $order_no_pay = $this->api('Order', 'order_number');
        $total_income = MemberCommissionModel::total_income($this->member['member_id']);
        $this->assign('total_income', number_format($total_income, 2));
        $commission = RewordModel::where([])->select()->toArray();
        $this->assign('order_status_num',$order_no_pay['data'][0]);
        $this->assign('no_read_number', $result['data']['no_read_number']);
        $this->assign('first_commission', $commission[0]['configure_value']);
        $this->assign('second_commission', $commission[1]['configure_value']);
        $this->assign('three_commission',  $commission[2]['configure_value']);
        $this->assign('title', '我的');
        return $this->fetch();

    }

    /**
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function personal()
    {
        $this->login_check();
        $this->assign('group_id', $this->member['group_id']);
        $this->assign('title', '个人中心');
        return $this->fetch();
    }

    /**
     * 个人信息
     * @return mixed
     * @throws Exception
     */
    public function personal_info()
    {
        $this->login_check();

        if ($this->is_ajax) {
            $member = [
                'account'     => input('account'),
                'blank'      => input('blank'),
                'real_name' => input('real_name', ''),
            ];

            $result = $this->api('Member', 'member_info_change', $member);

            Session::set('member_info_update_time', 0);
            $this->success($result['msg'], controller_url('index'));
        }

        $this->assign('title', '个人信息');
        return $this->fetch();
    }

    /**
     * 账号安全
     * @return mixed
     */
    public function personal_sec()
    {
        $this->login_check();

        $this->assign('title', '账号安全');
        return $this->fetch();
    }

    /**
     * 我的通知
     * @return mixed
     */
    public function message()
    {
        $this->login_check();
        $this->assign('title', '我的通知');
        $this->assign('return_url', $this->http_referer ?: folder_url('Index/index'));
        return $this->fetch();
    }

    /**
     * 收货地址
     * @param bool $choose
     * @param string $return_url
     * @return mixed
     * @throws Exception
     */
    public function address($choose = false, $return_url = '')
    {
        $this->login_check();

        $result = $this->api('Address', 'address_list', ['paginate' => false]);
        $this->assign('data_list', $result['data']['list']);

        empty($return_url) AND $return_url = controller_url('index');

        $this->assign('choose', $choose);
        $this->assign('title', '收货地址');
        $this->assign('return_url', $this->http_referer ?: controller_url('index'));
        return $this->fetch();
    }

    /**
     * 收货地址添加
     * @param bool $choose
     * @return mixed
     * @throws Exception
     */
    public function address_add($choose = false)
    {
        $this->login_check();

        if ($this->is_ajax) {
            $return_url = input('return_url', '');
            $consignee  = input('consignee', '');
            $mobile     = input('mobile', '');
            $address    = input('address', '');
            $district   = input('district', '');

            if (empty($consignee) || empty($mobile) || empty($address) || empty($district)) {
                $this->error('收件人,手机号,详细地址不能为空！');
            }

            $param = [
                'consignee' => $consignee,
                'address'   => $address,
                'mobile'    => $mobile,
                'district'  => $district,
            ];
            $this->api('Address', 'address_add', $param);
            $this->success('地址添加成功！', $return_url);
        }

        $this->assign('choose', $choose);
        $this->assign('edit', false);
        $this->assign('title', '新增收货地址');
        $this->assign('return_url',$this->http_referer ?: controller_url('address', ['choose' => $choose]));
        return $this->fetch('address_edit');
    }
    /**
     * 收货地址编辑
     * @param bool $choose
     * @return mixed
     * @throws Exception
     */
    public function address_edit($choose = false)
    {
        $this->login_check();

        $address_id = input('address_id', 0);

        if ($this->is_ajax) {
            $return_url = input('return_url', '');
            $consignee = input('consignee', '');
            $mobile = input('mobile', '');
            $address = input('address', '');
            $district = input('district', '');

            if (empty($consignee) || empty($mobile) || empty($address) || empty($district)) {
                $this->error('收件人,手机号,详细地址不能为空！');
            }

            $param = [
                'address_id' => $address_id,
                'consignee' => $consignee,
                'address' => $address,
                'mobile' => $mobile,
                'district' => $district,
            ];
            $this->api('Address', 'address_edit', $param);
            $this->success('编辑地址成功！', $return_url);
        }

        $result = $this->api('Address', 'address_detail', ['address_id' => $address_id]);
        $this->assign('data_info', $result['data']);

        $this->assign('choose', $choose);
        $this->assign('edit', true);
        $this->assign('title', '编辑收货地址');
        $this->assign('return_url',  controller_url('address', ['choose' => $choose]));
        return $this->fetch('address_edit');
    }


    /**
     * 我的账户
     * @return mixed
     * @throws Exception
     */
    public function account()
    {
        $this->login_check();

        $pay_order_id = input('pay_order_id', '');
        if (!empty($pay_order_id)) {
            $this->clear_update_member_time();
            $this->assign('order_payment', true);
        }

        $result = $this->api('Member', 'balance_info');
        $this->assign('info', $result['data']);

        $this->assign('title', '我的账户');
        $this->assign('return_url', controller_url('index'));
        return $this->fetch();
    }



    /**
     * 分享邀请
     * @return mixed
     */
    public function invitation()
    {
        $this->login_check();

        $this->assign('title', '分享邀请');
        $this->assign('return_url', controller_url('index'));
        return $this->fetch();
    }

    /**
     * 中心授权订单
     * @return mixed
     */
    public function order()
    {
        $this->login_check();

        $pay_order_id = input('pay_order_id', '');
        if (!empty($pay_order_id)) {
            $this->assign('order_payment', true);
        }

        $this->assign('category', input('category', 0));
        $this->assign('status_array', OrderShopModel::order_status_array());
        $this->assign('title', '系统订单');
        $this->assign('return_url', controller_url('index'));
        return $this->fetch();
    }

    /**
     * 产品订单
     * @return mixed
     */
    public function order1()
    {
        $this->login_check();

        $pay_order_id = input('pay_order_id', '');
        if (!empty($pay_order_id)) {
            $this->assign('order_payment', true);
        }
        $this->assign('category', input('category', 0));
        $this->assign('status_array', OrdersShopModel::order_status_array());
        $this->assign('title', '我的订单');
        $this->assign('return_url', controller_url('index'));
        return $this->fetch();
    }




    /**
     * 我的订单-继续支付
     */
    public function continue_pay()
    {
        $this->login_check();

        $data = [
            'order_id' => input('order_id', 0),
            'money' => input('amount', ''),
            'order_sn' => input('order_sn', ''),
        ];

        Session::set('payment_data', ['type' => 'shop', 'data' => $data]);
        $this->redirect(folder_url('Payment/payment'));
    }


    public function continue_pay1()
    {
        $this->login_check();

        $data = [
            'order_id' => input('order_id', 0),
            'money' => input('amount', ''),
            'order_sn' => input('order_sn', ''),
        ];

        Session::set('payment_data', ['type' => 'public', 'data' => $data]);
        $this->redirect(folder_url('Payment/payment'));
    }


    /**
     * 我的奖金
     * @return mixed
     * @throws Exception
     */
    public function amount()
    {
        $this->login_check();
        $type =  input('type', '11');
        $result = $this->api('Message', 'message_list', ['type' => $type]);
        $this->assign('message_list', $result['data']['list']);
        $this->assign('type', $type);
        $this->assign('title', '奖金明细');
        $this->assign('return_url', controller_url('reward'));
        return $this->fetch();
    }


    /**
     * 奖金类别
     * @return mixed
     * @throws Exception
     */
    public function reward()
    {
        $this->login_check();
        $res = MemberCommission::total_type_income($this->member['member_id']);
        $this->assign('res', $res[0]);
        $this->assign('title', '奖金类型');
        $this->assign('return_url', controller_url('index'));
        return $this->fetch();
    }

    /**
     * 云库存
     * @return mixed
     * @throws Exception
     */
    public function amount1()
    {
        $this->login_check();
        $this->assign('title', '库存明细');
        $this->assign('return_url', controller_url('index'));
        return $this->fetch();
    }

    /**
     * 奖金池
     * @return mixed
     * @throws Exception
     */
    public function amount2()
    {
        $this->login_check();
        $this->assign('title', '奖金池明细');
        $this->assign('return_url', controller_url('index'));
        return $this->fetch();
    }


    /**
     * 奖金提现
     * @return mixed
     * @throws Exception
     */
    public function amount_withdraw()
    {
        $this->login_check();

        if ($this->is_ajax) {
            $param = [
                'money' => input('money', ''),
                'account' => input('account', ''),
                'blank' => input('blank', ''),
                'real_name' => input('real_name', ''),
                'mobile' => input('mobile'),
            ];
            $result = $this->api('Receivable', 'withdrawals', $param);

            Session::set('member_info_update_time', 0);
            $this->success($result['msg'], controller_url('amount'));
        }

        $this->assign('title', '奖金提现');
        $this->assign('service_money', number_format(ConfigureModel::getValue('withdrawal_service_ratio'),1));
        $this->assign('return_url', controller_url('amount'));
        return $this->fetch();
    }






    /**
     * 我的团队
     * @return mixed
     */
    public function team()
    {
        $this->login_check();
        $this->assign('team_num', 0);
        $this->assign('title', '我的团队');
        $this->assign('return_url', controller_url('index'));
        return $this->fetch();
    }

    /**
     * 分销商下线
     * @return mixed
     * @throws Exception
     */
    public function agent_below()
    {
        $this->login_check();

        $invitation_id = input('invitation_id', 0);
        $result = $this->api('Member', 'user_info', ['user_id' => $invitation_id]);
        $team_num = $this->api('Member', 'teamNum', ['mid' => $invitation_id]);
        $this->assign('team_num', $team_num);
        $this->assign('agent', $result['data']['member']);
        $this->assign('title', '我的团队');
        $this->assign('return_url', controller_url('team'));
        return $this->fetch();
    }



    /**
     * 云库存赠送
     * @return mixed
     * @throws Exception
     */
    public function amount_give()
    {
        $this->login_check();
        if ($this->is_ajax) {
            $param = [
                'balance' => input('balance', ''),
                'mobile' => input('mobile', ''),
            ];
            $result = $this->api('Receivable', 'amount_give', $param);

            Session::set('member_info_update_time', 0);
            $this->success($result['msg'], controller_url('amount1'));
        }

        $this->assign('title', '报单币转换');
        $this->assign('return_url', controller_url('amount1'));
        return $this->fetch();
    }


    /**
     * @return mixed
     * @throws Exception
     */
    public function order2_submit()
    {
        $this->login_check();

        $order_key = Session::get('order_key');


        if (empty($order_key)) {
            $this->redirect($this->http_referer);
        }

        if ($this->is_ajax) {
            $product_num = input('product_num', '');
            $address_id   = input('address_id');
            $param = [
                'order_key' => $order_key,
                'product_num'=>$product_num,
                'address_id' =>$address_id,
            ];

           $this->api('Order', 'single2_order_place', $param);

            Session::delete('order_key');

            $this->success('', folder_url('user/order1'));
        }
        $address = $this->api('Address', 'default_address', ['paginate' => false]);
        $result = $this->api('Order', 'single2_settlement_info', ['order_key' => $order_key]);
        $this->assign('data_list', $result['data']);
        $this->assign('address_list', $address);
        $this->assign('return_url', folder_url('User/index'));
        $this->assign('title', '提交订单');
        return $this->fetch();
    }



    public function investment(){
        $this->assign('return_url', folder_url('User/index'));
        $this->assign('title', '团队招商');
        return $this->fetch();
    }





}