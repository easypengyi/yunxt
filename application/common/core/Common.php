<?php

namespace app\common\core;

use think\Lang;
use think\Config;
use think\Session;
use tool\UploadTool;
use think\Controller;
use app\common\model\LogWeb as LogWebModel;

/**
 * 公共类
 */
abstract class Common extends Controller
{
    // 模块名称
    protected $module = '';
    // 类名
    protected $class_name = '';
    // 是否ajax请求
    protected $is_ajax = false;
    // 来源地址
    protected $http_referer = '';
    // 来源地址为请求地址
    protected $referer_self = false;

    /**
     * 初始化方法
     * @return void
     */
    protected function _initialize()
    {
        parent::_initialize();

        $this->request->module() OR $this->request->module($this->module);

        $this->is_ajax      = $this->request->isAjax();
        $this->class_name   = class_basename($this);
        $this->http_referer = $this->request->server('HTTP_REFERER', '');
        empty($this->http_referer) AND $this->http_referer = Session::pull('HTTP_REFERER');
        empty($this->http_referer) AND $this->http_referer = '';

        if ($this->request->action() === 'operator_info') {
            $this->redirect(folder_url('Error/index'));
        }

        empty($this->http_referer) OR $this->referer_self = $this->request->url(true) == $this->http_referer;
    }

    /**
     * 操作者信息
     * @return array
     */
    public function operator_info()
    {
        return ['operator_id' => 0, 'type' => LogWebModel::TYPE_NO];
    }

    /**
     * 空操作
     * @return void
     */
    public function _empty()
    {
        $this->error(Lang::get('operation not valid'));
    }

    /**
     * 图片上传
     * @param bool         $only_one
     * @param string|array $name
     * @return array
     */
    protected function upload_thumb($only_one = true, $name = '')
    {
        return UploadTool::instance()->upload_thumb($only_one, $name);
    }

    /**
     * 音频上传
     * @param bool         $only_one
     * @param string|array $name
     * @return array
     */
    protected function upload_music($only_one = true, $name = '')
    {
        return UploadTool::instance()->upload_music($only_one, $name, true);
    }

    /**
     * 视频上传
     * @param bool         $only_one
     * @param string|array $name
     * @return array
     */
    protected function upload_video($only_one = true, $name = '')
    {
        return UploadTool::instance()->upload_video($only_one, $name, true);
    }

    /**
     * 加载模板输出
     * @access protected
     * @param  string $template 模板文件名
     * @param  array  $vars     模板输出变量
     * @param  array  $replace  模板替换
     * @param  array  $config   模板参数
     * @return mixed
     */
    protected function fetch($template = '', $vars = [], $replace = [], $config = [])
    {
        $this->before_assign();

        return parent::fetch($template, $vars, $replace, $config);
    }

    /**
     * 渲染内容输出
     * @access protected
     * @param  string $content 模板内容
     * @param  array  $vars    模板输出变量
     * @param  array  $replace 替换内容
     * @param  array  $config  模板参数
     * @return mixed
     */
    protected function display($content = '', $vars = [], $replace = [], $config = [])
    {
        $this->before_assign();

        return parent::display($content, $vars, $replace, $config);
    }

    /**
     * 输出前数据处理
     * @return void
     */
    protected function before_assign()
    {
        // 当前页面完整地址
        $this->assign('full_url', $this->request->url());
        $this->assign('file_version', Config::get('custom.view_file_version'));
    }

    /**
     * 返回封装后的 API 数据到客户端--成功
     * @param        $data
     * @param string $msg
     * @param string $type
     * @param array  $header
     * @return void
     */
    protected function success_result($data, $msg = '', $type = '', array $header = [])
    {
        $this->result($data, 1, $msg, $type, $header);
    }

    /**
     * 返回封装后的 API 数据到客户端--错误
     * @param        $data
     * @param string $msg
     * @param string $type
     * @param array  $header
     * @return void
     */
    protected function error_result($data, $msg = '', $type = '', array $header = [])
    {
        $this->result($data, 0, $msg, $type, $header);
    }

    /**
     * 引用检测
     * @return bool
     */
    protected function check_referer()
    {
        if (empty($this->http_referer) || 0 !== strpos($this->http_referer, $this->request->domain())) {
            return false;
        }
        return true;
    }
}