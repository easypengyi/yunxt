<?php

namespace app\admin\controller;

use Exception;
use app\common\controller\AdminController;
use app\common\model\Product as ProductModel;
use app\common\model\ProductEvaluate as ProductEvaluateModel;

/**
 * 商品评论 模块
 */
class ProductEvaluate extends AdminController
{
    protected $product_id;

    /** @var ProductModel $product */
    protected $product;

    /**
     * 初始化方法
     * @return void
     * @throws Exception
     */
    public function _initialize()
    {
        parent::_initialize();
        $this->product_id = input('product_id', '');

        $this->product = ProductModel::get($this->product_id);
        empty($this->product) AND $this->error('商品已删除！');

        $this->param['product']    = $this->product;
        $this->param['product_id'] = $this->product_id;
    }

    /**
     * 列表
     * @return mixed
     * @throws Exception
     */
    public function index()
    {
        $where = $this->search('name', '输入需要查询的商品名称');

        $where['product_id'] = $this->product_id;

        $order = $this->sort_order(ProductEvaluateModel::getTableFields(), 'create_time', 'desc');

        $list = ProductEvaluateModel::page_list($where, $order);
        if (!$list->isEmpty()) {
            $list->load(['Member']);
        }
        $this->assign($list->toArray());
        $this->assign('return_url', folder_url('Product/index'));
        return $this->fetch_view('', ['product_id']);
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
            $data_info = ProductEvaluateModel::create($data);
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
        $data_info = ProductEvaluateModel::get($id);
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
     * 删除（单个）
     * @param $id
     * @return void
     * @throws Exception
     */
    public function del($id)
    {
        $this->is_ajax OR $this->error('请求错误！');

        $data_info = ProductEvaluateModel::get($id);
        empty($data_info) OR $data_info->delete();
        $this->success('删除成功！', $this->http_referer ?: $this->return_url());
    }

    /**
     * 默认返回链接
     * @return string
     */
    protected function return_url()
    {
        return controller_url('index');
    }

    /**
     * 数据处理
     * @param ProductModel $data_info
     * @return mixed
     * @throws Exception
     */
    private function edit_data($data_info = null)
    {
        $data['product_id'] = $this->product_id;

        $data['member_id'] = input('member_id', '');
        empty($data['member_id']) AND $this->error('请选择会员！');

        $data['score'] = input('score', '');
        empty($data['score']) AND $this->error('请设置评分！');

        $data['content'] = input('content', '');
        empty($data['content']) AND $this->error('评价内容不为空！');

        $data['image_ids'] = input('image_ids/a', []);

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
