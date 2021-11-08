<?php

namespace app\admin\controller;

use think\Db;
use Exception;
use app\common\model\Admin as AdminModel;
use app\common\controller\AdminController;
use app\common\model\AdminGroupRelation as AdminGroupRelationModel;

/**
 * 管理员 模块
 */
class Admin extends AdminController
{
    protected $primary_key = '';

    /**
     * 初始化方法
     * @return void
     * @throws Exception
     */
    public function _initialize()
    {
        parent::_initialize();
        $this->primary_key = AdminModel::primary_key();
    }

    /**
     * 列表
     * @return mixed
     * @throws Exception
     */
    public function index()
    {
        $where = $this->search('admin_username', '输入需要查询的用户名');

        $where['del'] = false;

        $order = $this->sort_order(AdminModel::getTableFields(), $this->primary_key, 'asc');

        $list = AdminModel::page_list($where, $order);

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
            $data     = $this->edit_data();
            $group_id = $this->edit_group_data();
            try {
                Db::startTrans();
                $data_info = AdminModel::create($data);
                $this->edit_group($data_info, $group_id);
                Db::commit();
            } catch (Exception $e) {
                Db::rollback();
                $this->error('信息新增失败！');
            }
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
        $data_info = AdminModel::get($id);
        empty($data_info) AND $this->error('数据已删除！');

        $return_url = $this->return_url();
        if ($this->is_ajax) {
            $data     = $this->edit_data($data_info);
            $group_id = $this->edit_group_data();
            try {
                Db::startTrans();
                $data_info->save($data);
                $this->edit_group($data_info, $group_id);
                Db::commit();
            } catch (Exception $e) {
                Db::rollback();
                $this->error('信息修改失败！');
            }
            $this->cache_clear();
            $this->success('信息修改成功！', input('return_url', $return_url));
        }

        $this->assign('return_url', $this->http_referer ?: $return_url);
        $this->assign('data_info', $data_info);
        $this->assign('edit', true);
        return $this->fetch_view('edit', ['id']);
    }

    /**
     * 当前管理员信息
     * @return mixed
     * @throws Exception
     */
    public function profile()
    {
        if ($this->is_ajax) {
            $data_info = AdminModel::get($this->user[$this->primary_key]);
            $data      = $this->edit_data($data_info);
            $data_info->save($data);
            $this->cache_clear();
            $this->success('信息修改成功！');
        }

        $this->assign('data_info', $this->user);
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
        $this->user['admin_id'] == $id AND $this->error('禁止变更当前账户的启用状态！');

        $data_info = AdminModel::get($id);
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
        $this->user['admin_id'] == $id AND $this->error('禁止删除当前账户！');

        $result = AdminModel::where([$this->primary_key => $id])->update(['del' => true]);
        $result OR $this->error('删除失败！');
        $this->cache_clear();
        $this->success('删除成功！', $this->http_referer ?: $this->return_url());
    }

    /**
     * 数据处理
     * @param AdminModel $data_info
     * @return mixed
     */
    private function edit_data($data_info = null)
    {
        $data['admin_pwd']      = trim(input('admin_pwd', ''));
        $data['admin_email']    = input('admin_email', '');
        $data['admin_tel']      = input('admin_tel', '');
        $data['admin_realname'] = trim(input('admin_realname', ''));

        if (is_null($data_info)) {
            $data['admin_username'] = trim(input('admin_username', ''));
            $data['enable']         = boolval(input('enable', true));
            $data['create_time']    = time();
        } else {
            if (empty($data['admin_pwd'])) {
                unset($data['admin_pwd']);
            }
            $old_pwd = trim(input('old_pwd', ''));
            $new_pwd = trim(input('new_pwd', ''));

            if (!empty($old_pwd) && !empty($new_pwd)) {
                $result = $data_info->password_check($old_pwd);
                $result OR $this->error('原登录密码错误！');
                $data['admin_pwd'] = $new_pwd;
            }
        }

        return $data;
    }

    /**
     * 用户组数据处理
     * @return array
     */
    private function edit_group_data()
    {
        $group_id = input('group_id/a');
        $group_id = array_filter($group_id);
        empty($group_id) AND $this->error('请选择用户组！');

        return $group_id;
    }

    /**
     * 用户组编辑
     * @param AdminModel $data_info
     * @param array      $group_id
     * @return bool
     * @throws Exception
     */
    private function edit_group($data_info, $group_id)
    {
        return AdminGroupRelationModel::bind_group($group_id, $data_info->getAttr($this->primary_key));
    }

    /**
     * 缓存清理
     * @return void
     */
    private function cache_clear()
    {
    }
}
