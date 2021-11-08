<?php

namespace app\mobile\controller;

use app\common\model\Article as ArticleModel;
use app\common\model\Configure as ConfigureModel;
use app\common\model\Member;
use app\common\model\MemberGroupRelation;
use Exception;
use think\Cache;
use think\Log;
use think\Request;
use think\Session;
use helper\ValidateHelper;
use app\common\ResultCode;
use app\common\controller\MobileController;
use tool\HashidsTool;

/**
 * 商品
 */
class Product extends MobileController
{


    /**
     * 商品列表
     * @return mixed
     * @throws Exception
     */
    public function product_list()
    {
        $result        = $this->api('Shop', 'product_category_list');
        $category_list = $result['data']['list'];

        $category_id = input('category_id', 0);
        if (empty($category_id) && !empty($category_list)) {
            $category_id = $category_list[0]['category_id'];
        }

        $this->assign('category_id', $category_id);
        $this->assign('category_list', $category_list);
        $this->assign('title', 'ONCE商城');
        return $this->fetch();
    }


    /**
     * 商品详情
     * @return mixed
     * @throws Exception
     */
    public function detail()
    {

        $invitation_id = input('invitation_id', '');
        !empty($invitation_id) AND  Session::set('invitation_id',$invitation_id);

        $this->set_wx_login(Request::instance()->domain().Request::instance()->url());
        $this->login_check();
        $product_id = input('product_id', '');

        if ($product_id <= 5){
            $this->success('', folder_url('Index/index'));
        }

        $result  = $this->api('Shop', 'product_detail', ['product_id' => $product_id]);
        $product = $result['data'];
        $share_code = url('mobile/product/detail', ['product_id'=>$product_id,'invitation_id'=>Member::create_invitation($this->member['member_id'])], true, true);
        $this->assign('img',$this->member['member_headpic']['full_url']);
        $this->assign('share_img', $result['data']['share_image']['full_url']);
        $this->assign('share_code', $share_code);
        $this->assign('data_info', $product);
        $this->assign('title', '商品详情');
        $this->assign('return_url', $this->http_referer ?: folder_url('Index/index'));
        return $this->fetch();
    }


    /**
     * 中心授权
     * @return mixed
     * @throws \think\exception\DbException
     */
    public function one_index(){

        $this->assign('agreement', '');
        $this->assign('title', '系统报单');
        $this->assign('return_url', folder_url('User/index'));
        return $this->fetch();
    }




    /**
     * 中心授权提交订单
     * @return mixed
     * @throws Exception
     */
    public function order_submit()
    {
        $this->login_check();

        $order_key = Session::get('order_key');

        if (empty($order_key)) {
            $this->redirect($this->http_referer);
        }

        if ($this->is_ajax) {
            $top_id        = input('top_id', '');
            $top_name        = input('top_name', '');
            $two_id        = input('two_id', '');
            $two_name        = input('two_name', '');
            $nick_name       = input('nick_name', '');
            $mobile          = input('mobile', '');
            $uid             = input('uid', '');
            $province        = input('province', '');
            $city            = input('city', '');
            $district        = input('district', '');
            $address         = input('address', '');

            $param  = [
                'order_key' => $order_key,
                'top_id' => $top_id,
                'top_name' => $top_name,
                'two_id' => $two_id,
                'two_name' => $two_name,
                'id_code' => $uid,
                'nick_name' => $nick_name,
                'mobile' => $mobile,
                'province' => $province,
                'city' => $city,
                'district' => $district,
                'address' => $address,
            ];
            $result = $this->api('Order', 'single_order_place', $param);

            Session::delete('order_key');

            $data = [
                'order_id' => $result['data']['order_id'],
                'money'    => $result['data']['money'],
                'order_sn' => $result['data']['order_sn'],
            ];
            Session::set('payment_data', ['type' => 'shop', 'data' => $data]);
            $this->success('', folder_url('User/order'));
        }

        $result = $this->api('Order', 'single_settlement_info', ['order_key' => $order_key]);
        $this->assign('data_list', $result['data']);

        $this->assign('return_url',  $this->http_referer);
        $this->assign('title', '提交订单');
        return $this->fetch();
    }

    /**
     * 公益报单提交订单
     * @return mixed
     * @throws Exception
     */
    public function order1_submit()
    {
        $this->login_check();

        $order_key = Session::get('order_key');

        if (empty($order_key)) {
            $this->redirect($this->http_referer);
        }

        if ($this->is_ajax) {
            $address_id   = input('address_id');
            $param  = [
                'order_key' => $order_key,
                'address_id' =>$address_id,
            ];

            $result = $this->api('Order', 'single1_order_place', $param);

            Session::delete('order_key');

            $data = [
                'order_id' => $result['data']['order_id'],
                'money'    => $result['data']['money'],
                'order_sn' => $result['data']['order_sn'],
            ];
            Session::set('payment_data', ['type' => 'public', 'data' => $data]);
            $this->success('', folder_url('Payment/payment'));
        }

        $result = $this->api('Order', 'single1_settlement_info', ['order_key' => $order_key]);
        $address = $this->api('Address', 'default_address', ['paginate' => false]);
        $this->assign('data_list', $result['data']);
        $this->assign('address_list', $address);
        $this->assign('return_url', folder_url('Index/index'));
        $this->assign('title', '提交订单');
        return $this->fetch();
    }


}