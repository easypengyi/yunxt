<?php

namespace app\admin\controller;

use Tree;
use Exception;
use app\common\controller\AdminController;
use app\common\model\AdminRule as AdminRuleModel;

/**
 * 管理员权限菜单 模块
 */
class AdminRule extends AdminController
{
    /**
     * 初始化方法
     * @return void
     * @throws Exception
     */
    public function _initialize()
    {
        parent::_initialize();
    }

    /**
     * 列表
     * @return mixed
     * @throws Exception
     */
    public function index()
    {
        return $this->fetch_view();
    }

    /**
     * 添加
     * @param $pid
     * @return mixed
     * @throws Exception
     */
    public function add($pid = 0)
    {
        $return_url = $this->return_url();
        if ($this->is_ajax) {
            $data      = $this->edit_data();
            $data_info = AdminRuleModel::create($data);
            empty($data_info) AND $this->error('信息新增失败！');
            $this->cache_clear();
            $this->success('信息新增成功！', input('return_url', $return_url));
        }

        $this->assign('pid', $pid);
        $this->assign('return_url', $this->http_referer ?: $return_url);
        return $this->fetch_view('edit', ['pid']);
    }

    /**
     * 编辑
     * @param $id
     * @return mixed
     * @throws Exception
     */
    public function edit($id)
    {
        $data_info = AdminRuleModel::get($id);
        empty($data_info) AND $this->error('数据已删除！');

        $return_url = $this->return_url();
        if ($this->is_ajax) {
            $old_pid   = $data_info->getAttr('pid');
            $old_level = $data_info->getAttr('level');

            $data = $this->edit_data($data_info);
            $data_info->save($data);

            //更新子孙级菜单的level
            if ($old_pid != $data_info->getAttr('pid') && $old_level != $data_info->getAttr('level')) {
                $list = AdminRuleModel::all_auth_rule();

                $tree = Tree::instance();
                $tree->init($list, ['parentid' => 'pid']);
                $child_array = $tree->get_childs($id, true, false);

                if (!empty($child_array)) {
                    $level = $data_info->getAttr('level');
                    $where = [AdminRuleModel::primary_key() => ['in', $child_array]];
                    if ($level > $old_level) {
                        AdminRuleModel::where($where)->setInc('level', $level - $old_level);
                    } else {
                        AdminRuleModel::where($where)->setDec('level', $old_level - $level);
                    }
                }
            }

            $this->cache_clear();
            $this->success('信息修改成功！', input('return_url', $return_url));
        }

        $this->assign('pid', $data_info->getAttr('pid'));
        $this->assign('return_url', $this->http_referer ?: $return_url);
        $this->assign('data_info', $data_info);
        $this->assign('edit', true);
        return $this->fetch_view('edit', ['id']);
    }

    /**
     * 复制
     * @param $id
     * @return mixed
     * @throws Exception
     */
    public function copy($id)
    {
        $return_url = $this->return_url();
        if ($this->is_ajax) {
            $data      = $this->edit_data();
            $data_info = AdminRuleModel::create($data);
            empty($data_info) AND $this->error('信息新增失败！');
            $this->cache_clear();
            $this->success('信息新增成功！', input('return_url', $return_url));
        }

        $data_info = AdminRuleModel::get($id);
        empty($data_info) AND $this->error('数据已删除！');

        $this->assign('pid', $data_info->getAttr('pid'));
        $this->assign('return_url', $this->http_referer ?: $return_url);
        $this->assign('data_info', $data_info);
        return $this->fetch_view('edit');
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

        $data_info = AdminRuleModel::get($id);
        empty($data_info) AND $this->error('数据不存在！');
        $status = !$data_info->getAttr('enable');
        $result = $data_info->save(['enable' => $status]);
        $result OR $this->error('操作失败！');
        $this->cache_clear();
        $this->success_result(['status' => $status]);
    }

    /**
     * 显示状态变更
     * @param $id
     * @return void
     * @throws Exception
     */
    public function change_display($id)
    {
        $this->is_ajax OR $this->error('请求错误！');

        $data_info = AdminRuleModel::get($id);
        empty($data_info) AND $this->error('数据不存在！');
        $status = !$data_info->getAttr('display');
        $result = $data_info->save(['display' => $status]);
        $result OR $this->error('操作失败！');
        $this->cache_clear();
        $this->success_result(['status' => $status]);
    }

    /**
     * 权限分配状态变更
     * @param $id
     * @return void
     * @throws Exception
     */
    public function change_unassign($id)
    {
        $this->is_ajax OR $this->error('请求错误！');

        $data_info = AdminRuleModel::get($id);
        empty($data_info) AND $this->error('数据不存在！');
        $status = !$data_info->getAttr('unassign');
        $result = $data_info->save(['unassign' => $status]);
        $result OR $this->error('操作失败！');
        $this->cache_clear();
        $this->success_result(['status' => $status]);
    }

    /**
     * 检测状态变更
     * @param $id
     * @return void
     * @throws Exception
     */
    public function change_notcheck($id)
    {
        $this->is_ajax OR $this->error('请求错误！');

        $data_info = AdminRuleModel::get($id);
        empty($data_info) AND $this->error('数据不存在！');
        $status = !$data_info->getAttr('notcheck');
        $result = $data_info->save(['notcheck' => $status]);
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

        $list = AdminRuleModel::all_auth_rule();

        $tree = Tree::instance();
        $tree->init($list, ['parentid' => 'pid']);
        $child_array = $tree->get_childs($id, true, true);
        empty($child_array) AND $this->error('删除失败！');

        $result = AdminRuleModel::where([AdminRuleModel::primary_key() => ['in', $child_array]])->delete();
        $result OR $this->error('删除失败！');
        $this->cache_clear();
        $this->success('删除成功！', $this->http_referer ?: $this->return_url());
    }

    /**
     * 数据处理
     * @param AdminRuleModel $data_info
     * @return mixed
     * @throws Exception
     */
    private function edit_data($data_info = null)
    {
        $pid = input('pid', 0);
        if (empty($pid)) {
            $level = 1;
        } else {
            $parent = AdminRuleModel::get($pid);
            empty($parent) AND $this->error('添加失败');
            $level = $parent['level'] + 1;
        }

        $data['pid']      = $pid;
        $data['level']    = $level;
        $data['title']    = input('title', '');
        $data['name']     = input('name', '');
        $data['enable']   = boolval(input('enable', false));
        $data['display']  = boolval(input('display', false));
        $data['notcheck'] = boolval(input('notcheck', false));
        $data['unassign'] = boolval(input('unassign', false));
        $data['sort']     = intval(input('sort', 50));
        $data['css']      = input('css', '');

        if ($level == 1) {
            $data['name'] = '';
        } else {
            $data['css'] = '';
        }

        if (is_null($data_info)) {
            $data['create_time'] = time();
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
        AdminRuleModel::cacheClear();
    }
}
