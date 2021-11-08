<?php

namespace app\admin\controller;

use app\common\model\Member;
use app\common\model\MemberCommission;
use Exception;
use helper\StrHelper;
use helper\TimeHelper;
use app\common\controller\AdminController;
use app\common\model\ActivityOrderShop as ActivityOrderShopModel;
use think\Db;

/**
 * HPV订单 模块
 */
class Order3 extends AdminController
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
        $this->param['status']        = ActivityOrderShopModel::order_status_array();
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
        $where['status'] = ['not in', [ActivityOrderShopModel::STATUS_INVALID]];
        empty($this->search['status']) OR $where['status'] = $this->search['status'];

        $where['del']        = false;
        $where['order_type'] = ActivityOrderShopModel::TYPE_BASE;

        $order = $this->sort_order(ActivityOrderShopModel::getTableFields(), 'order_time', 'desc');

        $this->export($where, $order);

        $list = ActivityOrderShopModel::page_list($where, $order);
        if (!$list->isEmpty()) {
            $list->load(['Member']);
        }

        $this->assign('total_money', ActivityOrderShopModel::where($where)->sum('amount'));
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
        $where['status']        = ActivityOrderShopModel::STATUS_ALREADY_PAY;
        $where['refund_status'] = ['not in', [ActivityOrderShopModel::REFUND_STATUS_APPLY]];

        $order = $this->sort_order(ActivityOrderShopModel::getTableFields(), 'order_time', 'desc');

        $this->export($where, $order);

        $list = ActivityOrderShopModel::page_list($where, $order);
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
        $where['status']        = ActivityOrderShopModel::STATUS_CHECKING;

        $data_info = ActivityOrderShopModel::get($where);
        empty($data_info) AND $this->error('数据不存在！');

        $data   = [
            'status'          => ActivityOrderShopModel::STATUS_FINISH,
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
     * 已配送订单列表
     * @return mixed
     * @throws Exception
     */
    public function distribution_index()
    {
        $where = $this->search('order_sn', '输入需查询的订单号');

        $where['del']    = false;
        $where['status'] = ActivityOrderShopModel::STATUS_ALREADY_DISTRIBUTION;

        $order = $this->sort_order(ActivityOrderShopModel::getTableFields(), 'order_time', 'desc');

        $this->export($where, $order);

        $list = ActivityOrderShopModel::page_list($where, $order);
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
        $where['status']        = ActivityOrderShopModel::STATUS_CHECKING;
        $where['refund_status'] = ['not in', [ActivityOrderShopModel::REFUND_STATUS_APPLY]];

        $data_info = ActivityOrderShopModel::get($where);
        empty($data_info) AND $this->error('数据不存在！');

        $data   = [
            'refund_status' => ActivityOrderShopModel::REFUND_STATUS_NO,
            'status'        => ActivityOrderShopModel::STATUS_FINISH,
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
        $where['status'] = ActivityOrderShopModel::STATUS_CHECKING;

        $order = $this->sort_order(ActivityOrderShopModel::getTableFields(), 'order_time', 'desc');

        $this->export($where, $order);

        $list = ActivityOrderShopModel::page_list($where, $order);
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
        $where['status'] = ['in', [ActivityOrderShopModel::STATUS_FINISH]];

        $order = $this->sort_order(ActivityOrderShopModel::getTableFields(), 'order_time', 'desc');

        $this->export($where, $order);

        $list = ActivityOrderShopModel::page_list($where, $order);
        if (!$list->isEmpty()) {
            $list->load(['Member']);
        }

        $this->assign($list->toArray());
        return $this->fetch_view();
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

        $list = OrderssShopModel::all_list([], $where, $order);
        if (!$list->isEmpty()) {
            $list->load([]);
            $list->each(
                function ($item) {
                    /** @var OrderssShopModel $item */
                    $item->setAttr('payment', $this->param['payment'][$item->getAttr('payment_id')] ?? '未支付');
                    $item->setAttr('status', $this->param['status'][$item->getAttr('status')] ?? '');
                    $item->setAttr('order_time', date('Y-m-d H:i:s', $item->getAttr('order_time')));
                    $item->setAttr('mobile', $item->getAttr('mobile'));
                    $item->setAttr('shop_name', $item->getAttr('shop_name'));
                    $item->setAttr('nick_name', $item->getAttr('nick_name'));
                    $item->setAttr('amount', $item->getAttr('amount'));
                }
            );
        }

        $title = [
            'order_sn'        => '订单号',
            'transaction_sn'  => '支付单号',
            'shop_name'       => '公司名称',
            'mobile'          => '用户手机号',
            'nick_name'       => '用户姓名',
            'amount'          => '订单价格',
            'payment'         => '支付方式',
            'status'          => '状态',
            'order_time'      => '下单时间',
        ];

        $this->export_excel('中心授权订单', $title, $list->toArray());
    }
}
