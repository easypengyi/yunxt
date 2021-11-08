<?php

namespace app\admin\controller;

use Exception;
use app\common\controller\AdminController;
use app\common\model\CouponCode as CouponCodeModel;
use app\common\model\CouponTemplate as CouponTemplateModel;

/**
 * 优惠券赠送 模块
 */
class CouponCode extends AdminController
{
    protected $template_id;

    /**
     * 初始化方法
     * @return void
     * @throws Exception
     */
    public function _initialize()
    {
        parent::_initialize();

        $this->param['status'] = CouponCodeModel::status_array();

        $this->template_id = input('template_id', '');
    }

    /**
     * 列表
     * @return mixed
     * @throws Exception
     */
    public function index()
    {
        $where = $this->search();

        $where['template_id'] = $this->template_id;

        $order = $this->sort_order(CouponCodeModel::getTableFields(), 'create_time', 'desc');

        $list = CouponCodeModel::page_list($where, $order);
        if (!$list->isEmpty()) {
            $list->load(['Member']);
        }
        $this->assign($list->toArray());
        $this->assign('return_url', folder_url('CouponTemplate/index'));
        return $this->fetch_view('', ['template_id']);
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
            $data = $this->edit_data();
            while ($data['number']) {
                $data_info = CouponCodeModel::create(['template_id' => $data['template_id']]);
                $data['number']--;
            };

            empty($data_info) AND $this->error('信息新增失败！');

            $this->cache_clear();
            $this->success('信息添加成功！', input('return_url', $return_url));
        }

        $this->assign('return_url', $this->http_referer ?: $return_url);
        return $this->fetch_view('edit', ['template_id']);
    }

    /**
     * 默认返回链接
     * @return string
     */
    protected function return_url()
    {
        return controller_url('index', ['template_id' => $this->template_id]);
    }

    /**
     * 数据处理
     * @param CouponCodeModel $data_info
     * @return mixed
     * @throws Exception
     */
    private function edit_data($data_info = null)
    {
        $data['template_id'] = $this->template_id;
        $data['number']      = intval(input('number', 0));

        $template = CouponTemplateModel::get($this->template_id);
        $data['number'] <= $template->getAttr('receive_number') OR $this->error('生成数量不能大于总数量！');

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
