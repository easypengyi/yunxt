<?php

namespace app\admin\controller;

use Exception;
use tool\PaymentTool;
use app\common\controller\AdminController;
use app\common\model\PaymentConfig as PaymentConfigModel;

/**
 * 支付配置 模块
 */
class PaymentConfig extends AdminController
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

        $order = $this->sort_order(PaymentConfigModel::getTableFields(), 'id', 'desc');

        $list = PaymentConfigModel::page_list($where, $order);

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
            $data_info = PaymentConfigModel::create($data);
            empty($data_info) AND $this->error('信息新增失败！');
            $this->edit_after($data_info);
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
        $data_info = PaymentConfigModel::get($id);
        empty($data_info) AND $this->error('数据已删除！');

        $return_url = $this->return_url();
        if ($this->is_ajax) {
            $data = $this->edit_data($data_info);
            $data_info->save($data);
            $this->edit_after($data_info);
            $this->cache_clear();
            $this->success('信息修改成功！', input('return_url', $return_url));
        }

        $this->assign('return_url', $this->http_referer ?: $return_url);
        $this->assign('data_info', $data_info);
        $this->assign('edit', true);
        return $this->fetch_view('edit', ['id']);
    }

    /**
     * 配置支付数据
     * @param $id
     * @return mixed
     * @throws Exception
     */
    public function config($id)
    {
        $data_info = PaymentConfigModel::get($id);
        empty($data_info) AND $this->error('数据已删除！');

        $return_url = $this->return_url();
        if ($this->is_ajax) {
            $config['use_sandbox'] = input('use_sandbox', 0);
            switch ($data_info->getAttr('payment_id')) {
                case PaymentTool::ALIPAY:
                    $config['app_id']          = input('app_id', '');
                    $config['sign_type']       = input('sign_type', '');
                    $config['rsa_private_key'] = input('rsa_private_key', '');
                    $config['ali_public_key']  = input('ali_public_key', '');
                    break;
                case PaymentTool::WXPAY:
                    $config['app_id']       = input('app_id', '');
                    $config['app_secret']   = input('app_secret', '');
                    $config['mch_id']       = input('mch_id', '');
                    $config['sign_type']    = input('sign_type', '');
                    $config['md5_key']      = input('md5_key', '');
                    $config['app_cert_pem'] = input('app_cert_pem', '');
                    $config['app_key_pem']  = input('app_key_pem', '');
                    break;
                case PaymentTool::UPACPAY:
                    $config['mer_id']         = input('mer_id', '');
                    $config['sign_cert_pwd']  = input('sign_cert_pwd', '');
                    $config['sign_cert_path'] = input('sign_cert_path', '');
                    break;
                case PaymentTool::UPAY:
                    break;
                default:
                    break;
            }
            $data['config'] = $config;

            $result = $data_info->save($data);
            $result OR $this->error('信息配置失败！');
            $this->cache_clear();
            $this->success('信息配置成功！', input('return_url', $return_url));
        }

        $this->assign('return_url', $this->http_referer ?: $return_url);
        $this->assign('data_info', $data_info);
        return $this->fetch_view('', ['id']);
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

        $data_info = PaymentConfigModel::get($id);
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

        $result = PaymentConfigModel::where([PaymentConfigModel::primary_key() => $id])->delete();
        $result OR $this->error('删除失败！');
        $this->cache_clear();
        $this->success('删除成功！', $this->http_referer ?: $this->return_url());
    }

    /**
     * 数据处理
     * @param PaymentConfigModel $data_info
     * @return mixed
     */
    private function edit_data($data_info = null)
    {
        $data['payment_id'] = input('payment_id', '');
        $data['pay_type']   = input('pay_type', '');
        $data['enable']     = boolval(input('enable', false));
        $data['tolerant']   = input('tolerant', 0);

        if (is_null($data_info)) {
            $data['config'] = [];
        } else {

        }

        return $data;
    }

    /**
     * 后续数据处理
     * @param PaymentConfigModel $data_info
     * @throws Exception
     */
    private function edit_after($data_info)
    {
        if ($data_info->getAttr('tolerant')) {
            PaymentConfigModel::default_remove($data_info->getAttr('id'), $data_info->getAttr('pay_type'));
        }
    }

    /**
     * 缓存清理
     * @return void
     */
    private function cache_clear()
    {
        PaymentConfigModel::cacheClear();
    }
}
