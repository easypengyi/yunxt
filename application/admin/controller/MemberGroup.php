<?php

namespace app\admin\controller;

use Exception;
use think\Config;
use app\common\controller\AdminController;
use app\common\model\MemberGroup as MemberGroupModel;

/**
 * 会员用户组 模块
 */
class MemberGroup extends AdminController
{
    private $default_group = [];

    /**
     * 初始化方法
     * @return void
     * @throws Exception
     */
    public function _initialize()
    {
        parent::_initialize();
        $this->default_group = Config::get('member_rule.default_group');
        $this->assign('default_group', $this->default_group);
    }

    /**
     * 列表
     * @return mixed
     * @throws Exception
     */
    public function index()
    {

        $order = $this->sort_order(MemberGroupModel::getTableFields(), 'create_time', 'asc');

        $list = MemberGroupModel::page_list([], $order);

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
            $data_info = MemberGroupModel::create($data);
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
        in_array($id, $this->default_group) AND $this->error('默认会员组无法编辑！');

        $data_info = MemberGroupModel::get($id);
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
     * 启用状态变更
     * @param $id
     * @return void
     * @throws Exception
     */
    public function change_enable($id)
    {
        $this->is_ajax OR $this->error('请求错误！');
        in_array($id, $this->default_group) AND $this->error('默认会员组禁止关闭！');

        $data_info = MemberGroupModel::get($id);
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
        in_array($id, $this->default_group) AND $this->error('默认会员组禁止删除！');

        $result = MemberGroupModel::where([MemberGroupModel::primary_key() => $id])->delete();
        $result OR $this->error('删除失败！');
        $this->cache_clear();
        $this->success('删除成功！', $this->http_referer ?: $this->return_url());
    }

    /**
     * 数据处理
     * @param MemberGroupModel $data_info
     * @return mixed
     */
    private function edit_data($data_info = null)
    {
        $data['group_name'] = input('group_name', '');
        $data['num']     =    input('num', 0);
        $data['enable']     = boolval(input('enable', true));

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
        MemberGroupModel::cacheClear();
    }
}
