<?php

namespace app\admin\controller;

use app\common\model\Configure;
use app\common\model\Member;
use app\common\model\MemberCommission;
use app\common\model\MemberGroupRelation;
use app\common\model\Product as ProductModel;
use app\common\model\Region;
use Exception;
use helper\StrHelper;
use helper\TimeHelper;
use app\common\controller\AdminController;
use app\common\model\OrdersMiniApp as OrdersShopModel;
use think\Db;
// use tool\PaymentTool;


/**
 * 公益报单订单 模块
 */
class OrderMiniApp extends AdminController
{
    /**
     * 初始化方法
     * @return void
     * @throws Exception
     */
    public function _initialize()
    {
        parent::_initialize();
        $this->param['payment']       = [0 => '未支付', 2 => '微信', 4 => '余额'];
        $this->param['status']        = OrdersShopModel::order_status_array();
        $this->param['refund_status'] = OrdersShopModel::order_refund_status_array();

        
    }

    

    /**
     * 列表
     * @return mixed
     * @throws Exception
     */
    public function index()
    {
        $where = $this->search('order_sn', '输入需查询的订单号');

        $this->search['date']   = input('date', '');
        $this->search['status'] = input('status', '');

        $range_time = TimeHelper::range_time($this->search['date']);
        empty($range_time) OR $where['order_time'] = ['between', $range_time];

        $where['status'] = ['not in', [OrdersShopModel::STATUS_INVALID]];
        empty($this->search['status']) OR $where['status'] = $this->search['status'];

        $where['del']        = false;


        $order = $this->sort_order(OrdersShopModel::getTableFields(), 'order_time', 'desc');

        $this->export($where, $order);

        $list = OrdersShopModel::page_list($where, $order);
        if (!$list->isEmpty()) {
            $list->load(['Member']);
        }

        $this->assign('total_money', OrdersShopModel::where($where)->sum('amount'));
        $this->assign($list->toArray());
        return $this->fetch_view();
    }

    /**
     * 待发货订单列表
     * @return mixed
     * @throws Exception
     */
    public function delivery_index()
    {
        $where = $this->search('order_sn', '输入需查询的订单号');

        $where['del']           = false;
        $where['status']        = OrdersShopModel::STATUS_ALREADY_PAY;
        $where['refund_status'] = ['not in', [OrdersShopModel::REFUND_STATUS_APPLY]];

        $order = $this->sort_order(OrdersShopModel::getTableFields(), 'order_time', 'desc');

        $this->export($where, $order);

        $list = OrdersShopModel::page_list($where, $order);
        if (!$list->isEmpty()) {
            $list->load(['Member']);
        }

        $this->assign($list->toArray());
        return $this->fetch_view();
    }

    /**
     * 发货操作
     * @param $id
     * @throws Exception
     */
    public function delivery_examine($id)
    {
        $this->is_ajax OR $this->error('请求错误！');

        $where['order_id']      = $id;
        $where['del']           = false;
        $where['status']        = OrdersShopModel::STATUS_ALREADY_PAY;
        $where['refund_status'] = ['not in', [OrdersShopModel::REFUND_STATUS_APPLY]];

        $data_info = OrdersShopModel::get($where);
        empty($data_info) AND $this->error('数据不存在！');

        $data   = [
            'refund_status'   => OrdersShopModel::REFUND_STATUS_NO,
            'status'          => OrdersShopModel::STATUS_ALREADY_DISTRIBUTION,
            'courier_sn'      => input('courier_sn', ''),
            'distribution_id' => input('distribution_id', 0),
            'finish_time'   => time(),
        ];
        try {
            Db::startTrans();
            $data_info->save($data);
            self::reward($id);
            Db::commit();
        } catch (Exception $e) {
            Db::rollback();
            $this->error('确认完成失败！');
        }
        $this->success('确认完成！', $this->http_referer ?: $this->return_url());
    }

    /**
     * 取消订单 by shiqiren
     * @param $id
     * @throws Exception
     */
    public function del($id)
    {
        $this->is_ajax OR $this->error('请求错误！');

        $where['order_id']      = $id;
        $where['del']           = false;
        $where['status']        = array('neq',OrdersShopModel::STATUS_INVALID);

        $data_info = OrdersShopModel::get($where);
        empty($data_info) AND $this->error('数据不存在！');

        $data   = [
            'status'          => OrdersShopModel::STATUS_INVALID,
        ];
        try {
            

            $res = true;

            //如果订单已经支付，则还需要退款
            if($data_info->getAttr('payment_time')>0){
                //更新退款申请状态为“完成审批”
                $data2   = [
                    'refund_status' => OrdersShopModel::REFUND_STATUS_SUCCESS
                ];
                $res = $data_info->save($data2);
                $res = $data_info->order_refund(true);//发起退款
            }
            if($res){
                Db::startTrans();
                $data_info->save($data);
                Db::commit();
            }
            else{
                $this->error('操作失败！');
            }
        } catch (Exception $e) {
            Db::rollback();
            $this->error('操作失败！');
        }
        $this->success('操作成功！', $this->http_referer ?: $this->return_url());
    }

    /**
     * 报单城市特权奖 by shiqiren
     * @param $id
     * @return bool
     * @throws \think\Exception
     * @throws \think\exception\DbException
     */
    public static  function reward($id){
        $order = OrdersShopModel::get($id);
     
        $money = $order['product_num']*10;
        
        $member_id = Member::find_member_city($order['address']['district']);
        if ($member_id){
            Member::commission_inc($member_id, $money);
            MemberCommission::insert_log($member_id, MemberCommission::city, $money, $money, '来自'.$order['address']['consignee'].'的城市特权奖', 0);
        }
        
    }

    /**
     * 已取消订单列表 by shiqiren
     * @return mixed
     * @throws Exception
     */
    public function invalid_index()
    {
        $where = $this->search('order_sn', '输入需查询的订单号');

        $where['del']    = false;
        $where['status'] = OrdersShopModel::STATUS_INVALID;

        $order = $this->sort_order(OrdersShopModel::getTableFields(), 'order_time', 'desc');

        $list = OrdersShopModel::page_list($where, $order);
        if (!$list->isEmpty()) {
            $list->load(['Member', 'Distribution']);
        }

        $this->assign($list->toArray());
        return $this->fetch_view();
    }

    /**
     * 已配送订单列表
     * @return mixed
     * @throws Exception
     */
    public function distribution_index()
    {
        $where = $this->search('order_sn', '输入需查询的订单号');

        $where['del']    = false;
        $where['status'] = OrdersShopModel::STATUS_ALREADY_DISTRIBUTION;

        $order = $this->sort_order(OrdersShopModel::getTableFields(), 'order_time', 'desc');

//        $this->export($where, $order);

        $list = OrdersShopModel::page_list($where, $order);
        if (!$list->isEmpty()) {
            $list->load(['Member', 'Distribution']);
        }

        $this->assign($list->toArray());
        return $this->fetch_view();
    }

    /**
     * 收货操作
     * @param $id
     * @throws Exception
     */
    public function distribution_examine($id)
    {
        $this->is_ajax OR $this->error('请求错误！');

        $where['order_id']      = $id;
        $where['del']           = false;
        $where['status']        = OrdersShopModel::STATUS_ALREADY_DISTRIBUTION;
        $where['refund_status'] = ['not in', [OrdersShopModel::REFUND_STATUS_APPLY]];

        $data_info = OrdersShopModel::get($where);
        empty($data_info) AND $this->error('数据不存在！');

        $data   = [
            'refund_status' => OrdersShopModel::REFUND_STATUS_NO,
            'status'        => OrdersShopModel::STATUS_FINISH,
            'finish_time'   => time()
        ];
        $result = $data_info->save($data);
        $result OR $this->error('收货失败！');

        $this->success('收货完成！', $this->http_referer ?: $this->return_url());
    }



    /**
     * 检测中订单列表
     * @return mixed
     * @throws Exception
     */
    public function check_index()
    {
        $where = $this->search('order_sn', '输入需查询的订单号');

        $where['del']    = false;
        $where['status'] = OrdersShopModel::STATUS_CHECKING;

        $order = $this->sort_order(OrdersShopModel::getTableFields(), 'order_time', 'desc');

        $this->export($where, $order);

        $list = OrdersShopModel::page_list($where, $order);
        if (!$list->isEmpty()) {
            $list->load(['Member']);
        }

        $this->assign($list->toArray());
        return $this->fetch_view();
    }

    /**
     * 已完成订单列表
     * @return mixed
     * @throws Exception
     */
    public function finish_index()
    {
        $where = $this->search('order_sn', '输入需查询的订单号');

        $where['del']    = false;
        $where['status'] = ['in', [OrdersShopModel::STATUS_FINISH, OrdersShopModel::STATUS_EVALUATE]];

        $order = $this->sort_order(OrdersShopModel::getTableFields(), 'order_time', 'desc');

        $this->export($where, $order);

        $list = OrdersShopModel::page_list($where, $order);
        if (!$list->isEmpty()) {
            $list->load(['Member', 'Distribution']);
        }

        $this->assign($list->toArray());
        return $this->fetch_view();
    }

    /**
     * 退款订单列表
     * @return mixed
     * @throws Exception
     */
    public function refund_index()
    {
        $where = $this->search('order_sn', '输入需查询的订单号');

        $where['del']           = false;
        $where['refund_status'] = OrdersShopModel::REFUND_STATUS_APPLY;

        $order = $this->sort_order(OrdersShopModel::getTableFields(), 'order_time', 'desc');

        $this->export($where, $order);

        $list = OrdersShopModel::page_list($where, $order);
        if (!$list->isEmpty()) {
            $list->load(['Member']);
        }

        $this->assign($list->toArray());
        return $this->fetch_view();
    }

    /**
     * 退款成功订单列表
     * @return mixed
     * @throws Exception
     */
    public function refund_success_index()
    {
        $where = $this->search('order_sn', '输入需查询的订单号');

        $where['del']           = false;
        $where['refund_status'] = ['in', [OrdersShopModel::REFUND_STATUS_SUCCESS, OrdersShopModel::REFUND_STATUS_FINISH]];

        $order = $this->sort_order(OrdersShopModel::getTableFields(), 'order_time', 'desc');

        $this->export($where, $order);

        $list = OrdersShopModel::page_list($where, $order);
        if (!$list->isEmpty()) {
            $list->load(['Member']);
        }

        $this->assign($list->toArray());
        return $this->fetch_view();
    }

    /**
     * 退款审核
     * @param $id
     * @return void
     * @throws Exception
     */
    public function refund_examine($id)
    {
        $this->is_ajax OR $this->error('请求错误！');

        $where['order_id']      = $id;
        $where['del']           = false;
        $where['refund_status'] = OrdersShopModel::REFUND_STATUS_APPLY;

        $data_info = OrdersShopModel::get($where);
        empty($data_info) AND $this->error('数据已删除！');

        $data_info->order_refund(boolval(input('status', '')));

        $this->success('审核完成！', $this->http_referer ?: $this->return_url());
    }

    /**
     * 数据导出
     * @param $where
     * @param $order
     * @throws Exception
     */
    private function export($where, $order)
    {
        $export = input('export', false);

        if (!$export) {
            $this->assign('export', true);
            return;
        }

        $list = OrdersShopModel::all_list([], $where, $order);
        if (!$list->isEmpty()) {
            $list->each(
                function ($item) {
                    /** @var OrdersShopModel $item */
                    $address =  $item->getAttr('address');
                    $item->setAttr('payment', $this->param['payment'][$item->getAttr('payment_id')] ?? '未支付');
                    $item->setAttr('status', $this->param['status'][$item->getAttr('status')] ?? '');
                    $item->setAttr('order_time', date('Y-m-d H:i:s', $item->getAttr('order_time')));
                    $item->setAttr('member_tel', $address['mobile']);
                    $item->setAttr('member_realname',$address['consignee']);
                    $item->setAttr('address', $address['province'].$address['city'].$address['district'].$address['address']);
                    $item->setAttr('amount', $item->getAttr('amount'));
                }
            );
        }

        $title = [
            'order_sn'        => '订单号',
            'transaction_sn'  => '支付单号',
            'member_tel'      => '用户手机号',
            'member_realname' => '用户姓名',
            'address'         => '收货地址',
            'amount'          => '订单价格',
            'payment'         => '支付方式',
            'status'          => '状态',
            'order_time'      => '下单时间',
        ];

        $this->export_excel('产品订单', $title, $list->toArray());
    }
}
