<?php

namespace app\admin\controller;

use Exception;
use app\common\controller\AdminController;
use app\common\model\Product as ProductModel;


/**
 * 商品 模块
 */
class Product extends AdminController
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

        $where['del'] = false;
        $where['product_id'] = ['gt',5];
        $order = $this->sort_order(ProductModel::getTableFields(), 'sort', 'asc');

        $list = ProductModel::page_list($where, $order);
        if (!$list->isEmpty()) {
            $list->append([]);
        }

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
            $data_info = ProductModel::create($data);
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
        $data_info = ProductModel::get($id);
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

        $data_info = ProductModel::get($id);
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

        ProductModel::product_delete($id);
        $this->cache_clear();
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


        if ($this->request->file('image')) {
            $image = $this->upload_thumb(true, ['image']);
            $image['status'] OR $this->error($image['message']);

            $data['image_id'] = $image['data']['file_id'];
        } else {
            is_null($data_info) AND $this->error('请上传图片!');
        }


        if ($this->request->file('share_image')) {
            $share_image = $this->upload_thumb(true, ['share_image']);
            $share_image['status'] OR $this->error($share_image['message']);
            if ($share_image['status'] && $share_image['data']){
                $data['share_image_id'] = $share_image['data']['file_id'];
            }
        } else {
            is_null($data_info) AND $this->error('请上传分享海报图片!');
        }


        $data['current_price'] = input('current_price', '');
        empty($data['current_price']) AND $this->error('请设置现价！');


        $data['number']            = input('number', '');
        empty($data['number']) AND $this->error('请设置产品数量！');

        $data['name'] = trim(input('name', ''));
        empty($data['name']) AND $this->error('请设置名称！');

        $data['stock']            = input('stock', 0);
        $data['description']      = input('description', '');
        $data['detail_id']        = input('detail_id', '');
        $data['sort']             = input('sort', '');
        $data['original_price']   = input('original_price', '');
        $data['enable']           = boolval(input('enable', false));
        $data['detail_image_ids'] = input('image_id/a', []);

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
