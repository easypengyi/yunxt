<?php

namespace app\admin\controller;

use app\common\model\ArticleCenter as ArticleCenterModel;
use Exception;
use app\common\controller\AdminController;
use app\common\model\Article as ArticleModel;

/**
 * 文章 模块
 */
class Article extends AdminController
{

    protected $type = 1;
    /**
     * 初始化方法
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
        $where = $this->search('title', '输入需要查询的素材标题');

        $where['del'] = false;
        $where['type'] = 1;

        $order = $this->sort_order(ArticleCenterModel::getTableFields(), 'sort', 'asc');

        $list = ArticleCenterModel::page_list($where, $order);

        $this->assign($list->toArray());
        $this->assign('type',1);
        
        return $this->fetch_view();
    }

    /**
     * 列表
     * @return mixed
     * @throws Exception
     */
    public function article()
    {
        $where = $this->search('title', '输入需要查询的文章标题');

        $where['del'] = false;
        $where['type'] = 2;

        $order = $this->sort_order(ArticleCenterModel::getTableFields(), 'sort', 'asc');

        $list = ArticleCenterModel::page_list($where, $order);

        $this->assign($list->toArray());
        $this->assign('type',2);
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
            $data_info = ArticleCenterModel::create($data);
            empty($data_info) AND $this->error('信息新增失败！');
            $this->cache_clear();
            $this->success('信息新增成功！', input('return_url', $return_url));
        }

        $this->assign('return_url', $this->http_referer ?: $return_url);
        $this->assign('type', input('type', ''));

        return $this->fetch_view('edit',['type']);
    }

    /**
     * 编辑
     * @param $id
     * @return mixed
     * @throws Exception
     */
    public function edit($id)
    {
        $data_info = ArticleCenterModel::get($id);
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
        $this->assign('type', input('type', ''));
        return $this->fetch_view('edit', ['id','type']);
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

        $data_info = ArticleCenterModel::get($id);
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

        $result = ArticleCenterModel::where([ArticleCenterModel::primary_key() => $id])->update(['del' => true]);
        $result OR $this->error('删除失败！');
        $this->cache_clear();
        $this->success('删除成功！', $this->http_referer ?: $this->return_url());
    }

    /**
     * 数据处理
     * @param ArticleModel $data_info
     * @return mixed
     * @throws Exception
     */
    private function edit_data($data_info = null)
    {
         $data['type']   = input('type', '');

        if ($this->request->file('')) {
            $image = $this->upload_thumb(true, ['image']);
            $image['status'] OR $this->error($image['message']);

            $data['image_id'] = $image['data']['file_id'];
        } else {
            is_null($data_info) AND $this->error('请上传图片!');
        }
        $data['detail_id']    = input('detail_id', '');
        $data['content']   = input('content', '');
        $data['title']   = input('title', '');
        empty($data['title']) AND $this->error('请设置标题！');
        $data['sort']   = intval(input('sort', 50));
        $data['enable']     = boolval(input('enable', false));

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
