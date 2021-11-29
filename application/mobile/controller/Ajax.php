<?php

namespace app\mobile\controller;

use app\common\model\ArticleCenter;
use Exception;
use think\Session;
use app\common\ResultCode;
use app\common\controller\MobileController;
use app\common\model\Product as ProductModel;

/**
 * AJAX
 */
class Ajax extends MobileController
{
    /**
     * 初始化方法
     * @return void
     */
    protected function _initialize()
    {
        parent::_initialize();

        if (!$this->is_ajax) {
            $this->error('非法请求！');
        }
    }

    /**
     * 图片上传
     * @return void
     * @throws Exception
     */
    public function upload_image()
    {
        $res      = $this->api('File', 'upload_image');
        $image_id = explode(',', $res['data']['image']);
        $image    = [];
        foreach ($res['data']['url'] as $k => $v) {
            $image[] = ['id' => $image_id[$k], 'url' => $v];
        }
        $this->success_result($image);
    }

    /**
     * 全站统一客服信息
     * @return void
     * @throws Exception
     */
    public function service_message()
    {
        $result = $this->api('Holistic', 'service_phone');
        $this->success_result($result['data']);
    }

    /**
     * 更新banner点击数量
     * @return void
     * @throws Exception
     */
    public function banner_click()
    {
        $this->api('Banner', 'banner_click', ['id' => input('id', '')]);
        $this->success();
    }

    /**
     * 商品列表
     * @return void
     * @throws Exception
     */
    public function product_list()
    {
        $param = [
            'category_id'    => input('category_id', 0),
            'category_level' => 1,
            'page'           => input('page', ''),
            'sort_type'      => input('sort_type', ProductModel::SORT_PURCHASED_ASC),
        ];
        $list  = $this->api('Shop', 'product_list', $param);
        $this->success_result($list['data']);
    }

    /**
     * 消息列表
     * @return void
     * @throws Exception
     */
    public function message_list()
    {
        $list = $this->api('Message', 'message_list', ['page' => input('page', '')]);

        foreach ($list['data']['list'] as $v) {
            if (!$v['readed']) {
                $this->api('Message', 'message_readed', ['message_id' => $v['message_id']]);
            }
        }
        $this->success_result($list['data']);
    }

    /**
     * 消息删除
     * @return void
     * @throws Exception
     */
    public function message_delete()
    {
        $result = $this->api('Message', 'message_delete', ['message_id' => input('message_id', '')]);
        $this->success_result($result['data']);
    }

    /**
     * 地址删除
     * @return void
     * @throws Exception
     */
    public function address_delete()
    {
        $result = $this->api('Address', 'address_delete', ['address_id' => input('address_id', '')]);
        $this->success_result($result['data']);
    }

    /**
     * 地址默认修改
     * @return void
     * @throws Exception
     */
    public function address_default_change()
    {
        $param  = ['tolerant' => input('tolerant'), 'address_id' => input('address_id', '')];
        $result = $this->api('Address', 'address_default_change', $param);
        $this->success_result($result['data']);
    }

    /**
     * 商品收藏列表接口
     * @return void
     * @throws Exception
     */
    public function product_collection_list()
    {
        $param  = ['page' => input('page', '')];
        $result = $this->api('Shop', 'product_collection_list', $param);
        $this->success_result($result['data']);
    }

    /**
     * 商品收藏状态变更
     * @return void
     * @throws Exception
     */
    public function product_collection_change()
    {
        $product_id = input('product_id', '');
        $collection = boolval(input('collection', 0));

        if ($collection) {
            $param = ['product_id' => $product_id];
            $this->api('Shop', 'product_collection_cancel', $param);
            $collection = false;
        } else {
            $param = ['product_id' => $product_id];
            $this->api('Shop', 'product_collection_insert', $param);
            $collection = true;
        }
        $this->success_result(['collection' => $collection], $collection ? '收藏成功！' : '收藏取消成功！');
    }

    /**
     * 商品添加购物车
     * @return void
     * @throws Exception
     */
    public function product_add_cart()
    {
        $product_id = input('product_id', '');

        $param  = ['product_id' => $product_id];
        $result = $this->api('Cart', 'product_add', $param);
        $this->success_result($result['data']);
    }

    /**
     * 购物车商品删除
     * @return void
     * @throws Exception
     */
    public function product_delete()
    {
        $result = $this->api('Cart', 'product_delete', ['product_id' => input('product_id', '')]);
        $this->success_result($result['data']);
    }


    /**
     * 商城订单收货操作
     * @param $id
     * @throws Exception
     */
    public function distribution_examine()
    {
        $result =  $this->api('Order', 'distribution_examine',['order_id'=>input('order_id', 0)]);
        $this->success_result($result);
    }

    /**
     * 商城取消订单操作 by shiqiren
     * @param $id
     * @throws Exception
     */
    public function order_cancel()
    {
        $result =  $this->api('Order', 'order_cancel',['order_id'=>input('order_id', 0)]);
        $this->success_result($result);
    }

    /**
     * 会员优惠券列表接口
     * @return void
     * @throws Exception
     */
    public function coupon_list()
    {
        $param  = [
            'coupon_type' => input('coupon_type', ''),
            'paginate'    => false,
        ];
        $result = $this->api('Coupon', 'member_coupon_list', $param);
        $this->success_result($result['data']);
    }

    /**
     * 会员可用优惠券列表接口
     * @return void
     * @throws Exception
     */
    public function coupon_use_list()
    {
        $param  = [
            'product_id' => input('product_id', ''),
            'money'      => input('money', ''),
            'coupon_id'  => input('coupon_id', ''),
            'paginate'   => false,
        ];
        $result = $this->api('Coupon', 'coupon_use_list', $param);
        $this->success_result($result['data']);
    }

    /**
     * 中心授权结算
     * @return void
     * @throws Exception
     */
    public function single_settlement()
    {

        $result = $this->api('Order', 'single_settlement', [
            'two_id' => input('two_id', ''),
            'nick_name' => input('nick_name', ''),
            'mobile' => input('mobile', ''),
            'phone' => input('phone', ''),
            'uid'   => input('uid', ''),
            'trigger' => input('trigger', ''),
            'trigger1' => input('trigger1', ''),

        ]);
        $result['code'] == ResultCode::RES_MEMBER AND $this->error_result($result);

        Session::set('order_key', $result['data']['order_key']);
        $this->success_result($result['data']);
    }

    /**
     * 活动订单结算
     * @return void
     * @throws Exception
     */
    public function single1_settlement()
    {
        $result = $this->api('Order', 'single1_settlement', [
                 'product_id' => input('product_id', ''),
                 'product_num' =>  input('product_num', 1),
            ]
        );
        $result['code'] == ResultCode::RES_MEMBER AND $this->error_result($result);

        Session::set('order_key', $result['data']['order_key']);
        $this->success_result($result['data']);
    }



    /**
     * 我要发货
     * @return void
     * @throws Exception
     */
    public function single2_settlement()
    {
        $result = $this->api('Order', 'single2_settlement', ['product_id' => input('product_id', '')]);
        $result['code'] == ResultCode::RES_MEMBER AND $this->error_result($result);

        Session::set('order_key', $result['data']['order_key']);
        $this->success_result($result['data']);
    }


    /**
     * 余额记录列表
     * @return void
     * @throws Exception
     */
    public function balance_record_list()
    {
        $result = $this->api('Member', 'balance_record_list', ['page' => input('page', '')]);
        $this->success_result($result['data']);
    }

    /**
     * 订单列表
     * @return void
     * @throws Exception
     */
    public function order_list()
    {
        $result = $this->api('Order', 'order_list', ['page' => input('page', ''), 'category' => input('category', '')]);
        $this->success_result($result['data']);
    }

    /**
     * 订单列表
     * @return void
     * @throws Exception
     */
    public function order1_list()
    {
        $result = $this->api('Order', 'order1_list', ['page' => input('page', ''), 'category' => input('category', '')]);
        $this->success_result($result['data']);
    }

    /**
     * 订单列表
     * @return void
     * @throws Exception
     */
    public function order2_list()
    {
        $result = $this->api('Order', 'order2_list', ['page' => input('page', ''), 'category' => input('category', '')]);
        $this->success_result($result['data']);
    }

    /**
     * 核销订单列表
     * @return void
     * @throws Exception
     */
    public function order3_list()
    {
        $result = $this->api('Order', 'order3_list', ['page' => input('page', '')]);
        $this->success_result($result['data']);
    }

    /**
     * HPV订单列表
     * @return void
     * @throws Exception
     */
    public function order4_list()
    {
        $result = $this->api('Order', 'order4_list', ['page' => input('page', '')]);
        $this->success_result($result['data']);
    }

    /**
     * @return void
     * @throws Exception
     */
    public function video_list()
    {
        $result = $this->api('Banner', 'video_list', ['page' => input('page', ''), 'category' => input('category', '')]);
        $this->success_result($result['data']);
    }

    /**
     * @return void
     * @throws Exception
     */
    public function source_list()
    {
        $result = $this->api('Banner', 'source', ['page' => input('page', ''),'type' => ArticleCenter::TYPE_ONE]);
        $this->success_result($result['data']['list']);
    }

    /**
     * @return void
     * @throws Exception
     */
    public function article_list()
    {
        $result = $this->api('Banner', 'article', ['page' => input('page', ''),'type' => ArticleCenter::TYPE_TWO]);
        $this->success_result($result['data']['list']);
    }


    /**
     * 会员评价列表
     * @return void
     * @throws Exception
     */
    public function comment_list()
    {
        $result = $this->api('Member', 'comment_list', ['page' => input('page', '')]);
        $this->success_result($result['data']);
    }

    /**
     * 邀请列表
     * @return void
     * @throws Exception
     */
    public function invitation_list()
    {
        $param = [
            'page'          => input('page', ''),
            'level'         => input('level', 1),
            'invitation_id' => input('invitation_id', 0),
        ];

        $result = $this->api('Member', 'invitation_list', $param);
        $this->success_result($result['data']);
    }

    /**
     * 会员佣金记录列表
     * @return void
     * @throws Exception
     */
    public function commission_record_list()
    {
        $result = $this->api('Member', 'commission_record_list', ['page' => input('page', ''),'type' => input('type', '')]);
        $this->success_result($result['data']);
    }

    /**
     * 会员佣金记录列表
     * @return void
     * @throws Exception
     */
    public function commission_record_list1()
    {
        $result = $this->api('Member', 'commission_record_list1', ['page' => input('page', '')]);
        $this->success_result($result['data']);
    }
    /**
     * 我的团队
     */
    public function team_list(){
        $param = [
            'page'          => input('page', ''),
            'category'      => input('category', ''),
        ];

        $result = $this->api('Member', 'team_list', $param);
        $this->success_result($result['data']);
    }

    /**
     * 我的学员
     */
    public function student_list(){
        $param = [
            'page'          => input('page', ''),
        ];
        $result = $this->api('Member', 'student_list', $param);
        $this->success_result($result);
    }

    /**
     * 查找转赠人信息
     *
     * @throws Exception
     */
    public function member(){
        $result =  $this->api('Member', 'member_info',['mobile'=>input('mobile', '')]);
        $this->success_result($result);
    }
}
