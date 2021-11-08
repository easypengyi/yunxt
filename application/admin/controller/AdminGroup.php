<?php

namespace app\admin\controller;

use Exception;
use think\Config;
use app\common\controller\AdminController;
use app\common\model\AdminRule as AdminRuleModel;
use app\common\model\AdminGroup as AdminGroupModel;

/**
 * 管理员用户组 模块
 */
class AdminGroup extends AdminController
{
    /**
     * 初始化方法
     * @return void
     * @throws Exception
     */
    public function _initialize()
    {
        parent::_initialize();
        $this->param['no_check_group_id'] = Config::get('admin_rule.no_check_group_id');
    }

    /**
     * 列表
     * @return mixed
     * @throws Exception
     */
    public function index()
    {
        $where = $this->search('group_name', '输入需要查询的用户组名');

        $order = $this->sort_order(AdminGroupModel::getTableFields(), 'create_time', 'asc');

        $list = AdminGroupModel::page_list($where, $order);

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
        $return_url = $this->return_url();
        if ($this->is_ajax) {
            $data      = $this->edit_data();
            $data_info = AdminGroupModel::create($data);
            empty($data_info) AND $this->error('信息新增失败！');
            $this->cache_clear();
            $this->success('信息新增成功！', input('return_url', $return_url));
        }

        $this->assign('return_url', $this->http_referer ?: $return_url);
        return $this->fetch_view('edit');
    }

    /**
     * 编辑
     * @param $id
     * @return mixed
     * @throws Exception
     */
    public function edit($id)
    {
        in_array($id, $this->param['no_check_group_id']) AND $this->error('超级管理员禁止编辑！');

        $data_info = AdminGroupModel::get($id);
        empty($data_info) AND $this->error('数据已删除！');

        $return_url = $this->return_url();
        if ($this->is_ajax) {
            $data = $this->edit_data($data_info);
            $data_info->save($data);
            $this->cache_clear();
            $this->success('信息修改成功！', input('return_url', $return_url));
        }

        $this->assign('return_url', $this->http_referer ?: $return_url);
        $this->assign('data_info', $data_info);
        $this->assign('edit', true);
        return $this->fetch_view('edit', ['id']);
    }

    /**
     * 配置规则
     * @param $id
     * @return mixed
     * @throws Exception
     */
    public function access($id)
    {
        in_array($id, $this->param['no_check_group_id']) AND $this->error('超级管理员无需分配权限！');

        $data_info = AdminGroupModel::get($id);
        empty($data_info) AND $this->error('数据已删除！');

        $return_url = $this->return_url();
        if ($this->is_ajax) {
            $data['rules'] = input('rules/a', []);
            $data_info->save($data);
            $this->cache_clear();
            $this->success('配置规则成功！', input('return_url', $return_url));
        }

        $this->assign('return_url', $this->http_referer ?: $return_url);
        $this->assign('data_info', $data_info);
        return $this->fetch_view();
    }

    /**
     * 启用状态变更
     * @param $id
     * @return void
     * @throws Exception
     */
    public function change_enable($id)
    {
        $this->is_ajax OR $this->error('请求错误！');
        in_array($id, $this->param['no_check_group_id']) AND $this->error('超级管理员禁止关闭！');

        $data_info = AdminGroupModel::get($id);
        empty($data_info) AND $this->error('数据不存在！');
        $status = !$data_info->getAttr('enable');
        $result = $data_info->save(['enable' => $status]);
        $result OR $this->error('操作失败！');
        $this->cache_clear();
        $this->success_result(['status' => $status]);
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
        in_array($id, $this->param['no_check_group_id']) AND $this->error('超级管理员无法删除！');

        $result = AdminGroupModel::where([AdminGroupModel::primary_key() => $id])->delete();
        $result OR $this->error('删除失败！');
        $this->cache_clear();
        $this->success('删除成功！', $this->http_referer ?: $this->return_url());
    }

    /**
     * 数据处理
     * @param AdminGroupModel $data_info
     * @return mixed
     */
    private function edit_data($data_info = null)
    {
        $data['group_name'] = input('group_name', '');
        $data['enable']     = boolval(input('enable', false));

        if (is_null($data_info)) {
            $data['create_time'] = time();
            $data['rules']       = [];
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
        AdminGroupModel::cacheClear();
        AdminRuleModel::cacheClear();
    }
}
