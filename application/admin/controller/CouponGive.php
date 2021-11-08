<?php

namespace app\admin\controller;

use Exception;
use think\Queue;
use app\common\job\CouponCallback;
use app\common\controller\AdminController;
use app\common\model\CouponGive as CouponGiveModel;

/**
 * 优惠券赠送 模块
 */
class CouponGive extends AdminController
{
    protected $template_id;

    /**
     * 初始化方法
     * @return void
     * @throws Exception
     */
    public function _initialize()
    {
        parent::_initialize();

        $this->param['status'] = CouponGiveModel::status_array();

        $this->template_id = input('template_id', '');
    }

    /**
     * 列表
     * @return mixed
     * @throws Exception
     */
    public function index()
    {
        $where = $this->search();

        $where['template_id'] = $this->template_id;

        $order = $this->sort_order(CouponGiveModel::getTableFields(), 'create_time', 'desc');

        $list = CouponGiveModel::page_list($where, $order);
        if (!$list->isEmpty()) {
            $list->load(['Admin']);
        }

        $this->assign($list->toArray());
        $this->assign('return_url', folder_url('CouponTemplate/index'));
        return $this->fetch_view('', ['template_id']);
    }

    /**
     * 添加
     * @return mixed
     * @throws Exception
     */
    public function add()
    {
        $return_url = $this->return_url();
        if ($this->is_ajax) {
            $data      = $this->edit_data();
            $data_info = CouponGiveModel::create($data);
            empty($data_info) AND $this->error('信息新增失败！');

            Queue::push(CouponCallback::class, $data_info);
            $this->cache_clear();
            $this->success('优惠券赠送已添加，请等待处理！', input('return_url', $return_url));
        }

        $this->assign('return_url', $this->http_referer ?: $return_url);
        return $this->fetch_view('edit', ['template_id']);
    }

    /**
     * 默认返回链接
     * @return string
     */
    protected function return_url()
    {
        return controller_url('index', ['template_id' => $this->template_id]);
    }

    /**
     * 数据处理
     * @param CouponGiveModel $data_info
     * @return mixed
     * @throws Exception
     */
    private function edit_data($data_info = null)
    {
        $data['template_id'] = $this->template_id;
        $data['member_all']  = boolval(input('member_all', 0));
        if ($data['member_all']) {
            $data['member_ids'] = [];
        } else {
            $data['member_ids'] = array_filter(input('member_id/a', []));
        }
        $data['admin_id'] = $this->user['admin_id'];

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
}
