<?php

namespace app\admin\controller;

use Exception;
use helper\TimeHelper;
use app\common\controller\AdminController;
use app\common\model\Configure as ConfigureModel;
use app\common\model\MessageSend as MessageSendModel;

/**
 * 消息发送 模块
 */
class MessageSend extends AdminController
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
        $where = $this->search();

        $where['del'] = false;

        $order = $this->sort_order(MessageSendModel::getTableFields(), 'create_time', 'desc');

        $list = MessageSendModel::page_list($where, $order);
        if (!$list->isEmpty()) {
            $list->load(['Admin']);
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
            $data_info = MessageSendModel::create($data);
            empty($data_info) AND $this->error('信息新增失败！');
            $this->cache_clear();
            $this->success('信息新增成功！', input('return_url', $return_url));
        }

        $this->assign('return_url', $this->http_referer ?: $return_url);
        return $this->fetch_view('edit');
    }

    /**
     * 复制
     * @param $id
     * @return mixed
     * @throws Exception
     */
    public function copy($id)
    {
        $data_info = MessageSendModel::get($id);
        empty($data_info) AND $this->error('数据已删除！');

        $return_url = $this->return_url();
        if ($this->is_ajax) {
            $data      = $this->edit_data();
            $data_info = MessageSendModel::create($data);
            empty($data_info) AND $this->error('信息新增失败！');
            $this->cache_clear();
            $this->success('信息新增成功！', input('return_url', $return_url));
        }

        $this->assign('return_url', $this->http_referer ?: $return_url);
        $this->assign('data_info', $data_info);
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

        $result = MessageSendModel::where([MessageSendModel::primary_key() => $id])->update(['del' => true]);
        $result OR $this->error('删除失败！');
        $this->cache_clear();
        $this->success('删除成功！', $this->http_referer ?: $this->return_url());
    }

    /**
     * 数据处理
     * @param MessageSendModel $data_info
     * @return mixed
     * @throws Exception
     */
    private function edit_data($data_info = null)
    {
        $data['admin_id'] = $this->user['admin_id'];
        $data['content']  = trim(input('content', ''));
        empty($data['content']) AND $this->error('内容不为空！');

        $data['member_limit'] = boolval(input('member_limit', 0));
        if ($data['member_limit']) {
            $data['member_ids'] = array_filter(input('member_id/a', []));
            empty($data['member_ids']) AND $this->error('请选择发送消息的会员！');
        } else {
            $data['member_ids'] = [];
        }

        $data['time_limit'] = boolval(input('time_limit', 0));
        if ($data['time_limit']) {
            $data['show_time'] = intval(strtotime(input('show_time')));
            $data['show_time'] > time() OR $this->error('发送时间必须比当前时间晚！');
        } else {
            $data['show_time'] = time();
        }

        $data['expiry'] = boolval(input('expiry', false));
        if ($data['expiry']) {
            $data['expiry_time'] = $data['show_time'] +
                TimeHelper::daysToSecond(ConfigureModel::getValue('message_expiry_day'));
        } else {
            $data['expiry_time'] = 0;
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
        MessageSendModel::cacheClear();
    }
}
