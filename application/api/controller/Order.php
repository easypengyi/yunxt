<?php

namespace app\api\controller;

use app\common\model\Member;
use app\common\model\Member as MemberModel;
use app\common\model\MemberAddress as MemberAddressModel;
use app\common\model\MemberBalance;
use app\common\model\MemberCommission;
use app\common\model\MemberGroupRelation;
use app\common\model\OrderShop;
use helper\StrHelper;
use think\Db;
use Exception;
use think\Cache;
use think\Config;
use helper\TimeHelper;
use app\common\controller\ApiController;
use app\common\model\Product as ProductModel;
use app\common\model\OrderShop as OrderShopModel;
use app\common\model\OrdersShop as OrdersShopModel;
use app\common\model\OrderssShop as OrderssShopModel;
use app\common\model\ActivityOrderShop as ActivityOrderShopModel;
use app\common\model\MemberCoupon as MemberCouponModel;
use app\common\model\ProductEvaluate as ProductEvaluateModel;
use app\common\model\OrderShopRefund as OrderShopRefundModel;
use think\Log;
use tool\HashidsTool;

/**
 * 商城订单 API
 */
class Order extends ApiController
{
    /**
     * 商城订单列表接口
     * 分页
     * @return void
     * @throws Exception
     */
    public function order_list()
    {
        $category = $this->get_param('category', OrderShopModel::CATEGORY_ALL);
        $this->check_login();

        $list = OrderShopModel::client_list($this->member_id, $category);
        output_success('', $list);
    }
    /**
     * 商城订单列表接口
     * 分页
     * @return void
     * @throws Exception
     */
    public function order1_list()
    {
        $category = $this->get_param('category', OrdersShopModel::CATEGORY_ALL);
        $this->check_login();

        $list = OrdersShopModel::client_list($this->member_id, $category);
        output_success('', $list);
    }

    /**
     * 商城订单列表接口
     * 分页
     * @return void
     * @throws Exception
     */
    public function order2_list()
    {
        $category = $this->get_param('category', OrderssShopModel::CATEGORY_ALL);
        $this->check_login();

        $list = OrderssShopModel::client_list($this->member_id, $category);
        output_success('', $list);
    }

    /**
     * 核销订单列表接口
     * 分页
     * @return void
     * @throws Exception
     */
    public function order3_list()
    {
        $this->check_login();
        $list = OrderssShopModel::order3_list($this->member_id);
        output_success('', $list);
    }

    /**
     * HPV订单列表
     * @throws \think\exception\DbException
     */
    public function order4_list()
    {
        $category = $this->get_param('category', OrderShopModel::CATEGORY_ALL);
        $this->check_login();

        $list = ActivityOrderShopModel::client_list($this->member_id, $category);
        output_success('', $list);
    }


    /**
     * 商城订单详情接口
     * @return void
     * @throws Exception
     */
    public function order_detail()
    {
        $order_id = $this->get_param('order_id');
        $this->check_login();

        $detail = OrderShopModel::detail($order_id, $this->member_id);
        empty($detail) AND output_error('订单已删除！');
        output_success('', $detail);
    }


    /**
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function order2_detail()
    {
        $order_id = $this->get_param('order_id');
        $this->check_login();
        $where['order_id'] = $order_id;
        $where['hide'] = false;
        $where['del']  = false;
        $where['status'] = OrderssShopModel::STATUS_HEXIAO;
        $detail = OrderssShopModel::where($where)->find();
        empty($detail) AND output_error('订单已删除！');
        output_success('', $detail);
    }

    /**
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function write_off()
    {
        $this->check_login();

        $order_id = $this->get_param('order_id');
        $name = $this->get_param('name');
        $blood_number = $this->get_param('blood_number');
        empty($blood_number) AND output_error('血液编号不能为空！');

        $group_id   = MemberGroupRelation::where(['member_id'=>$this->member_id])->find()['group_id'];

        $order = OrderssShopModel::where(['order_id'=>$order_id,'status'=>OrderssShopModel::STATUS_HEXIAO])->find();
        empty($order) AND output_error('订单已核销！');

        if ($group_id != MemberGroupRelation::six){
            output_error('无核销资格！');
        }else{
            try {
                Db::startTrans();
                $where['order_id'] = $order_id;
                $data['status']  = OrderssShopModel::STATUS_CHECKING;
                $data['mechanism_name']  = $name;
                $data['mechanism_id']  = $this->member_id;
                $data['blood_number']  = $blood_number;
                OrderssShopModel::where($where)->update($data);
                $commission = StrHelper::ceil_decimal((500), 2);//抽血佣金
                Member::commission_inc($order['user_id'], $commission);
                MemberCommission::insert_log($order['user_id'], MemberCommission::write_off, 0, $commission, '来自'.$name.'的核销奖金', 0);
                Db::commit();
            } catch (Exception $e) {
                Db::rollback();
                throw $e;
            }
        }
        output_success('',[]);
    }

    /**
     * 商城订单取消接口
     * @return void
     * @throws Exception
     */
    public function order_cancel()
    {
        $order_id = $this->get_param('order_id');
        $this->check_login();

        //如果订单已经付款但还没发货，则先退款 by shiqiren
        $where['order_id']      = $order_id;
        $where['del']           = false;
        $where['status'] = ['in', [OrderShopModel::STATUS_WAIT_PAY, OrderShopModel::STATUS_ALREADY_PAY]];
        $data_info = OrdersShopModel::get($where);
        empty($data_info) AND output_success('数据不存在！');


        //如果订单已经支付，则还需要退款
        if($data_info->getAttr('status')==OrderShopModel::STATUS_ALREADY_PAY){
            //更新退款申请状态为“完成审批”
            $data2   = [
                'refund_status' => OrdersShopModel::REFUND_STATUS_SUCCESS
            ];
            $data_info->save($data2);
            $data_info->order_refund(true);//发起退款
        }
        $data_info->order_cancel();
        // $result = OrderShopModel::order_cancel_batch($this->member_id, $order_id, true);
        // $result OR output_error('订单取消失败！');
        output_success('订单取消成功！');
    }

    /**
     * 公益订单收货接口
     * @throws \think\Exception
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    public function distribution_examine()
    {
        $this->check_login();
        $where['order_id']      =  $address_id = $this->get_param('order_id');
        $where['del']           = false;
        $where['status']        = OrdersShopModel::STATUS_ALREADY_DISTRIBUTION;
        $where['refund_status'] = ['not in', [OrdersShopModel::REFUND_STATUS_APPLY]];



        $data_info = OrdersShopModel::get($where);
        empty($data_info) AND output_error('数据不存在！');
        $data   = [
            'refund_status' => OrdersShopModel::REFUND_STATUS_NO,
            'status'        => OrdersShopModel::STATUS_FINISH,
            'finish_time'   => time()
        ];
        $result = $data_info->save($data);
        $result OR output_error('收货失败！');

        output_success('收货完成！');
    }


    /**
     * 商城订单退款申请接口
     * @return void
     * @throws Exception
     */
    public function order_refund()
    {
        $order_id = intval($this->get_param('order_id'));
        $content  = $this->get_param('content');
        $image_id = $this->get_param('image_id', '');
        $this->check_login();

        $order = OrderShopModel::refund_order_info($order_id, $this->member_id);
        empty($order) AND output_error('订单不满足退款申请条件！');

        $result = $order->order_refund_apply();
        $result OR output_error('订单不满足退款申请条件！');

        OrderShopRefundModel::insert_refund($order->getAttr('order_id'), $content, explode(',', $image_id));
        output_success();
    }

    /**
     * 订单隐藏接口
     * 订单删除
     * @return void
     * @throws Exception
     */
    public function order_hide()
    {
        $order_id = $this->get_param('order_id');
        $this->check_login();

        $result = OrderShopModel::order_hide($order_id, $this->member_id);
        $result OR output_error('订单已删除！');
        output_success();
    }

    /**
     * 订单评价接口
     * data=[{"product_id":1,"content":"内容","image":"1,2,3,4","score":1}]
     * @return void
     * @throws Exception
     */
    public function order_evaluate()
    {
        $data      = $this->get_param('data');
        $anonymity = boolval($this->get_param('anonymity', 0));
        $order_id  = $this->get_param('order_id');
        $this->check_login();

        try {
            $data = json_decode($data, true);
        } catch (Exception $e) {
            $data = [];
        }

        empty($data) AND output_error('请填写评论数据！');

        $order = OrderShopModel::evaluate_order_info($order_id, $this->member_id);
        empty($order) AND output_error('订单已评价或未满足评价条件！');

        $refund_status = $order->getAttr('refund_status');
        if (!in_array($refund_status, [OrderShopModel::REFUND_STATUS_NO, OrderShopModel::REFUND_STATUS_FAIL])) {
            output_error('已申请退款！');
        }

        try {
            Db::startTrans();
            foreach ($data as $v) {
                if (isset($v['product_id']) && isset($v['content']) && isset($v['score'])) {
                    ProductEvaluateModel::insert_evaluate(
                        $this->member_id,
                        $order_id,
                        $v['product_id'],
                        $v['content'],
                        explode(',', $v['image']),
                        $v['score'],
                        $anonymity
                    );
                }
            }

            $result = $order->save(['status' => OrderShopModel::STATUS_EVALUATE]);
            $result OR output_error('评论失败');
            Db::commit();
        } catch (Exception $e) {
            Db::rollback();
            throw $e;
        }

        output_success('评价成功！');
    }

    /**
     * 订单数量接口
     * @return void
     * @throws Exception
     */
    public function order_number()
    {
        $query =
            "SELECT
            COUNT(status = ".OrdersShopModel::STATUS_WAIT_PAY." or null) as dfk,
            COUNT(status = ".OrdersShopModel::STATUS_ALREADY_PAY." or null) as dfh,
            COUNT(status = ".OrdersShopModel::STATUS_ALREADY_DISTRIBUTION." or null) as dsh
            FROM ydn_orders_shop WHERE member_id = $this->member_id";

           output_success('',  OrdersShopModel::query($query));

    }

    /**
     * 订单是否支付验证接口
     * @return void
     * @throws Exception
     */
    public function order_payment_check()
    {
        $order_id = $this->get_param('order_id');
        $this->check_login();

        $check = OrderShopModel::order_payment_check($order_id, $this->member_id);
        output_success('', ['payment' => $check]);
    }

    //-------------------------------------------------- 单商城

    /**
     * 中心授权结算接口
     * 中心授权下单接口的前置接口
     * @return void
     * @throws Exception
     */
    public function single_settlement()

    {
        $this->check_login();


        $two_mobile = $this->get_param('two_mobile');
        $two_name = $this->get_param('two_name');

        $nick_name = $this->get_param('nick_name');
        $mobile = $this->get_param('mobile');
        $id_code = $this->get_param('id_code');


        $trigger  = $this->get_param('trigger');
        empty($trigger) AND output_error('请填写经销级别！');

        $trigger1  = $this->get_param('trigger1');
        empty($trigger1) AND output_error('请填写结算方式！');

        switch ($trigger){
            case '云系统创客':
             $product_id = 1;
             break;
            case '代理人':
                $product_id = 2;
                break;
            case '执行董事':
                $product_id = 3;
                break;
            case '全球合伙人':
                $product_id = 4;
                break;
            case '联合创始人':
                $product_id = 5;
                break;
            default:
                $product_id = 1;
        }
        $group_id = MemberGroupRelation::get_group_id($this->member_id);

        switch ($trigger1){
            case '云库存结算':
                $is_admin = 1;
                if ($group_id <= $product_id){
                    output_error('级别不足，请选择平台结算方式！');
                }
                break;
            case '平台结算':
                $is_admin = 0;
                break;
            default:
                $is_admin = 0;
        }

        empty($nick_name) AND output_error('请填写用户名称！');
        empty($mobile) AND output_error('请填写用户手机号码！');
        empty($id_code) AND output_error('请填写用户身份证号码！');


        if ($two_mobile){
            $two_id =  MemberModel::get(['member_tel'=>$two_mobile, 'del'=>0])['member_id'];
            if(!empty($two_id)){
                //所有接點必須是自己團隊以下,不可以平衡放或放置其他線
                $member_ids =  MemberGroupRelation::where(['all_path'=>array('like', '%,'.$this->member_id.',%')])
                    ->column('member_id');
                if(count($member_ids)  > 0 ){
                    $res_member = [];
                    foreach ($member_ids as $val){
                        $res_member[$val] = $val;
                    }
                    if(!isset($res_member[$two_id])){
                        output_error('该接点人，不在团队内！');
                    }
                }else{
                    output_error('该接点人，不在团队内！');
                }
            }
            (empty($two_id) AND Config::get('custom.invitation_code_check')) AND output_error('接点人手机号不存在！');
        }else{
            $two_id = 0;
        }


        $result = MemberModel::check_phone($mobile);
        $result AND output_error('该用户手机号码已经存在！');


        $product_list = ProductModel::order_product_list($product_id);

        if (!$product_list){
            output_error('商品不存在！');
        }

        Config::get('store.order_continue_pay') OR OrderShopModel::order_cancel_batch($this->member_id);

        //订单总价
        $total_money = '0.00';
        //商品列表
        $product_id = [];
        //订单列表
        $order_list = [];

        foreach ($product_list as $v) {

            $order['product_id']          = $v['product_id'];
            $order['product_name']        = $v['name'];
            $order['product_num']         = $v['number'];
            $order['product_image']       = $v['image'];
            $order['original_unit_price'] = $v['original_price'];
            $order['unit_price']          = $v['current_price'];
            $order['money']               = $v['current_price'];
            $order['top_id']              = $this->member_id;
            $order['two_id']              = $two_id;
            $order['two_name']            = $two_name;
            $order['nick_name']           = $nick_name;
            $order['mobile']              = $mobile;
            $order['uid']                 = $id_code;
            $order['is_admin']            = $is_admin;

            $total_money  = bcadd($total_money, $order['money'], 2);
            $order_list[] = $order;
            $product_id[] = $order['product_id'];
        }

        $list['order']       = $order_list;
        $list['product_id']  = $product_id;
        $list['total_money'] = $total_money;

        $order_key = OrderShopModel::order_cache_key($this->member_id, $this->app_type);
        Cache::tag('order')->set($order_key, $list, TimeHelper::daysToSecond());
        $list['order_key'] = $order_key;
        output_success('', $list);
    }

    /**
     * 中心授权结算信息接口
     * @return void
     */
    public function single_settlement_info()
    {
        $order_key = $this->get_param('order_key');

        $result = OrderShopModel::order_cache_key_check($order_key, $this->member_id, $this->app_type);
        $result OR output_error('数据错误！');

        $info = Cache::get($order_key);
        empty($info) AND output_error('请勿重复提交订单！');

        $info['order_key'] = $order_key;
        output_success('', $info);
    }

    /**
     * 中心授权下单接口
     * other 格式 [{"id":1,"coupon_id":1,"remark":"备注内容"}] id 订单列表顺序key coupon_id 优惠券ID
     * @return void
     * @throws Exception
     */
    public function single_order_place()
    {
        $order_key  = $this->get_param('order_key');

        $this->check_login();

        $result = OrderShopModel::order_cache_key_check($order_key, $this->member_id, $this->app_type);
        $result OR output_error('数据错误！');

        $info = Cache::get($order_key);
        empty($info) AND output_error('请勿重复提交订单！');

        try {
            $order_list   = [];
            $product_list = [];
            $total_money  = 0;
            $product_num = 0;
            $is_admin = 0;
            $nick_name = '';
             Db::startTrans();
            foreach ($info['order'] as $k => $v) {
                $v['product_image_id'] = $v['product_image']['file_id'];

                // 去除多余变量
                unset($v['product_image']);
                $data = $v;
                $data['member_id'] = $this->member_id;
                $data['amount']     = $v['money'];
                $is_admin = $v['is_admin'];
                $order_list[]   = $data;
                $product_list[] = $v['product_id'];
                $total_money    = bcadd($total_money, $data['money'], 2);
                $product_num = $v['product_num'];
                $nick_name = $v['nick_name'];
            }
            $list = OrderShopModel::order_place_batch($order_list);

            if ($is_admin == 1){
                if ($product_num < 1){
                    $message = '产品数量错误';
                    output_error($message);
                }
                $result = MemberModel::balance_dec($this->member_id, $product_num);
                MemberBalance::insert_log($this->member_id,MemberBalance::SHOP,$product_num,'来自'.$nick_name.'的云库存报单',0);
                if (!$result) {
                    $message = '云库存不足！';
                    output_error($message);
                }

            }


            Db::commit();
        } catch (Exception $e) {
            Db::rollback();
            throw $e;
        }


        Cache::rm($order_key);

        output_success('', $list);
    }




    /**
     * @return void
     * @throws Exception
     */
    public function single1_settlement()
    {
        $this->check_login();

        $product = $this->get_param('product_id','');
        $product_num = $this->get_param('product_num',1);

        $product_list = ProductModel::order_product_list($product);

        if (!$product_list){
            output_error('商品不存在！');
        }

        Config::get('store.order_continue_pay') OR OrderShopModel::order_cancel_batch($this->member_id);

        //订单总价
        $total_money = '0.00';
        //商品列表
        $product_id = [];
        //订单列表
        $order_list = [];

        $group = MemberGroupRelation::get_top($this->member_id);

        foreach ($product_list as $v) {

            if ($product == ProductModel::PRODUCT_ID){
                switch ($group['group_id']){
                    case MemberGroupRelation::first:
                        $v['current_price'] = MemberGroupRelation::five_price;
                        break;
                    case MemberGroupRelation::second:
                        $v['current_price'] = MemberGroupRelation::four_price;
                        break;
                    case MemberGroupRelation::three:
                        $v['current_price'] = MemberGroupRelation::three_price;
                        break;
                    case MemberGroupRelation::four:
                        $v['current_price'] = MemberGroupRelation::two_price;
                        break;
                }
            }

            $order['money']  =  $v['current_price']*$product_num;
            $order['product_id']          = $v['product_id'];
            $order['product_name']        = $v['name'];
            $order['product_image']       = $v['image'];
            $order['original_unit_price'] = $v['original_price'];
            $order['unit_price']          = $v['current_price'];
            $order['product_num']         = $product_num;

            $total_money  = bcadd($total_money, $order['money'], 2);
            $order_list[] = $order;
            $product_id[] = $order['product_id'];
        }

        $list['order']       = $order_list;
        $list['product_id']  = $product_id;
        $list['total_money'] = $total_money;

        $order_key = OrdersShopModel::order_cache_key($this->member_id, $this->app_type);
        Cache::tag('order')->set($order_key, $list, TimeHelper::daysToSecond());
        $list['order_key'] = $order_key;
        output_success('', $list);
    }

    /**
     * @return void
     */
    public function single1_settlement_info()
    {
        $order_key = $this->get_param('order_key');

        $result = OrdersShopModel::order_cache_key_check($order_key, $this->member_id, $this->app_type);
        $result OR output_error('数据错误！');

        $info = Cache::get($order_key);
        empty($info) AND output_error('请勿重复提交订单！');

        $info['order_key'] = $order_key;
        output_success('', $info);
    }

    /**
     * other 格式 [{"id":1,"coupon_id":1,"remark":"备注内容"}] id 订单列表顺序key coupon_id 优惠券ID
     * @return void
     * @throws Exception
     */
    public function single1_order_place()
    {
        $order_key  = $this->get_param('order_key');
        $address_id =  $this->get_param('address_id','');
        !$address_id AND output_error('请选择收货地址！');

        $address = MemberAddressModel::address_detail($this->member_id, $address_id);
        !$address AND output_error('地址不存在！');

        $address = $address->address_info();

        $this->check_login();

        $result = OrdersShopModel::order_cache_key_check($order_key, $this->member_id, $this->app_type);
        $result OR output_error('数据错误！');

        $info = Cache::get($order_key);
        empty($info) AND output_error('请勿重复提交订单！');

        try {
            $order_list   = [];
            $product_list = [];
            $total_money  = 0;
            Db::startTrans();
            foreach ($info['order'] as $k => $v) {
                $result = ProductModel::frozen_stock($v['product_id'], 1);
                $result OR output_error('商品库存不足！');
                $v['product_image_id'] = $v['product_image']['file_id'];
                // 去除多余变量
                unset($v['product_image']);
                $data = $v;
                $data['member_id'] = $this->member_id;
                $data['amount']   = $v['money'];
                $data['address']  = $address;
                $data['status']  = OrdersShopModel::STATUS_WAIT_PAY;
                $order_list[]   = $data;
                $product_list[] = $v['product_id'];
                $total_money    = bcadd($total_money, $data['money'], 2);
            }
            $list = OrdersShopModel::order_place_batch($order_list);
            Db::commit();
        } catch (Exception $e) {
            Db::rollback();
            throw $e;
        }

        Cache::rm($order_key);

        output_success('', $list);
    }


    /**
     * 我要发货前置接口
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function single2_settlement()
    {
        $this->check_login();

        $product_id = $this->get_param('product_id');


        $product_list = ProductModel::order_product_list($product_id);


        if (!$product_list){
            output_error('商品不存在！');
        }

        Config::get('store.order_continue_pay') OR OrderShopModel::order_cancel_batch($this->member_id);

        //订单总价
        $total_money = '0.00';
        //商品列表
        $product_id = [];
        //订单列表
        $order_list = [];

        foreach ($product_list as $v) {
            $order['money']  = $v['current_price'];
            $order['product_id']          = $v['product_id'];
            $order['product_name']        = $v['name'];
            $order['product_image']       = $v['image'];
            $order['original_unit_price'] = $v['original_price'];
            $order['unit_price']          = $v['current_price'];
            $total_money  = bcadd($total_money, $order['money'], 2);
            $order_list[] = $order;
            $product_id[] = $order['product_id'];
        }
        $list['order']       = $order_list;
        $list['product_id']  = $product_id;
        $list['total_money'] = $total_money;

        $order_key = OrdersShopModel::order_cache_key($this->member_id, $this->app_type);
        Cache::tag('order')->set($order_key, $list, TimeHelper::daysToSecond());
        $list['order_key'] = $order_key;
        output_success('', $list);
    }

    public function single2_settlement_info()
    {
        $order_key = $this->get_param('order_key');

        $result = OrdersShopModel::order_cache_key_check($order_key, $this->member_id, $this->app_type);
        $result OR output_error('数据错误！');

        $info = Cache::get($order_key);
        empty($info) AND output_error('请勿重复提交订单！');

        $info['order_key'] = $order_key;
        output_success('', $info);
    }

    /**
     * other 格式 [{"id":1,"coupon_id":1,"remark":"备注内容"}] id 订单列表顺序key coupon_id 优惠券ID
     * @return void
     * @throws Exception
     */
    public function single2_order_place()
    {
        $this->check_login();

        $order_key  = $this->get_param('order_key');
        $address_id =  $this->get_param('address_id','');
        $product_num =  $this->get_param('product_num',1);
        !$address_id AND output_error('请选择收货地址！');

        $address = MemberAddressModel::address_detail($this->member_id, $address_id);
        !$address AND output_error('地址不存在！');

        $address = $address->address_info();


        $result = OrdersShopModel::order_cache_key_check($order_key, $this->member_id, $this->app_type);
        $result OR output_error('数据错误！');

        $info = Cache::get($order_key);
        empty($info) AND output_error('请勿重复提交订单！');

        $result = MemberModel::balance_dec($this->member_id, $product_num);

        if (!$result) {
            $message = '云库存不足！';
            output_error($message);
        }
        MemberBalance::insert_log($this->member_id,MemberBalance::SHOP,$product_num,'来自'.$address['consignee'].'的库存发货',0);

        try {
            $order_list   = [];
            $product_list = [];
            $total_money  = 0;
            Db::startTrans();
            foreach ($info['order'] as $k => $v) {
                $v['product_image_id'] = $v['product_image']['file_id'];
                // 去除多余变量
                unset($v['product_image']);
                $data = $v;
                $data['address']  = $address;
                $data['member_id'] = $this->member_id;
                $data['amount']   = $v['money']*$product_num;
                $data['product_num']   = $product_num;
                $data['order_type'] = 2;
                $data['status'] = OrdersShopModel::STATUS_ALREADY_PAY;
                $order_list[]   = $data;
                $product_list[] = $v['product_id'];
                $total_money    = bcadd($total_money, $data['money'], 2);
            }
            $list = OrdersShopModel::order_place_batch($order_list);
            Db::commit();
        } catch (Exception $e) {
            Db::rollback();
            throw $e;
        }

        Cache::rm($order_key);

        output_success('', $list);
    }





    /**
     * 订单支付信息接口
     * @return void
     * @throws Exception
     */
    public function order_payment_info()
    {
        $order_id = $this->get_param('order_id');
        $this->check_login();

        $order = OrderShopModel::pay_list($order_id, $this->member_id);
        empty($order) AND output_error('订单已经支付！');

        $data = [
            'order_id' => [],
            'order_sn' => [],
            'money'    => '0.00',
            'no_pay'   => false,
        ];

        foreach ($order as $o) {
            $amount = $o->getAttr('amount');
            if (empty($amount)) {
                $o->order_pay_finish(0, '');
            } else {
                $data['order_id'][] = $o->getAttr('order_id');
                $data['order_sn'][] = $o->getAttr('order_sn');
                $data['money']      = bcadd($data['money'], $amount, 2);
            }
        }

        $data['order_id'] = implode(',', $data['order_id']);
        $data['order_sn'] = implode(',', $data['order_sn']);
        $data['no_pay']   = $data['money'] == '0.00';

        $data['no_pay'] AND output_error('订单已经支付！');

        output_success('', $data);
    }

    //-------------------------------------------------- 私有方法

    /**
     * 可用优惠券
     * @param $coupon_id
     * @param $money
     * @param $product_id
     * @return MemberCouponModel
     * @throws Exception
     */
    private function coupon_use_info($coupon_id, $money, $product_id)
    {
        $coupon = MemberCouponModel::coupon_use_info($coupon_id, $this->member_id, $money, $product_id);

        empty($coupon) AND output_error('优惠券不符合使用要求！');
        return $coupon;
    }
}
