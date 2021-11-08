<?php

namespace app\admin\controller;

use app\common\model\Message as MessageModel;
use Exception;
use tool\PaymentTool;
use app\common\controller\AdminController;
use app\common\model\OrderWithdrawals as OrderWithdrawalsModel;

/**
 * 提现 模块
 */
class OrderWithdrawals extends AdminController
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
        $this->param['status']  = OrderWithdrawalsModel::order_status_array();
    }

    /**
     * 列表
     * @return mixed
     * @throws Exception
     */
    public function index()
    {
        $where = $this->search('order_sn', '输入需查询的订单号');

        $where['del']    = false;
        $where['status'] = ['not in', [OrderWithdrawalsModel::STATUS_INVALID]];

        $order = $this->sort_order(OrderWithdrawalsModel::getTableFields(), 'order_time', 'desc');

        $this->export($where, $order);

        $list = OrderWithdrawalsModel::page_list($where, $order);
        if (!$list->isEmpty()) {
            $list->load(['Member']);
        }

        $this->assign($list->toArray());
        return $this->fetch_view();
    }

    /**
     * 待审核提现 by shiqiren
     * @return mixed
     * @throws Exception
     */
    public function wait()
    {
        $where = $this->search('order_sn', '输入需查询的订单号');

        $where['del']    = false;
        $where['status'] = OrderWithdrawalsModel::STATUS_WAIT_PAY;

        $order = $this->sort_order(OrderWithdrawalsModel::getTableFields(), 'order_time', 'desc');

        $this->export($where, $order);

        $list = OrderWithdrawalsModel::page_list($where, $order);
        if (!$list->isEmpty()) {
            $list->load(['Member']);
        }

        $this->assign($list->toArray());
        return $this->fetch_view();
    }

    /**
     * 已完成提现 by shiqiren
     * @return mixed
     * @throws Exception
     */
    public function finish()
    {
        $where = $this->search('order_sn', '输入需查询的订单号');

        $where['del']    = false;
        $where['status'] = OrderWithdrawalsModel::STATUS_FINISH;

        $order = $this->sort_order(OrderWithdrawalsModel::getTableFields(), 'order_time', 'desc');

        $this->export($where, $order);

        $list = OrderWithdrawalsModel::page_list($where, $order);
        if (!$list->isEmpty()) {
            $list->load(['Member']);
        }

        $this->assign($list->toArray());
        return $this->fetch_view();
    }

    /**
     * 完成操作
     * @param $id
     * @throws Exception
     */
    public function examine($id)
    {
        $this->is_ajax OR $this->error('请求错误！');

        $where['withdrawals_id'] = $id;
        $where['del']            = false;
        $where['status']         = OrderWithdrawalsModel::STATUS_WAIT_PAY;

        $data_info = OrderWithdrawalsModel::get($where);
        empty($data_info) AND $this->error('数据不存在！');

        $remark = input('remark', "");

        $result = $data_info->order_finish($data_info['order_sn'],$remark);
        $result OR $this->error('操作失败！');
        MessageModel::commission_message_readed($id);
        // $this->success('操作完成！', $this->http_referer ?: $this->return_url());
        $this->success('操作完成！', input('return_url', $this->http_referer ?: $this->return_url()));
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

        $list = OrderWithdrawalsModel::all_list([], $where, $order);
        if (!$list->isEmpty()) {
            $list->load(['Member']);
            $list->each(
                function ($item) {
                    /** @var OrderWithdrawalsModel $item */
                    $member = $item->getAttr('member');
                    $item->setAttr('order_time', date('Y-m-d H:i:s', $item->getAttr('order_time')));
                    $item->setAttr('member_tel', $member['member_tel']);
                }
            );
        }

        $title = [
            'order_sn'        => '订单号',
            'amount'          => '金额',
            'service_money'   => '手续费金额',
            'money'           => '提现金额',
            'member_tel'      => '用户手机',
            'account'         => '账号',
            'real_name'       => '真实姓名',
            'order_time'      => '时间',
        ];

        $this->export_excel('提现订单', $title, $list->toArray());
    }
}
