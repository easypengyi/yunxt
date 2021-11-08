<?php

namespace app\admin\controller;

use Tree;
use Exception;
use app\common\controller\AdminController;
use app\common\model\ProductCategory as ProductCategoryModel;

/**
 * 商品分类 模块
 */
class ProductCategory extends AdminController
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
     * @param int $pid
     * @return mixed
     * @throws Exception
     */
    public function add($pid = 0)
    {
        $return_url = $this->return_url();
        if ($this->is_ajax) {
            $data      = $this->edit_data();
            $data_info = ProductCategoryModel::create($data);
            empty($data_info) AND $this->error('信息新增失败！');

            $data_info          = $data_info->toArray();
            $data_info['pid']   = $data_info['category_id'];
            $data_info['level'] += 1;
            unset($data_info['category_id']);
            ProductCategoryModel::create($data_info);

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
        $data_info = ProductCategoryModel::get($id);
        empty($data_info) AND $this->error('数据已删除！');

        $return_url = $this->return_url();
        if ($this->is_ajax) {
            $old_pid   = $data_info->getAttr('pid');
            $old_level = $data_info->getAttr('level');

            $data = $this->edit_data($data_info);
            $data_info->save($data);

            //更新子孙级的level
            if ($old_pid != $data_info->getAttr('pid') && $old_level != $data_info->getAttr('level')) {
                $list = ProductCategoryModel::all_product_category();

                $tree = Tree::instance();
                $tree->init($list, ['parentid' => 'pid', 'id' => ProductCategoryModel::primary_key()]);
                $child_array = $tree->get_childs($id, true, false);

                if (!empty($child_array)) {
                    $level = $data_info->getAttr('level');
                    $where = [ProductCategoryModel::primary_key() => ['in', $child_array]];
                    if ($level > $old_level) {
                        ProductCategoryModel::where($where)->setInc('level', $level - $old_level);
                    } else {
                        ProductCategoryModel::where($where)->setDec('level', $old_level - $level);
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
     * 启用状态变更
     * @param $id
     * @return void
     * @throws Exception
     */
    public function change_enable($id)
    {
        $this->is_ajax OR $this->error('请求错误！');

        $data_info = ProductCategoryModel::get($id);
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

        $list = ProductCategoryModel::all_product_category();

        $tree = Tree::instance();
        $tree->init($list, ['parentid' => 'pid', 'id' => ProductCategoryModel::primary_key()]);
        $child_array = $tree->get_childs($id, true, true);
        empty($child_array) AND $this->error('删除失败！');

        $result = ProductCategoryModel::where([ProductCategoryModel::primary_key() => ['in', $child_array]])->delete();
        $result OR $this->error('删除失败！');
        $this->cache_clear();
        $this->success('删除成功！', $this->http_referer ?: $this->return_url());
    }

    /**
     * 数据处理
     * @param ProductCategoryModel $data_info
     * @return mixed
     * @throws Exception
     */
    private function edit_data($data_info = null)
    {
        $pid = input('pid', 0);
        if (empty($pid)) {
            $level = 1;
        } else {
            $parent = ProductCategoryModel::get($pid);
            empty($parent) AND $this->error('添加失败');
            $level = $parent->getAttr('level') + 1;
        }

        if ($this->request->file()) {
            $image = $this->upload_thumb(true, ['image']);
            $image['status'] OR $this->error($image['message']);

            $data['image_id'] = $image['data']['file_id'];
        } else {
            $data['image_id'] = 0;
            //is_null($data_info) AND $this->error('请上传图片!');
        }

        $data['level'] = $level;
        $data['level'] > 2 AND $this->error('只允许添加二级分类！');

        $data['pid']  = $pid;
        $data['name'] = trim(input('name', ''));
        empty($data['name']) AND $this->error('请填写商品分类名称！');

        $data['enable'] = boolval(input('enable', false));
        $data['sort']   = intval(input('sort', 50));

        $where       = ['level' => $data['level'], 'name' => $data['name']];
        $category_id = ProductCategoryModel::where($where)->value('category_id', 0);

        if (is_null($data_info)) {
            empty($category_id) OR $this->error('该分类名称已存在！');
        } else {
            if ($category_id != $data_info->getAttr('category_id')) {
                empty($category_id) OR $this->error('该分类名称已存在！');
            }
            if ($data_info->getAttr('category_id') == $pid) {
                $this->error('请勿设置本身子级！');
            }
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
