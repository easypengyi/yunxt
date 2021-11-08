<?php

namespace app\admin\controller;

use app\common\model\ActivityOrderShop;
use app\common\model\ActivityPeople;
use Tree;
use Exception;
use think\Cache;
use tool\PaymentTool;
use app\common\model\Admin as AdminModel;
use app\common\controller\AdminController;
use app\common\model\Region as RegionModel;
use app\common\model\Member as MemberModel;
use app\common\model\Product as ProductModel;
use app\common\model\Message as MessageModel;
use app\common\model\OrderShop as OrderShopModel;
use app\common\model\OrdersShop as OrdersShopModel;
use app\common\model\AdminRule as AdminRuleModel;
use app\common\model\AdminGroup as AdminGroupModel;
use app\common\model\MemberGroup as MemberGroupModel;
use app\common\model\Distribution as DistributionModel;
use app\common\model\ProductCategory as ProductCategoryModel;
use app\common\model\MemberActivation as MemberActivationModel;

/**
 * AJAX 模块
 */
class Ajax extends AdminController
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
        $this->is_ajax OR $this->error('提交方式不正确！');

        $this->show = ['navigation_bar' => false, 'left_nav' => false, 'head_nav' => false];
    }

    //-------------------------------------------------- 数据提交

    /**
     * 管理员头像修改
     * @param $file_id
     * @throws Exception
     */
    public function admin_headpic_change($file_id)
    {
        $data_info = AdminModel::get($this->user['admin_id']);
        $result    = $data_info->save(['admin_headpic_id' => $file_id]);
        $result OR $this->error('头像修改失败！');
        $this->success('头像修改成功！');
    }

    /**
     * 消息已读修改
     * @param $message_id
     * @throws Exception
     */
    public function head_message_read($message_id)
    {
        $where = ['message_id' => $message_id, 'member_id' => 0];

        $data_info = MessageModel::get($where);
        $result    = $data_info->save(['readed' => true]);
        $result OR $this->error('消息已读失败！');
        $this->success('消息已读成功！');
    }

    //-------------------------------------------------- 模型数据对象

    /**
     * 优惠券商品列表
     * @param $id
     * @throws Exception
     */
    public function product_list($id)
    {
        $where['product_id'] = ['in', $id];

        $order = ['product_id' => 'asc'];

        $list = ProductModel::all_list([], $where, $order);
        $this->success_result($list);
    }

    /**
     * 会员列表
     * @param $id
     * @throws Exception
     */
    public function member_list($id)
    {
        $where['member_id'] = ['in', $id];

        $order = ['member_id' => 'asc'];

        $list = MemberModel::all_list([], $where, $order);
        $this->success_result($list);
    }

    /**
     * 店铺列表
     * @param $id
     * @throws \think\exception\DbException
     */
    public function people_list($id)
    {
        $where['id'] = ['in', $id];
        $list = ActivityPeople::all_list([], $where, []);
        $this->success_result($list);
    }


    /**
     * select选项--会员用户组
     * @param $id
     * @return mixed
     * @throws Exception
     */
    public function select_group($id)
    {
        $list = MemberGroupModel::group_array();
        $this->assign('select_id', $id);
        $this->assign('data_list', $list);
        return $this->fetch_view('select_base');
    }

    public function select_stock($id)
    {
        $list = MemberGroupModel::select_stock();
        $this->assign('select_id', $id);
        $this->assign('data_list', $list);
        return $this->fetch_view('select_base');
    }

    //-------------------------------------------------- HTML数据

    /**
     * 后台消息列表
     * @return mixed
     * @throws Exception
     */
    public function message_head_list()
    {
        $list          = MessageModel::message_list(0, 0, true);
        $message_total = MessageModel::message_no_read_number(0);

        $this->assign($list);
        $this->assign('message_total', $message_total);
        return $this->fetch_view();
    }

    /**
     * 商品分类菜单列表
     * @param int    $pid
     * @param int    $level
     * @param string $tag
     * @return mixed
     * @throws Exception
     */
    public function product_category_list($pid = 0, $level = 0, $tag = 'pid')
    {
        $where['pid'] = $pid;

        $order = ['sort' => 'asc'];

        $list = ProductCategoryModel::all_list([], $where, $order);

        $tree = Tree::instance();
        $tree->init($list->toArray(), ['parentid' => 'pid', 'id' => ProductCategoryModel::primary_key()]);
        $list = $tree->menu_left($pid, $level);

        $this->assign('data_list', $list);
        $this->assign('level', $level);
        $this->assign('tag', $tag);
        return $this->fetch_view();
    }

    /**
     * 管理员用户组权限树
     * @param $group_id
     * @return mixed
     * @throws Exception
     */
    public function admin_auth_group_access($group_id)
    {
        $group = AdminGroupModel::get($group_id);

        $list = AdminRuleModel::rule_tree();

        $key = md5(serialize($list)) . md5(serialize($group['rules']));

        $access = Cache::get($key);
        if (empty($access)) {
            $access = $this->group_access_sub_list($list, $group['rules']);
            Cache::tag(AdminRuleModel::getCacheTag())->set($key, $access);
        }

        $this->assign('access', $access);
        return $this->fetch_view('auth_group_access');
    }

    /**
     * 管理员权限菜单列表
     * @param int    $pid
     * @param int    $level
     * @param string $tag
     * @return mixed
     * @throws Exception
     */
    public function admin_auth_rule($pid = 0, $level = 0, $tag = 'pid')
    {
        $where['pid'] = $pid;

        $order = ['sort' => 'asc'];

        $list = AdminRuleModel::all_list([], $where, $order);

        $tree = Tree::instance();
        $tree->init($list->toArray(), ['parentid' => 'pid']);
        $list = $tree->menu_left($pid, $level);

        $this->assign('data_list', $list);
        $this->assign('level', $level);
        $this->assign('tag', $tag);
        return $this->fetch_view();
    }

    //-------------------------------------------------- select子项

    /**
     * select选项--配送方式
     * @return mixed
     * @throws Exception
     */
    public function select_distribution()
    {
        $list = DistributionModel::column('name', 'id');

        $this->assign('select_id', 0);
        $this->assign('data_list', $list);
        return $this->fetch_view('select_base');
    }

    /**
     * select选项--订单状态
     * @param $id
     * @return mixed
     */
    public function select_order_status($id)
    {
        $list = OrderShopModel::order_status_array();

        $this->assign('select_id', $id);
        $this->assign('data_list', $list);
        return $this->fetch_view('select_base');
    }

    /**
     * select选项--订单状态
     * @param $id
     * @return mixed
     */
    public function select_order1_status($id)
    {
        $list = OrdersShopModel::order_status_array();

        $this->assign('select_id', $id);
        $this->assign('data_list', $list);
        return $this->fetch_view('select_base');
    }

    /**
     * select选项--订单状态
     * @param $id
     * @return mixed
     */
    public function select_activation_status($id)
    {
        $list = MemberActivationModel::order_status_array();

        unset($list[1], $list[2], $list[4]);
        $this->assign('select_id', $id);
        $this->assign('data_list', $list);
        return $this->fetch_view('select_base');
    }


    /**
     * select选项--订单状态
     * @param $id
     * @return mixed
     */
    public function select_order3_status($id)
    {
        $list = ActivityOrderShop::order_status_array();

        $this->assign('select_id', $id);
        $this->assign('data_list', $list);
        return $this->fetch_view('select_base');
    }

    /**
     * select选项--商品分类
     * @param $id
     * @return mixed
     * @throws Exception
     */
    public function select_product_category($id)
    {
        $list = ProductCategoryModel::category_filter(input('level', 1));

        $this->assign('select_id', $id);
        $this->assign('data_list', $list);
        return $this->fetch_view('select_base');
    }

    /**
     * select选项--管理员用户组
     * @param $id
     * @return mixed
     * @throws Exception
     */
    public function select_admin_group($id)
    {
        $select_id = AdminGroupModel::user_group_group($id);

        $list = AdminGroupModel::group_array(false);

        $this->assign('select_id', $select_id);
        $this->assign('data_list', $list);
        return $this->fetch_view('select_base');
    }

    /**
     * select选项--会员用户组
     * @param $id
     * @return mixed
     * @throws Exception
     */
    public function select_member_group($id)
    {
        $select_id = MemberGroupModel::user_group_group($id);

        $list = MemberGroupModel::group_array();

        $this->assign('select_id', $select_id);
        $this->assign('data_list', $list);
        return $this->fetch_view('select_base');
    }

    /**
     * select选项--支付类型
     * @param $id
     * @return mixed
     * @throws Exception
     */
    public function select_payment($id)
    {
        $list = PaymentTool::instance()->payment_array();

        $this->assign('select_id', $id);
        $this->assign('data_list', $list);
        return $this->fetch_view('select_base');
    }

    /**
     * select选项--支付种类
     * @param $id
     * @return mixed
     * @throws Exception
     */
    public function select_pay_type($id)
    {
        $list = PaymentTool::instance()->suppor_pay_type(input('payment_id', ''));

        $this->assign('select_id', $id);
        $this->assign('data_list', $list);
        return $this->fetch_view('select_base');
    }

    /**
     * select选项--商品分类树
     * @param $id
     * @return mixed
     * @throws Exception
     */
    public function select_product_category_tree($id)
    {
        $list = ProductCategoryModel::all_product_category();

        $tree = Tree::instance();
        $tree->init($list, ['parentid' => 'pid', 'id' => ProductCategoryModel::primary_key()]);
        $list = $tree->menu_left();

        $this->assign('select_id', $id);
        $this->assign('data_list', $list);
        return $this->fetch_view();
    }

    /**
     * select选项--管理员权限菜单树
     * @param $id
     * @return mixed
     * @throws Exception
     */
    public function select_admin_rule_tree($id)
    {
        $list = AdminRuleModel::all_auth_rule();

        $tree = Tree::instance();
        $tree->init($list, ['parentid' => 'pid']);
        $list = $tree->menu_left();

        $this->assign('select_id', $id);
        $this->assign('data_list', $list);
        return $this->fetch_view('select_rule_tree');
    }

    /**
     * select选项--省信息
     * @param int $id
     * @return mixed
     * @throws Exception
     */
    public function select_province($id = 0)
    {
        return $this->select_region($id, RegionModel::PROVINCE);
    }

    /**
     * select选项--市信息
     * @param int $id
     * @return mixed
     * @throws Exception
     */
    public function select_city($id = 0)
    {
        return $this->select_region($id, RegionModel::CITY);
    }

    /**
     * select选项--区信息
     * @param int $id
     * @return mixed
     * @throws Exception
     */
    public function select_district($id = 0)
    {
        return $this->select_region($id, RegionModel::DISTRICT);
    }

    /**
     * select选项--区域信息
     * @param int $id
     * @param int $type
     * @return mixed
     */
    public function select_region($id = 0, $type = 0)
    {
        $list = RegionModel::children_array($type, input('pid', 1));

        $this->assign('select_id', $id);
        $this->assign('data_list', $list);
        return $this->fetch_view('select_base');
    }

    //-------------------------------------------------- 私有方法

    /**
     * 递归输出权限
     * @param        $list
     * @param        $rule
     * @param string $tag
     * @param int    $level
     * @return string
     * @throws Exception
     */
    private function group_access_sub_list($list, $rule, $tag = 'id', $level = 1)
    {
        $html = '';
        $left = ($level * 20 - 10) . 'px';
        foreach ($list as $k => $v) {
            $tags = $tag . '-' . $v['id'];

            $data = [
                'dataid'    => $tags,
                'checked'   => in_array($v['id'], $rule),
                'left'      => $left,
                'level'     => $level,
                'data_info' => $v,
            ];

            $html .= $this->view->fetch('piece/group_access_piece', $data);

            if (isset($v['sub']) && !empty($v['sub'])) {
                $html .= $this->group_access_sub_list($v['sub'], $rule, $tags, $level + 1);
            }
        }
        return $html;
    }
}
