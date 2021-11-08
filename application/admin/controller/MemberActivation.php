<?php

namespace app\admin\controller;

use Exception;
use tool\PaymentTool;
use app\common\controller\AdminController;
use app\common\model\MemberActivation as MemberActivationModel;

/**
 * 开通会员 模块
 */
class MemberActivation extends AdminController
{
    /**
     * 初始化方法
     * @return void
     * @throws Exception
     */
    public function _initialize()
    {
        parent::_initialize();
        $this->param['payment'] = PaymentTool::instance()->payment_array();
        $this->param['status']  = MemberActivationModel::order_status_array();
    }

    /**
     * 列表
     * @return mixed
     * @throws Exception
     */
    public function index()
    {
        $where = $this->search('order_sn', '输入需查询的订单号');

        $this->search['status'] = input('status', '');

        $where['del']    = false;
        $where['status'] = ['not in', [MemberActivationModel::STATUS_INVALID, MemberActivationModel::STATUS_WAIT_PAY]];
        empty($this->search['status']) OR $where['status'] = $this->search['status'];
        
        $order = $this->sort_order(MemberActivationModel::getTableFields(), 'order_time', 'desc');

        $this->export($where, $order);

        $list = MemberActivationModel::page_list($where, $order);
        if (!$list->isEmpty()) {
            $list->load(['Member', 'Distribution']);
        }

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

        $where['del']    = false;
        $where['status'] = MemberActivationModel::STATUS_ALREADY_PAY;

        $order = $this->sort_order(MemberActivationModel::getTableFields(), 'order_time', 'desc');

        $this->export($where, $order);

        $list = MemberActivationModel::page_list($where, $order);
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
        $where['status']        = MemberActivationModel::STATUS_ALREADY_PAY;
        $where['refund_status'] = ['not in', [MemberActivationModel::REFUND_STATUS_APPLY]];

        $data_info = MemberActivationModel::get($where);
        empty($data_info) AND $this->error('数据不存在！');

        $data   = [
            'refund_status'   => MemberActivationModel::REFUND_STATUS_NO,
            'status'          => MemberActivationModel::STATUS_ALREADY_DISTRIBUTION,
            'courier_sn'      => input('courier_sn', ''),
            'distribution_id' => input('distribution_id', 0),
            'box_code'        => input('box_code', ''),
            'delivery_time'   => time(),
        ];
        $result = $data_info->save($data);
        $result OR $this->error('发货失败！');

        $this->success('发货完成！', $this->http_referer ?: $this->return_url());
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
        $where['status'] = MemberActivationModel::STATUS_ALREADY_DISTRIBUTION;

        $order = $this->sort_order(MemberActivationModel::getTableFields(), 'order_time', 'desc');

        $this->export($where, $order);

        $list = MemberActivationModel::page_list($where, $order);
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

        $where['order_id'] = $id;
        $where['del']      = false;
        $where['status']   = MemberActivationModel::STATUS_ALREADY_DISTRIBUTION;

        $data_info = MemberActivationModel::get($where);
        empty($data_info) AND $this->error('数据不存在！');

        $data   = ['status' => MemberActivationModel::STATUS_FINISH];
        $result = $data_info->save($data);
        $result OR $this->error('收货失败！');

        $this->success('收货完成！', $this->http_referer ?: $this->return_url());
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

        $list = MemberActivationModel::all_list([], $where, $order);
        if (!$list->isEmpty()) {
            $list->load(['Member']);
            $list->each(
                function ($item) {
                    /** @var MemberActivationModel $item */
                    $member = $item->getAttr('member');
                    $item->setAttr('payment', $this->param['payment'][$item->getAttr('payment_id')]);
                    $item->setAttr('order_time', date('Y-m-d H:i:s', $item->getAttr('order_time')));
                    $item->setAttr('member_tel', $member['member_tel']);
                    $item->setAttr('member_nickname', $member['member_nickname']);
                }
            );
        }

        $title = [
            'order_sn'        => '订单号',
            'amount'          => '金额',
            'payment'         => '支付方式',
            'transaction_sn'  => '支付单号',
            'member_tel'      => '账号',
            'member_nickname' => '昵称',
            'order_time'      => '时间',
        ];

        $this->export_excel('会员开通订单', $title, $list->toArray());
    }
}
