<?php

namespace app\admin\controller;

use app\common\model\Configure;
use app\common\model\Member;
use app\common\model\MemberCommission;
use app\common\model\Message;
use app\common\model\OrderShop as OrderShopModel;
use app\common\model\Report as ReportModel;
use Exception;
use helper\StrHelper;
use helper\TimeHelper;
use app\common\controller\AdminController;
use app\common\model\OrderssShop as OrderssShopModel;
use think\Db;
use tool\UploadTool;

/**
 * 健康大使订单 模块
 */
class Order2 extends AdminController
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
        $this->param['status']        = OrderssShopModel::order_status_array();
    }

    /**
     * 列表
     * @return mixed
     * @throws Exception
     */
    public function index()
    {
        $where = $this->search('order_sn|blood_number', '输入需查询的订单号/血液编号');

        $this->search['date']   = input('date', '');
        $this->search['status'] = input('status', '');

        $range_time = TimeHelper::range_time($this->search['date']);
        empty($range_time) OR $where['order_time'] = ['between', $range_time];

        empty($this->search['status']) OR $where['status'] = $this->search['status'];

        $where['del']        = false;
        $where['order_type'] = OrderssShopModel::TYPE_BASE;

        $order = $this->sort_order(OrderssShopModel::getTableFields(), 'order_time', 'desc');

        $this->export($where, $order);

        $list = OrderssShopModel::page_list($where, $order);
        if (!$list->isEmpty()) {
            $list->load(['Member']);
        }

        $this->assign('total_money', OrderssShopModel::where($where)->sum('amount'));
        $this->assign($list->toArray());
        return $this->fetch_view();
    }


    /**
     * 检测中订单列表
     * @return mixed
     * @throws Exception
     */
    public function check_index()
    {
        $where = $this->search('order_sn|blood_number', '输入需查询的订单号/血液编号');

        $where['del']    = false;
        $where['status'] = OrderssShopModel::STATUS_CHECKING;

        $order = $this->sort_order(OrderssShopModel::getTableFields(), 'order_time', 'desc');

        $this->export($where, $order);

        $list = OrderssShopModel::page_list($where, $order);
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
        $where = $this->search('order_sn|blood_number', '输入需查询的订单号/血液编号');

        $where['del']    = false;
        $where['status'] = OrderssShopModel::STATUS_FINISH;

        $order = $this->sort_order(OrderssShopModel::getTableFields(), 'order_time', 'desc');

        $this->export($where, $order);

        $list = OrderssShopModel::page_list($where, $order);
        if (!$list->isEmpty()) {
            $list->load(['Member','Report']);
        }

        $this->assign($list->toArray());
        return $this->fetch_view();
    }




    /**
     * 添加
     * @return mixed
     * @throws Exception
     */
    public function add()
    {
        $order_id = input('order_id', 0);
        empty($order_id) AND $this->error('订单ID不为空！');

        $return_url = $this->return_url();
        if ($this->is_ajax) {
            $data             = $this->edit_data();
            $data['order_id'] = $order_id;

            try {
                Db::startTrans();
                $data_info = ReportModel::create($data);
                OrderssShopModel::order_finish($data_info->getAttr('order_id'));
                Db::commit();
            } catch (Exception $e) {
                Db::rollback();
                $this->error('信息新增失败！');
            }
            $order = OrderssShopModel::get($data_info->getAttr('order_id'));
            $this->cache_clear();
            $this->success('信息新增成功！', input('return_url', $return_url));
        }

        $this->assign('return_url', $this->http_referer ?: $return_url);
        return $this->fetch_view('edit');
    }


    /**
     * 重新上传
     * @return mixed
     * @throws Exception
     */
    public function update()
    {
        $order_id = input('order_id', 0);
        empty($order_id) AND $this->error('订单ID不为空！');

        $return_url = $this->return_url();
        if ($this->is_ajax) {
            $data             = $this->edit_data();
            $data['order_id'] = $order_id;
            try {
                Db::startTrans();
                $data_info = ReportModel::update($data);
                OrderssShopModel::order_finish($data_info->getAttr('order_id'));
                Db::commit();
            } catch (Exception $e) {
                Db::rollback();
                $this->error('信息新增失败！');
            }
           OrderssShopModel::get($data_info->getAttr('order_id'));
            $this->cache_clear();
            $this->success('信息新增成功！', input('return_url', $return_url));
        }

        $this->assign('return_url', $this->http_referer ?: $return_url);
        return $this->fetch_view('edit');
    }

    /**
     * 数据处理
     * @param ReportModel $data_info
     * @return mixed
     * @throws Exception
     */
    private function edit_data($data_info = null)
    {
        if ($this->request->file()) {
            $image = UploadTool::instance()->upload_file(true, ['file']);
            $image['status'] OR $this->error($image['message']);
            $data['file_id'] = $image['data']['file_id'];
        } else {
            is_null($data_info) AND $this->error('请上传文件!');
        }

        $data['name']   = input('name', '');
        $data['remark'] = input('remark', '');

        if (is_null($data_info)) {

        } else {

        }

        return $data;
    }


    /**
     * 缓存清理
     * @return void
     */
    private function cache_clear()
    {
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
