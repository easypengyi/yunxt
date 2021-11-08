<?php

namespace app\admin\controller;

use Exception;
use app\common\controller\AdminController;
use app\common\model\LogWeb as LogWebModel;

/**
 * 访问日志 模块
 */
class LogWeb extends AdminController
{
    /**
     * 初始化方法
     * @return void
     * @throws Exception
     */
    public function _initialize()
    {
        parent::_initialize();
        $this->param['type'] = LogWebModel::type_array();
    }

    /**
     * 列表
     * @return mixed
     * @throws Exception
     */
    public function index()
    {
        $where = $this->search('url', '输入需要查询的操作地址');

        $order = $this->sort_order(LogWebModel::getTableFields(), 'create_time', 'desc');

        $list = LogWebModel::page_list($where, $order);
        if (!$list->isEmpty()) {
            $list->load(['Operator']);
            $list->append(['operator_name']);
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

        $result = LogWebModel::where([LogWebModel::primary_key() => $id])->delete();
        $result OR $this->error('删除失败！');
        $this->success('删除成功！', $this->http_referer ?: $this->return_url());
    }

    /**
     * 清空
     * @return void
     * @throws Exception
     */
    public function drop()
    {
        $result = LogWebModel::where([LogWebModel::primary_key() => ['>', 0]])->delete();
        $result OR $this->error('清空失败！');
        $this->success('清空成功！', $this->http_referer ?: $this->return_url());
    }
}
