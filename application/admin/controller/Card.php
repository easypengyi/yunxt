<?php

namespace app\admin\controller;
use app\common\model\ActivityPeople;
use Exception;
use app\common\controller\AdminController;
use app\common\model\Card as CardModel;


/**
 * 活动模块
 */
class Card extends AdminController
{

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
        $where = $this->search('name', '输入活动名称');
        $where['del']        = false;
        $where['type']       = 1;
        $order = $this->sort_order(CardModel::getTableFields(), 'id', 'desc');

        $list = CardModel::page_list($where, $order);
        $this->assign($list->toArray());
        return $this->fetch_view();
    }

    /**
     * 列表
     * @return mixed
     * @throws Exception
     */
    public function salon()
    {
        $where = $this->search('name', '输入活动名称');
        $where['del']        = false;
        $where['type']       = 2;
        $order = $this->sort_order(CardModel::getTableFields(), 'id', 'desc');

        $list = CardModel::page_list($where, $order);
        $this->assign($list->toArray());
        return $this->fetch_view();
    }

    /**
     * 列表
     * @return mixed
     * @throws Exception
     */
    public function activity_people()
    {
        $where = $this->search('name', '输入姓名');
        $order = $this->sort_order(CardModel::getTableFields(), 'id', 'desc');
        $list = ActivityPeople::page_list($where, $order);
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
            $data_info = CardModel::create($data);
            empty($data_info) AND $this->error('信息新增失败！');
            $this->cache_clear();
            $this->success('信息新增成功！', input('return_url', $return_url));
        }

        $this->assign('return_url', $this->http_referer ?: $return_url);
        return $this->fetch_view('edit');
    }

    /**
     * 添加
     * @return mixed
     * @throws Exception
     */
    public function salon_add()
    {
        $return_url = $this->return_url();

        if ($this->is_ajax) {
            $data      = $this->salon_edit_data();
            $data_info = CardModel::create($data);
            empty($data_info) AND $this->error('信息新增失败！');
            $this->cache_clear();
            $this->success('信息新增成功！', input('return_url', $return_url));
        }

        $this->assign('return_url', $this->http_referer ?: $return_url);
        return $this->fetch_view('salon_edit');
    }

    /**
     * 添加
     * @return mixed
     * @throws Exception
     */
    public function activity_add()
    {
        $return_url = $this->return_url();

        if ($this->is_ajax) {
            $data      = $this->activity_edit_data();
            $data_info = ActivityPeople::create($data);
            empty($data_info) AND $this->error('信息新增失败！');
            $this->cache_clear();
            $this->success('信息新增成功！', input('return_url', $return_url));
        }

        $this->assign('return_url', $this->http_referer ?: $return_url);
        return $this->fetch_view('activity_edit');
    }




    /**
     * 编辑
     * @param $id
     * @return mixed
     * @throws Exception
     */
    public function edit($id)
    {
        $data_info = CardModel::get($id);
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
        return $this->fetch_view('edit');
    }

    /**
     * 编辑
     * @param $id
     * @return mixed
     * @throws Exception
     */
    public function salon_edit($id)
    {
        $data_info = CardModel::get($id);
        empty($data_info) AND $this->error('数据已删除！');

        $return_url = $this->return_url();
        if ($this->is_ajax) {
            $data = $this->salon_edit_data($data_info);
            $data_info->save($data);
            $this->cache_clear();
            $this->success('信息修改成功！', input('return_url', $return_url));
        }

        $this->assign('return_url', $this->http_referer ?: $return_url);
        $this->assign('data_info', $data_info);
        $this->assign('edit', true);
        return $this->fetch_view('salon_edit');
    }

    /**
     * 编辑
     * @param $id
     * @return mixed
     * @throws Exception
     */
    public function activity_edit($id)
    {
        $data_info = ActivityPeople::get($id);
        empty($data_info) AND $this->error('数据已删除！');

        $return_url = $this->return_url();
        if ($this->is_ajax) {
            $data = $this->activity_edit_data($data_info);
            $data_info->save($data);
            $this->cache_clear();
            $this->success('信息修改成功！', input('return_url', $return_url));
        }

        $this->assign('return_url', $this->http_referer ?: $return_url);
        $this->assign('data_info', $data_info);
        $this->assign('edit', true);
        return $this->fetch_view('activity_edit');
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

        $data_info = CardModel::get($id);
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

        $result = CardModel::where([CardModel::primary_key() => $id])->update(['del' => true]);
        $result OR $this->error('删除失败！');
        $this->cache_clear();
        $this->success('删除成功！', $this->http_referer ?: $this->return_url());
    }


    /**
     * 数据处理
     * @param CardModel $data_info
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

        $data['type']       = 1;
        $data['name']       = input('name', '');
        $data['sort']       = intval(input('sort', 50));
        $data['enable']     = boolval(input('enable', true));
        $data['province'] = input('province', '');
        $data['city'] = input('city', '');
        $data['area'] = input('area', '');
        $data['address'] = input('address', '');
        $data['detail_image_ids'] = input('image_id/a', []);
        $data['start_time']   = intval(strtotime(input('start_time', '')));
        $data['end_time']   = intval(strtotime(input('end_time', '')));
        $data['description'] = input('description', '');
        $data['num'] = input('num', '');
        $data['start_time'] = intval(strtotime(input('start_time', '')));
        $data['end_time'] = intval(strtotime(input('end_time', '')));
        $data['longitude'] = input('longitude', '');
        $data['latitude'] = input('latitude', '');
        $data['phone']        = input('phone', '');
        $data['detail_id']     = input('detail_id', '');
        $data['admin_ids'] = array_filter(input('admin_id/a', []));

        empty($data['address']) AND $this->error('请输入活动详细地址！');
        empty(!$data['phone']) OR $this->error('请输入咨询电话');
        empty(!$data['longitude']) OR $this->error('请输入经度位置');
        empty(!$data['latitude']) OR $this->error('请输入纬度位置');

        if (is_null($data_info)) {

        } else {

        }

        return $data;
    }



    /**
     * 数据处理
     * @param CardModel $data_info
     * @return mixed
     * @throws Exception
     */
    private function salon_edit_data($data_info = null)
    {

        if ($this->request->file('image')) {
            $image = $this->upload_thumb(true, ['image']);
            $image['status'] OR $this->error($image['message']);
            $data['image_id'] = $image['data']['file_id'];
        } else {
            is_null($data_info) AND $this->error('请上传图片!');
        }

        $data['type']       = 2;
        $data['name']       = input('name', '');
        $data['sort']       = intval(input('sort', 50));
        $data['enable']     = boolval(input('enable', true));
        $data['province'] = input('province', '');
        $data['city'] = input('city', '');
        $data['area'] = input('area', '');
        $data['address'] = input('address', '');

        $data['start_time']   = intval(strtotime(input('start_time', '')));
        $data['end_time']   = intval(strtotime(input('end_time', '')));
        $data['description'] = input('description', '');
        $data['num'] = input('num', '');
        $data['time'] = intval(strtotime(input('time', '')));
        $data['detail_id']     = input('detail_id', '');

        empty($data['address']) AND $this->error('请输入活动详细地址！');

        if (is_null($data_info)) {

        } else {

        }

        return $data;
    }

    public function activity_edit_data($data_info = null){
        if ($this->request->file('image')) {
            $image = $this->upload_thumb(true, ['image']);
            $image['status'] OR $this->error($image['message']);
            $data['image_id'] = $image['data']['file_id'];
        } else {
            is_null($data_info) AND $this->error('请上传图片!');
        }
        $data['name']       = input('name', '');
        $data['title']       = input('title', '');
        empty($data['name']) AND $this->error('请输入姓名！');
        empty($data['title']) AND $this->error('请输入称号！');

        if (is_null($data_info)) {

        } else {

        }

        return $data;
    }


    /**
     * 默认返回链接
     * @return string
     */
    protected function return_url()
    {
        return controller_url(['index']);
    }



    /**
     * 缓存清理
     * @return void
     */
    private function cache_clear()
    {
    }




}
