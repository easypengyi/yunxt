<?php

namespace app\admin\controller;

use Exception;
use app\common\controller\AdminController;
use app\common\model\Product as ProductModel;
use app\common\model\CouponTemplate as CouponTemplateModel;

/**
 * 优惠券模板 模块
 */
class CouponTemplate extends AdminController
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
        $where = $this->search('coupon_name', '输入需要查询的模板名称');

        $where['del'] = false;

        $order = $this->sort_order(CouponTemplateModel::getTableFields(), 'create_time', 'desc');

        $list = CouponTemplateModel::page_list($where, $order);
        if (!$list->isEmpty()) {
            $list->append(['send_number']);
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
            $data_info = CouponTemplateModel::create($data);
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
        $data_info = CouponTemplateModel::get($id);
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

        $data_info = CouponTemplateModel::get($id);
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

        $result = CouponTemplateModel::where([CouponTemplateModel::primary_key() => $id])->update(['del' => true]);
        $result OR $this->error('删除失败！');
        $this->cache_clear();
        $this->success('删除成功！', $this->http_referer ?: $this->return_url());
    }

    /**
     * 数据处理
     * @param CouponTemplateModel $data_info
     * @return mixed
     * @throws Exception
     */
    private function edit_data($data_info = null)
    {
        // 时间限制处理
        $data['time_limit'] = boolval(input('time_limit', false));
        if ($data['time_limit']) {
            $data['start_time'] = intval(strtotime(input('start_time', '')));
            $data['end_time']   = intval(strtotime(input('end_time', '')));
            $data['end_time'] > $data['start_time'] OR $this->error('开始时间必须比结束时间早！');
        } else {
            $data['start_time'] = 0;
            $data['end_time']   = 0;
        }

        // 领取限制处理
        $data['receive_limit'] = boolval(input('receive_limit', false));
        if ($data['receive_limit']) {
            $data['start_receive_time'] = intval(strtotime(input('start_receive_time', '')));
            $data['start_receive_time'] < $data['start_time'] OR $this->error('领取时间不能晚于开始时间！');
        } else {
            $data['start_receive_time'] = 0;
        }

        // 商品限制处理
        $data['product_limit'] = boolval(input('product_limit', false));
        if ($data['product_limit']) {
            $where = ['product_id' => ['in', input('product_id/a', [])]];

            $data['product_ids'] = ProductModel::where($where)->column('product_id');
        } else {
            $data['product_ids'] = [];
        }

        $data['coupon_name'] = input('coupon_name', '');
        $data['coupon_desc'] = input('coupon_desc', '');
        $data['fill']        = floatval(input('fill', ''));
        empty($data['fill']) AND $this->error('满足金额不为空！');

        $data['value'] = floatval(input('value', ''));
        empty($data['value']) AND $this->error('优惠金额不为空！');

        $data['receive_number'] = intval(input('receive_number', ''));
        empty($data['receive_number']) AND $this->error('可领取数量不为空！');

        $data['number_limit'] = intval(input('number_limit', ''));
        empty($data['number_limit']) AND $this->error('会员领取限制不为空！');

        $data['activity_send'] = boolval(input('activity_send', false));

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
