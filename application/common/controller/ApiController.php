<?php

namespace app\common\controller;

use think\App;
use Exception;
use think\Cache;
use tool\AesTool;
use think\Config;
use helper\HttpHelper;
use helper\ActionHelper;
use app\common\Constant;
use app\common\ResultCode;
use app\common\core\Common;
use app\common\model\LogWeb as LogWebModel;
use app\common\model\Message as MessageModel;
use app\common\model\MemberToken as MemberTokenModel;
use app\common\model\MessageSend as MessageSendModel;
use app\common\model\MessageSendItem as MessageSendItemModel;
use app\common\model\MemberGroupRelation as MemberGroupRelationModel;

/**
 * 接口基础类
 */
abstract class ApiController extends Common
{
    protected $module = 'api';
    // 必须使用post
    protected $must_post = false;
    // 必须apptype参数
    protected $must_apptype = true;
    // 必须appversion参数
    protected $must_appversion = true;
    // 必须进行接口认证
    protected $must_verification = true;
    // 允许使用消息
    protected $allow_message = false;
    // 允许的客户端类型
    protected $allow_apptype = [];
    // 会员token
    protected $token = '';
    // 会员客户端类型
    protected $app_type = 0;
    // 会员客户端分类
    protected $app_category = 0;
    // 客户端版本号
    protected $app_version = 0;
    // 会员ID
    protected $member_id = 0;
    // 第三方登录类型ID
    protected $oauth_from = 0;

    /**
     * 初始化方法
     * @return void
     * @throws Exception
     */
    protected function _initialize()
    {
        parent::_initialize();

        $this->must_post         = Config::get('must_post');
        $this->must_apptype      = Config::get('must_apptype');
        $this->must_appversion   = Config::get('must_appversion');
        $this->must_verification = Config::get('must_verification');
        $this->allow_apptype     = Config::get('allow_apptype');
        $this->allow_message     = Config::get('allow_message');

        $this->set_apptype();

        $this->set_appversion();

        $this->set_appcategory();

        $this->api_verification();

        $this->set_member_id();

        $this->change_connect_time();

        $this->check_message();
    }

    /**
     * 操作者信息
     * @return array
     */
    public function operator_info()
    {
        if (empty($this->member_id)) {
            return ['operator_id' => 0, 'type' => LogWebModel::TYPE_NO];
        }

        return ['operator_id' => $this->member_id, 'type' => LogWebModel::TYPE_MEMBER];
    }

    /**
     * 获取参数
     * @param      $name
     * @param      $default_value
     * @param bool $trim
     * @return mixed
     */
    protected function get_param($name, $default_value = null, $trim = true)
    {
        $data = $this->request->post($name, null);
        if (!$this->must_post && $data === null) {
            $data = $this->request->param($name, $default_value);
        } else {
            is_null($data) AND (is_null($default_value) OR $data = $default_value);
        }

        $is_null = is_null($data);

        $is_null AND output_error(App::$debug ? $name : '' . '参数错误！');

        $is_null OR ($trim AND $data = trim($data));

        return $data;
    }

    /**
     * 验证是否登录
     * @return void
     */
    protected function check_login()
    {
        empty($this->member_id) AND output_error('请登录！', ResultCode::RES_LOGIN_ERR);
    }

    /**
     * 验证会员组
     * @param        $group_id
     * @param bool   $must
     * @param string $message
     * @return bool
     * @throws Exception
     */
    protected function check_group($group_id, $must = true, $message = '')
    {
        $result = MemberGroupRelationModel::check_group($this->member_id, $group_id);
        ($must && !$result) AND output_error(empty($message) ? '无操作权限！' : $message);
        return $result;
    }

    /**
     * 验证客户端分类
     * @param $category_id
     * @return bool
     */
    protected function check_category($category_id)
    {
        return $this->app_category === $category_id;
    }

    /**
     * token生成
     * @param     $member_id
     * @param int $oauth_from
     * @return string
     * @throws Exception
     */
    protected function create_token($member_id, $oauth_from = 0)
    {
        $token = md5($member_id . $oauth_from) . md5(microtime());
        MemberTokenModel::insert_token($token, $member_id, $oauth_from, $this->app_type);
        return $token;
    }

    /**
     * 退出登录
     * @param int $member_id
     * @return bool
     * @throws Exception
     */
    protected function out_login($member_id = 0)
    {
        if (empty($member_id)) {
            return MemberTokenModel::delete_by_token($this->token);
        } else {
            return MemberTokenModel::delete_token($member_id);
        }
    }

    /**
     * 接口验证
     * @return void
     * @throws Exception
     */
    private function api_verification()
    {
        // debug 模式跳过认证
        if (App::$debug) {
            return;
        }

        // 内部网页调用跳过
        if (ActionHelper::is_inside()) {
            return;
        }

        // 不进行验证跳过
        if (!$this->must_verification) {
            return;
        }

        $timestamp = $this->get_param('timestamp');
        time() - intval(Config::get('api_timeout')) > $timestamp AND output_error('请求超时');

        $sign = $this->get_param('sign');

        $aes   = AesTool::instance('api_aes');
        $param = $this->request->except('sign');

        $check_sign = $aes->encrypt(urldecode(HttpHelper::http_build_query($param)));
        $sign === $check_sign OR output_error('认证失败！');
    }

    /**
     * 设置 apptype
     * @return void
     */
    private function set_apptype()
    {
        $this->app_type = intval($this->get_param('apptype', 0));
        in_array($this->app_type, $this->allow_apptype) OR $this->app_type = 0;
        if ($this->must_apptype && empty($this->app_type)) {
            output_error('参数错误！');
        }
    }

    /**
     * 设置 appversion
     * @return void
     */
    private function set_appversion()
    {
        $this->app_version = intval($this->get_param('appversion', 0));
        if ($this->must_appversion && empty($this->app_version)) {
            output_error('参数错误！');
        }
    }

    /**
     * 设置 appcategory
     * @return void
     */
    private function set_appcategory()
    {
        $this->app_category = intval($this->get_param('appcategory', 0));
        in_array($this->app_category, Constant::category_group()) OR $this->app_category = Constant::CATEGORY_MEMBER;
    }

    /**
     * 设置 会员id
     * @return void
     * @throws Exception
     */
    private function set_member_id()
    {
        $this->token = $this->get_param('token', '');

        $token = MemberTokenModel::load_token($this->token);
        if (!empty($token) && $this->app_type === intval($token['app_type'])) {
            $this->member_id  = intval($token['member_id']);
            $this->oauth_from = intval($token['oauth_from']);
        }
    }

    /**
     * 修改会员访问时间
     * @return void
     * @throws Exception
     */
    private function change_connect_time()
    {
        if (!empty($this->token)) {
            MemberTokenModel::change_time($this->token);
        }
    }

    /**
     * 检查系统消息
     * @return void
     * @throws Exception
     */
    private function check_message()
    {
        if (empty($this->member_id)) {
            return;
        }

        if (!$this->allow_message) {
            return;
        }

        $key = __FUNCTION__ . $this->member_id;

        if (Cache::get($key)) {
            return;
        }

        $list = MessageSendModel::send_list($this->member_id);
        foreach ($list as $v) {
            MessageModel::insert_message($this->member_id, 0, $v['content'], MessageModel::SYSTEM, 0, $v['show_time']);
            MessageSendItemModel::insert_item($this->member_id, $v['send_id']);
        }

        Cache::tag(MessageSendModel::getCacheTag())->set($key, true);
    }

    /**
     * 空方法处理
     * @return void
     */
    public function _empty()
    {
        output_error('接口不存在！');
    }
}
