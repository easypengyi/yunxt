<?php

namespace app\admin\controller;

use Exception;
use app\common\controller\AdminController;
use app\common\model\Column as ColumnModel;

/**
 * 栏目 模块
 */
class Column extends AdminController
{
    /**
     * 初始化方法
     * @throws Exception
     */
    public function _initialize()
    {
        parent::_initialize();

        $this->param['type']     = ColumnModel::type_group();
        $this->param['position'] = ColumnModel::position_group();
    }

    /**
     * 列表
     * @return mixed
     * @throws Exception
     */
    public function index()
    {
        $where = $this->search('name', '输入需要查询的名称');

        $where['del'] = false;

        $order = $this->sort_order(ColumnModel::getTableFields(), 'sort', 'asc');

        $list = ColumnModel::page_list($where, $order);

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
            $data_info = ColumnModel::create($data);
            empty($data_info) AND $this->error('信息新增失败！');
            $this->cache_clear();
            $this->success('信息新增成功！', input('return_url', $return_url));
        }

        $this->assign('return_url', $this->http_referer ?: $return_url);
        return $this->fetch_view('edit', ['type']);
    }

    /**
     * 编辑
     * @param $id
     * @return mixed
     * @throws Exception
     */
    public function edit($id)
    {
        $data_info = ColumnModel::get($id);
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
        return $this->fetch_view('edit', ['id', 'type']);
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

        $data_info = ColumnModel::get($id);
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

        $result = ColumnModel::where([ColumnModel::primary_key() => $id])->update(['del' => true]);
        $result OR $this->error('删除失败！');
        $this->cache_clear();
        $this->success('删除成功！', $this->http_referer ?: $this->return_url());
    }

    /**
     * 数据处理
     * @param ColumnModel $data_info
     * @return mixed
     * @throws Exception
     */
    private function edit_data($data_info = null)
    {
        $data['type'] = input('type', '');
        array_key_exists($data['type'], ColumnModel::type_group()) OR $this->error('类型不存在！');

        $data['position'] = input('position', '');
        array_key_exists($data['position'], ColumnModel::position_group()) OR $this->error('位置不存在！');

        $data['name']   = input('name', '');
        $data['sort']   = intval(input('sort', 50));
        $data['enable'] = boolval(input('enable', false));

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
