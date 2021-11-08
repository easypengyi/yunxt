<?php

namespace app\admin\controller;

use Exception;
use app\common\controller\AdminController;
use app\common\model\Banner as BannerModel;
use app\common\model\Product as ProductModel;

/**
 * 广告 模块
 */
class Banner extends AdminController
{
    protected $type = 0;

    /**
     * 初始化方法
     * @throws Exception
     */
    public function _initialize()
    {
        parent::_initialize();
        $this->type = input('type', 0);
        if (!in_array($this->type, BannerModel::banner_type_group())) {
            $this->redirect(folder_url());
        }

        $this->param['type']      = $this->type;
        $this->param['skip']      = BannerModel::banner_skip_array();
        $this->param['type_skip'] = BannerModel::banner_type_skip_array();
    }

    /**
     * 列表
     * @return mixed
     * @throws Exception
     */
    public function index()
    {
        $where = $this->search('name', '输入需要查询的名称');

        $where['type'] = $this->type;
        $where['del']  = false;

        $order = $this->sort_order(BannerModel::getTableFields(), 'sort', 'asc');

        $list = BannerModel::page_list($where, $order);

        $this->assign($list->toArray());
        return $this->fetch_view('', ['type']);
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
            $data_info = BannerModel::create($data);
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
        $data_info = BannerModel::get($id);
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

        $data_info = BannerModel::get($id);
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

        $result = BannerModel::where([BannerModel::primary_key() => $id])->update(['del' => true]);
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
        return controller_url(['index', ['type' => $this->type]]);
    }

    /**
     * 数据处理
     * @param BannerModel $data_info
     * @return mixed
     * @throws Exception
     */
    private function edit_data($data_info = null)
    {
        $data['type'] = $this->type;

        if ($this->request->file()) {
            $image = $this->upload_thumb(true, ['image']);
            $image['status'] OR $this->error($image['message']);
            $data['image_id'] = $image['data']['file_id'];
        } else {
            is_null($data_info) AND $this->error('请上传图片!');
        }

        $data['skip'] = trim(input('skip', ''));
        empty($data['skip']) AND $this->error('必须选择跳转方式');

        $data['name']       = input('name', '');
        $data['sort']       = intval(input('sort', 50));
        $data['enable']     = boolval(input('enable', false));
        $data['start_time'] = intval(strtotime(input('start_time', '')));
        $data['end_time']   = intval(strtotime(input('end_time', '')));
        $data['end_time'] > $data['start_time'] OR $this->error('开始时间必须比结束时间早！');

        $data['url']     = '';
        $data['content'] = '';

        switch (input('skip', '')) {
            case BannerModel::SKIP_IMAGE:
                break;
            case BannerModel::SKIP_URL:
                $data['url'] = input('url', '');
                empty($data['url']) AND $this->error('链接不为空');
                break;
            case BannerModel::SKIP_PRODUCT:
                $data['content'] = intval(input('product_id', ''));
                empty($data['content']) AND $this->error('内容不为空');
                $result = ProductModel::check($data['content']);
                $result OR $this->error('商品不存在！');
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
