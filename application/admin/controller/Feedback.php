<?php

namespace app\admin\controller;

use Exception;
use app\common\Constant;
use app\common\controller\AdminController;
use app\common\model\Feedback as FeedbackModel;

/**
 * 意见反馈 模块
 */
class Feedback extends AdminController
{
    /**
     * 初始化方法
     * @throws Exception
     */
    public function _initialize()
    {
        parent::_initialize();
        $this->param['app_type'] = Constant::client_array();
    }

    /**
     * 列表
     * @return mixed
     * @throws Exception
     */
    public function index()
    {
        $where = $this->search();

        $order = $this->sort_order(FeedbackModel::getTableFields(), 'create_time', 'desc');

        $list = FeedbackModel::page_list($where, $order);

        if (!$list->isEmpty()) {
            $list->load(['Member']);
        }

        $this->assign($list->toArray());
        return $this->fetch_view();
    }

    /**
     * 删除（单个）
     * @param $id
     * @return void
     * @throws Exception
     */
    public function del($id)
    {
        $this->is_ajax OR $this->error('请求错误！');

        $data_info = FeedbackModel::get($id);
        empty($data_info) OR $data_info->delete();
        $this->success('删除成功！', $this->http_referer ?: $this->return_url());
    }
}
