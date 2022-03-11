<?php

namespace app\admin\controller;

use app\common\model\ActivityOrderShop;
use app\common\model\ActivityPeople;
use app\common\model\CouponGive as CouponGiveModel;
use app\common\model\OrderssShop;
use app\common\model\Region;
use Exception;
use app\common\Constant;
use app\common\controller\AdminController;
use app\common\model\Member as MemberModel;
use app\common\model\Message as MessageModel;
use app\common\model\Product as ProductModel;
use app\common\model\Feedback as FeedbackModel;
use app\common\model\OrderShop as OrderShopModel;
use app\common\model\MemberGroup as MemberGroupModel;
use app\common\model\ProductCategory as ProductCategoryModel;
use app\common\model\ProductEvaluate as ProductEvaluateModel;
use app\common\model\MemberGroupRelation as MemberGroupRelationModel;

/**
 * 公共 模块
 */
class Common extends AdminController
{
    protected $no_check = true;

    /**
     * 初始化方法
     * @return void
     * @throws Exception
     */
    public function _initialize()
    {
        parent::_initialize();

        if (!$this->check_referer()) {
            $this->error('无访问权限！');
        }

        $this->show = ['navigation_bar' => false, 'left_nav' => false, 'head_nav' => false];
    }

    /**
     * 订单详情
     * @param $id
     * @return mixed
     * @throws Exception
     */
    public function order_detail($id)
    {
        $invoice_type = Constant::invoice_array();
        $data_info    = OrderShopModel::get($id);
        $data_info['province'] = Region::get($data_info['province'])['name'];
        $data_info['city'] = Region::get($data_info['city'])['name'];
        $data_info['district'] = Region::get($data_info['district'])['name'];
        $this->assign('data_info', $data_info);
        $this->assign('invoice_type', $invoice_type);
        return $this->fetch_view();
    }

    /**
     * 订单详情
     * @param $id
     * @return mixed
     * @throws Exception
     */
    public function orderss_detail($id)
    {
        $invoice_type = Constant::invoice_array();
        $data_info    = OrderssShop::get($id);
        $this->assign('data_info', $data_info);
        $this->assign('invoice_type', $invoice_type);
        return $this->fetch_view();
    }

    /**
     * 订单详情
     * @param $id
     * @return mixed
     * @throws Exception
     */
    public function activity_detail($id)
    {
        $data_info = ActivityOrderShop::get($id);
        $data_info['province'] = Region::get($data_info['province'])['name'];
        $data_info['city'] = Region::get($data_info['city'])['name'];
        $data_info['district'] = Region::get($data_info['district'])['name'];
        $this->assign('data_info', $data_info);
        return $this->fetch_view();
    }

    /**
     * 商品评价详情
     * @param $id
     * @return mixed
     * @throws Exception
     */
    public function product_evaluate_detail($id)
    {
        $data_info = ProductEvaluateModel::get($id);
        $this->assign('data_info', $data_info);
        return $this->fetch_view();
    }

    /**
     * 会员反馈详情
     * @param $id
     * @return mixed
     * @throws Exception
     */
    public function feedback_detail($id)
    {
        $data_info = FeedbackModel::get($id, ['Member']);
        $this->assign('data_info', $data_info);
        return $this->fetch_view();
    }

    /**
     * 后台消息列表
     * @return mixed
     * @throws Exception
     */
    public function head_message_list()
    {
        $where['del']       = false;
        $where['show_time'] = ['<=', time()];
        $where['member_id'] = 0;

        $order = $this->sort_order(MessageModel::getTableFields(), 'readed', 'asc');

        $order['show_time'] = 'desc';

        $list = MessageModel::page_list($where, $order);
        if (!$list->isEmpty()) {
            $list->load(['SendMember']);
            $list->append(['extras']);
        }

        $this->assign($list->toArray());
        return $this->fetch_view();
    }

    //-------------------------------------------------- 数据选择

    /**
     * 选择商品
     * @return mixed
     * @throws Exception
     */
    public function choose_product()
    {
        $product_id = array_filter(input('product_id/a', [1,2,3,4,5]));

        $where        = $this->search('name', '输入需要查询的商品名称');
        $where['del'] = false;

        empty($product_id) OR $where['product_id'] = ['not in', $product_id];

        $order = $this->sort_order(ProductModel::getTableFields(), 'product_id', 'asc');

        $list = ProductModel::page_list($where, $order);

        $this->assign('choose_all', boolval(input('choose_all', 0)));
        $this->assign('callback', input('callback', ''));
        $this->assign($list->toArray());
        return $this->fetch_view('', ['product_id', 'callback', 'choose_all']);
    }

    /**
     * 选择会员
     * @param int $member_group
     * @return mixed
     * @throws Exception
     */
    public function choose_member()
    {
        $member_id = array_filter(input('member_id/a', []));

        $where = $this->search('member_realname|member_tel', '输入需要查询的姓名、手机号');

        empty($member_id) OR $where['member_id'] = ['not in', $member_id];
        $where['del'] = false;
//        $group['group_id'] = ['neq', MemberGroupRelationModel::seven];
//        $where[]      = ['exp', MemberGroupRelationModel::where_in_raw($group, 'member_id')];
        $order = $this->sort_order(MemberModel::getTableFields(), 'member_id', 'asc');

        $list = MemberModel::page_list($where, $order);

        $this->assign('choose_all', boolval(input('choose_all', 0)));
        $this->assign('callback', input('callback', ''));
        $this->assign($list->toArray());
        return $this->fetch_view('choose_member', ['member_group', 'member_id', 'callback', 'choose_all']);
    }


    /**
     * 选择店铺
     * @return mixed
     * @throws Exception
     */
    public function choose_people()
    {
        $admin_id = array_filter(input('admin_id/a', []));

        $where        = $this->search('name', '输入需要查询的姓名');
        empty($admin_id) OR $where['id'] = ['not in', $admin_id];

        $list = ActivityPeople::page_list($where, []);

        $this->assign('choose_all', boolval(input('choose_all', 0)));
        $this->assign('callback', input('callback', ''));
        $this->assign($list->toArray());
        return $this->fetch_view('', ['admin_id', 'callback', 'choose_all']);
    }


    /**
     * 查看赠送人
     * @return mixed
     * @throws Exception
     */
    public function coupon_member()
    {
        $give_id = input('give_id');

        $data_info    = CouponGiveModel::get($give_id);

        $where['member_id'] = ['in',$data_info['member_ids']];

        $order = $this->sort_order(MemberModel::getTableFields(), 'member_id', 'asc');

        $list = MemberModel::page_list($where, $order);

        $this->assign($list->toArray());
        return $this->fetch_view('coupon_member');
    }

    /**
     * 选择商品分类
     * @return mixed
     * @throws Exception
     */
    public function choose_product_category()
    {
        $category_id = array_filter(input('category_id/a', []));

        $where = $this->search('name', '输入需要查询的分类名称');

        $where['level']  = 1;
        $where['enable'] = true;
        empty($category_id) OR $where['category_id'] = ['not in', $category_id];

        $order = $this->sort_order(ProductCategoryModel::getTableFields(), 'category_id', 'asc');

        $list = ProductCategoryModel::page_list($where, $order);

        $this->assign('choose_all', boolval(input('choose_all', 0)));
        $this->assign('callback', input('callback', ''));
        $this->assign($list->toArray());
        return $this->fetch_view('', ['category_id', 'callback', 'choose_all']);
    }
}
