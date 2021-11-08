<?php

namespace app\admin\controller;

use Exception;
use app\common\controller\AdminController;
use app\common\model\Column as ColumnModel;
use app\common\model\Product as ProductModel;
use app\common\model\ColumnItem as ColumnItemModel;
use app\common\model\ProductSeries as ProductSeriesModel;

/**
 * 栏目内容 模块
 */
class ColumnItem extends AdminController
{
    protected $column_id;

    protected $column = null;

    /**
     * 初始化方法
     * @throws Exception
     */
    public function _initialize()
    {
        parent::_initialize();
        $this->column_id = input('column_id', '');

        $this->column = ColumnModel::get($this->column_id);
        empty($this->column) AND $this->error('栏目已删除！');

        $this->param['column_id']  = $this->column_id;
        $this->param['skip']       = ColumnItemModel::skip_array();
        $this->param['image_size'] = ColumnModel::image_size_group();
    }

    /**
     * 列表
     * @return mixed
     * @throws Exception
     */
    public function index()
    {
        $where = $this->search('name', '输入需要查询的名称');

        $where['column_id'] = $this->column_id;
        $where['del']       = false;

        $order = $this->sort_order(ColumnItemModel::getTableFields(), 'sort', 'asc');

        $list = ColumnItemModel::page_list($where, $order);

        $this->assign('return_url', folder_url('Column/index'));
        $this->assign($list->toArray());
        return $this->fetch_view('', ['column_id']);
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
            $data_info = ColumnItemModel::create($data);
            empty($data_info) AND $this->error('信息新增失败！');
            $this->cache_clear();
            $this->success('信息新增成功！', input('return_url', $return_url));
        }

        $this->assign('column_type', $this->column['type']);
        $this->assign('return_url', $this->http_referer ?: $return_url);
        return $this->fetch_view('edit', ['column_id']);
    }

    /**
     * 编辑
     * @param $id
     * @return mixed
     * @throws Exception
     */
    public function edit($id)
    {
        $data_info = ColumnItemModel::get($id);
        empty($data_info) AND $this->error('数据已删除！');

        $return_url = $this->return_url();
        if ($this->is_ajax) {
            $data = $this->edit_data($data_info);
            $data_info->save($data);
            $this->cache_clear();
            $this->success('信息修改成功！', input('return_url', $return_url));
        }

        $this->assign('column_type', $this->column['type']);
        $this->assign('return_url', $this->http_referer ?: $return_url);
        $this->assign('data_info', $data_info);
        $this->assign('edit', true);
        return $this->fetch_view('edit', ['id', 'column_id']);
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

        $data_info = ColumnItemModel::get($id);
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

        $result = ColumnItemModel::where([ColumnItemModel::primary_key() => $id])->update(['del' => true]);
        $result OR $this->error('删除失败！');
        $this->cache_clear();
        $this->success('删除成功！', $this->http_referer ?: $this->return_url());
    }

    /**
     * 默认返回链接
     * @return string
     */
    protected function return_url()
    {
        return controller_url(['index', ['column_id' => $this->column_id]]);
    }

    /**
     * 数据处理
     * @param ColumnItemModel $data_info
     * @return mixed
     * @throws Exception
     */
    private function edit_data($data_info = null)
    {
        $data['column_id'] = $this->column_id;

        if ($this->request->file()) {
            $image = $this->upload_thumb(true, ['image']);
            $image['status'] OR $this->error($image['message']);
            $data['image_id'] = $image['data']['file_id'];
        } else {
            is_null($data_info) AND $this->error('请上传图片!');
        }

        $data['skip'] = trim(input('skip', ''));
        empty($data['skip']) AND $this->error('必须选择跳转方式');

        $data['name']   = input('name', '');
        $data['sort']   = intval(input('sort', 50));
        $data['enable'] = boolval(input('enable', false));

        $data['url']     = '';
        $data['content'] = '';

        switch (input('skip', '')) {
            case ColumnItemModel::SKIP_IMAGE:
                break;
            case ColumnItemModel::SKIP_URL:
                $data['url'] = input('url', '');
                empty($data['url']) AND $this->error('链接不为空');
                break;
            case ColumnItemModel::SKIP_PRODUCT:
                $data['content'] = intval(input('product_id', ''));
                empty($data['content']) AND $this->error('内容不为空');
                $result = ProductModel::check($data['content']);
                $result OR $this->error('商品不存在！');
                break;
            case ColumnItemModel::SKIP_SERIES:
                $data['content'] = intval(input('series_id', ''));
                empty($data['content']) AND $this->error('内容不为空');
                $result = ProductSeriesModel::check($data['content']);
                $result OR $this->error('专题不存在！');
                break;
            default:
                $this->error('请选择跳转模式');
                break;
        }

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
