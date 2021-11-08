<?php

namespace app\common\controller;

use Exception;
use think\Lang;
use think\Log;
use think\Session;
use app\common\Constant;
use helper\ActionHelper;
use app\common\ResultCode;
use app\common\core\Common;
use UserAgent;

/**
 * 网站基础类
 */
abstract class WebController extends Common
{
    protected $app_type = Constant::CLIENT_WEB;

    protected $app_version = 1;

    protected $member;

    protected $token;

    protected $login_url = '';

    protected $wx_login = 'wx_login';

    protected $must_bind_phone = false;

    protected $member_tag = 'member';

    protected $token_tag = 'token';

    protected $must_bind_phone_tag = 'must_bind_phone';

    protected $logining = false;

    protected $is_weixin = false;

    /**
     * 初始化方法
     * @return void
     */
    protected function _initialize()
    {
        parent::_initialize();

        $this->is_weixin = UserAgent::instance()->is_weixin();
        $this->is_weixin AND $this->app_type = Constant::CLIENT_WX;

        $this->get_token();
        $this->get_member();

        $this->logining = !empty($this->token);
        $this->update_member();
    }

    /**
     * 接口访问
     * @param       $controller
     * @param       $action
     * @param array $param
     * @param bool  $handle
     * @return array
     * @throws Exception
     */
    protected function api($controller, $action, $param = [], $handle = true)
    {
        $base   = ['apptype' => $this->app_type, 'token' => $this->token, 'appversion' => $this->app_version];
        $result = ActionHelper::action('api', $controller, $action, array_merge($base, $param));
        $handle AND $this->api_code_check($result);
        return $result;
    }

    /**
     * 登录判断
     * @param bool $is_must
     * @return int
     */
    protected function login_check($is_must = true)
    {
        if (!empty($this->token) && isset($this->member['member_id'])) {
            return intval($this->member['member_id']);
        }
        $this->logout();
        $is_must AND $this->redirect_login(Lang::get($this->logining ? 'api login fail' : 'login check'));
        return 0;
    }

    /**
     * 绑定身份证判断
     * @param bool $is_must
     * @return int
     */
    protected function uid_check($is_must = true)
    {
        if (!empty($this->token) && isset($this->member['member_id'])) {
            return intval($this->member['member_id']);
        }
        $this->logout();
        $is_must AND $this->redirect_login(Lang::get($this->logining ? 'api login fail' : 'login check'));
        return 0;
    }


    /**
     * 登出
     * @return void
     */
    protected function logout()
    {
        $this->set_token(null);
        $this->set_member(null);
        $this->set_must_bind_phone();
    }




    /**
     * 设置会员信息
     * @param $member
     * @return void
     */
    protected function set_member($member)
    {
        if (empty($member)) {
            $this->member = [];
            Session::delete($this->member_tag);
            return;
        }
        $this->member = $member;
        Session::set($this->member_tag, $member);
    }

    /**
     * 获取会员信息
     * @return mixed
     */
    protected function get_member()
    {
        $session = Session::get($this->member_tag);
        return $this->member = empty($session) ? [] : $session;
    }

    /**
     * 更新会员信息
     * @return void
     */
    protected abstract function update_member();

    /**
     * 设置token
     * @param $token
     * @return void
     */
    protected function set_token($token)
    {
        if (empty($token)) {
            $this->token = '';
            Session::delete($this->token_tag);
            return;
        }

        $this->token = $token;
        Session::set($this->token_tag, $token);
    }

    /**
     * 获取token
     * @return mixed
     */
    protected function get_token()
    {
        $session = Session::get($this->token_tag);
        return $this->token = empty($session) ? '' : $session;
    }

    /**
     * 输出前数据处理
     * @return void
     */
    protected function before_assign()
    {
        parent::before_assign();
        $this->assign('app_type', $this->app_type);
        $this->assign('token', $this->token);
        $this->assign('member', $this->member);
    }

    /**
     * 设置必须绑定手机号状态
     * @param bool $bool
     * @return void
     */
    protected function set_must_bind_phone($bool = false)
    {
        $this->must_bind_phone = boolval($bool);
        Session::set($this->must_bind_phone_tag, $bool);
    }

    /**
     * 获取必须绑定手机号状态
     * @return mixed
     */
    protected function get_must_bind_phone()
    {
        $bool = Session::get($this->must_bind_phone_tag);
        return $this->must_bind_phone = boolval($bool);
    }

    /**
     * 重定向登录页面
     * @param $message
     * @param $referer
     * @return void
     */
    protected function redirect_login($message, $referer = null)
    {
        if ($this->is_ajax) {
            $this->error($message, folder_url($this->login_url));
        } else {
            is_null($referer) AND $referer = $this->request->url(true);
            $this->redirect(folder_url($this->login_url), [], 302, ['HTTP_REFERER' => $referer]);
        }
    }

    /**
     * 接口返回数据处理
     * @param $result
     * @return void
     */
    protected function api_handler($result)
    {
        switch (intval($result['code'])) {
            case ResultCode::RES_LOGIN_ERR:
                $this->logout();
                $this->redirect_login(Lang::get($this->logining ? 'api login fail' : 'login check'));
                break;
            case ResultCode::RES_PARAMETER_ERR:
                $this->error($result['msg']);
                break;
        }
    }

    /**
     * 接口返回数据登录判断
     * @param $result
     * @return void
     */
    private function api_code_check($result)
    {
        if (!is_array($result)) {
            return;
        }
        if (!isset($result['code'])) {
            return;
        }

        $this->api_handler($result);
    }
}
