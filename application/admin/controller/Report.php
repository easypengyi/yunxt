<?php

namespace app\admin\controller;

use app\common\model\Message;
use app\common\model\OrderShop;
use app\common\model\OrderShop as OrderShopModel;
use Exception;
use think\Db;
use tool\UploadTool;
use app\common\controller\AdminController;
use app\common\model\Report as ReportModel;

/**
 * 检测报告 模块
 */
class Report extends AdminController
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
        $where = $this->search('name', '输入需要查询的名称');

        $where['del'] = false;

        $order = $this->sort_order(ReportModel::getTableFields(), 'create_time', 'desc');

        $list = ReportModel::page_list($where, $order);
        if (!$list->isEmpty()) {
            $list->load(['orderShop']);
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
        $order_id = input('order_id', 0);
        empty($order_id) AND $this->error('订单ID不为空！');

        $return_url = $this->return_url();
        if ($this->is_ajax) {
            $data             = $this->edit_data();
            $data['order_id'] = $order_id;

            try {
                Db::startTrans();
                $data_info = ReportModel::create($data);
                OrderShopModel::order_finish($data_info->getAttr('order_id'));
                Db::commit();
            } catch (Exception $e) {
                Db::rollback();
                $this->error('信息新增失败！');
            }
            $order = OrderShopModel::get($data_info->getAttr('order_id'));
            Message::home_report($order['member_id']);
            $this->cache_clear();
            $this->success('信息新增成功！', input('return_url', $return_url));
        }

        $this->assign('return_url', $this->http_referer ?: $return_url);
        return $this->fetch_view('edit');
    }

    /**
     * 重新上传
     * @return mixed
     * @throws Exception
     */
    public function update()
    {
        $order_id = input('order_id', 0);
        empty($order_id) AND $this->error('订单ID不为空！');

        $return_url = $this->return_url();
        if ($this->is_ajax) {
            $data             = $this->edit_data();
            $data['order_id'] = $order_id;
            try {
                Db::startTrans();
                $data_info = ReportModel::update($data);
                OrderShopModel::order_finish($data_info->getAttr('order_id'));
                Db::commit();
            } catch (Exception $e) {
                Db::rollback();
                $this->error('信息新增失败！');
            }
            $order = OrderShopModel::get($data_info->getAttr('order_id'));
            Message::home_report($order['member_id']);
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
        $data_info = ReportModel::get($id);
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

        $result = ReportModel::where([ReportModel::primary_key() => $id])->update(['del' => true]);
        $result OR $this->error('删除失败！');
        $this->cache_clear();
        $this->success('删除成功！', $this->http_referer ?: $this->return_url());
    }

    /**
     * 数据处理
     * @param ReportModel $data_info
     * @return mixed
     * @throws Exception
     */
    private function edit_data($data_info = null)
    {
        if ($this->request->file()) {
            $image = UploadTool::instance()->upload_file(true, ['file']);
            $image['status'] OR $this->error($image['message']);
            $data['file_id'] = $image['data']['file_id'];
        } else {
            is_null($data_info) AND $this->error('请上传文件!');
        }

        $data['name']   = input('name', '');
        $data['remark'] = input('remark', '');

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
