<?php

namespace app\mobile\controller;

use app\common\model\ActivityOrderShop;
use app\common\model\Member;
use app\common\model\Member as MemberModel;
use app\common\model\MemberGroup;
use app\common\model\MemberGroupRelation;
use app\common\model\OrderShop;
use app\common\model\OrdersShop;
use Exception;
use think\Config;
use think\Log;
use think\Session;
use thinksdk\ThinkOauth;
use app\common\controller\MobileController;
use app\common\model\SmsCode as SmsCodeModel;
use app\common\model\OauthUser as OauthUserModel;

/**
 * 登录
 */
class Login extends MobileController
{
    /**
     * 登录
     * @return mixed
     * @throws Exception
     */
    public function index()
    {
        if ($this->is_ajax) {
            $return_url = input('return_url', '');
            $type       = intval(input('type'));
            $mobile     = input('telephone', '');
            $password   = md5(input('password', ''));
            $code       = input('code', '');
            switch ($type) {
                case 1:
                    $result = $this->api('Member', 'sms_login', ['telephone' => $mobile, 'code' => $code]);
                    break;
                case 2:
                    $result = $this->api('Member', 'login', ['telephone' => $mobile, 'password' => $password]);
                    break;
                default:
                    $this->error('类型错误！');
                    $result = null;
                    break;
            }

            $this->set_token($result['data']['token']);
            $this->set_member($result['data']['member']);

            $this->success('登录成功！', folder_url('Index/index'));
        }

        $this->assign('title', '登录');
        $this->assign('return_url', $this->http_referer ?: folder_url('Index/index'));
        return $this->fetch();
    }

    /**
     * 注册
     * @return mixed
     * @throws Exception
     */
    public function register()
    {
        $invitation = input('invitation', '');

        if ($this->is_ajax) {
            $mobile   = input('telephone', '');
            $password = md5(input('password', ''));
            $code     = input('code', '');

            $param  = ['telephone' => $mobile, 'password' => $password, 'code' => $code, 'invitation' => $invitation];
            $result = $this->api('Member', 'register', $param);

            $this->set_token($result['data']['token']);
            $this->set_member($result['data']['member']);

            $this->success('恭喜您，注册成功！', folder_url('User/index'));
        }

        $this->assign('invitation', $invitation);
        $this->assign('title', '注册');
        return $this->fetch();
    }

    /**
     * 退出登录
     * @return void
     * @throws Exception
     */
    public function logoff()
    {
        $this->login_check();

        $this->logout();
        $this->set_wx_login(null);
        $this->redirect(controller_url('index'));
    }

    /**
     * 注册-补充信息
     * @return mixed
     * @throws Exception
     */
    public function regis_info()
    {
        $this->login_check();

        if ($this->is_ajax) {
            $member = [
                'name'     => input('nickname'),
                'sex'      => input('sex'),
                'birthday' => input('birthday', ''),
                'mail'     => input('mail', ''),
            ];
            $this->api('Member', 'member_info_change', $member);

            $address = [
                'consignee' => input('consignee'),
                'address'   => input('address'),
                'mobile'    => input('telephone'),
                'district'  => input('district'),
            ];
            $this->api('Address', 'address_add', $address);

            $this->success('信息修改成功！', folder_url('User/index'));
        }
        $this->assign('title', '注册-补充信息');
        $this->assign('return_url', $this->http_referer ?: folder_url());
        return $this->fetch();
    }

    /**
     * 修改手机号1
     * @return mixed
     * @throws Exception
     */
    public function phone_changeo()
    {
        $this->login_check();

        if ($this->is_ajax) {
            $mobile = input('mobile');
            $code   = input('code', '');

            if (empty($mobile) || empty($code)) {
                $this->error('手机号、验证码不能为空！');
            }

            $result = $this->api(
                'Sms',
                'verification',
                ['telephone' => $mobile, 'code' => $code, 'type' => SmsCodeModel::CHANGE_PHONE_OLD]
            );

            Session::set('verify_code', $result['data']['verify']);
            $this->success($result['msg'], controller_url('phone_change'));
        }
        $this->assign('title', '更改手机号');
        return $this->fetch();
    }

    /**
     * 修改手机号2
     * @return mixed
     * @throws Exception
     */
    public function phone_change()
    {
        $this->login_check();

        $verify = Session::get('verify_code');

        if ($this->is_ajax) {
            if (empty($verify)) {
                $this->error('旧号码验证已失效，请重新验证！', controller_url('phone_changeo'));
            }

            $mobile = input('mobile', '');
            $code   = input('code', '');

            $result = $this->api(
                'Member',
                'change_mobile',
                ['telephone' => $mobile, 'code' => $code, 'verify' => $verify]
            );

            Session::delete('verify_code');
            $this->clear_update_member_time();
            $this->success($result['msg'], folder_url('Login/index'));
        }

        empty($verify) AND $this->redirect(folder_url('User/index'));

        $this->assign('title', '更改手机号');
        return $this->fetch();
    }

    /**
     * 重置密码1
     * @return mixed
     * @throws Exception
     */
    public function pwd_reseto()
    {
        if ($this->is_ajax) {
            $mobile = input('mobile', '');
            $code   = input('code', '');

            $result = $this->api(
                'Sms',
                'verification',
                ['telephone' => $mobile, 'code' => $code, 'type' => SmsCodeModel::FORGET_PWD]
            );

            Session::set('verify', $result['data']['verify']);
            $this->success($result['msg'], controller_url('pwd_reset'));
        }

        $this->assign('title', '重置密码');
        return $this->fetch();
    }

    /**
     * 重置密码2
     * @return mixed
     * @throws Exception
     */
    public function pwd_reset()
    {
        $verify = Session::get('verify');

        empty($verify) AND $this->redirect(controller_url('index'));

        if ($this->is_ajax) {
            if (empty($verify)) {
                $this->error('旧号码验证已失效，请重新验证！', controller_url('pwd_reseto'));
            }
            $password = trim(input('check_password', ''));
            empty($password) AND $this->error('密码不为空！');

            $result = $this->api('Member', 'verify_set_pwd', ['verify' => $verify, 'password' => md5($password)]);

            Session::delete('verify');
            $this->success($result['msg'], controller_url('index'));
        }
        $this->assign('title', '重置密码');
        return $this->fetch();
    }

    /**
     * 修改登录密码
     * @return mixed
     * @throws Exception
     */
    public function pwd_change()
    {
        $this->login_check();

        if ($this->is_ajax) {
            $old_password = trim(input('old_password', ''));
            $new_password = trim(input('new_password', ''));

            if (empty($old_password) || empty($new_password)) {
                $this->error('原密码,新密码不能为空！');
            }

            $param  = ['opassword' => md5($old_password), 'npassword' => md5($new_password)];
            $result = $this->api('Member', 'change_pwd', $param);

            $this->success($result['msg'], folder_url('User/personal'));
        }

        $this->assign('title', '修改登录密码');
        return $this->fetch();
    }

    /**
     * 绑定手机号
     * @return mixed
     * @throws Exception
     */
    public function phone_bind()
    {
        $this->login_check();

        if ($this->is_ajax) {
            $mobile   = trim(input('mobile', ''));
            $code     = trim(input('code', ''));
            $password = trim(input('password', ''));

            if (empty($mobile) || empty($code)) {
                $this->error('手机号,密码,验证码不能为空！');
            }

            $param  = ['telephone' => $mobile, 'code' => $code, 'password' => md5($password)];
            $result = $this->api('Member', 'bind_mobile', $param);

            $this->set_token($result['data']['token']);

            $this->clear_update_member_time();
            $this->success($result['msg'], folder_url('User/index'));
        }

        $this->assign('title', '绑定手机号码');
        return $this->fetch();
    }



    /**
     * 绑定身份证
     * @return mixed
     * @throws Exception
     */
    public function uid_bind()
    {
        $this->login_check();

        if ($this->is_ajax) {
            $uid   = trim(input('uid', ''));

            if (empty($uid)) {
                $this->error('身份证不能为空！');
            }

            $param  = ['uid' => $uid];
            $result = $this->api('Member', 'bind_uid', $param);


            $this->clear_update_member_time();
            $this->success($result['msg'], folder_url('User/index'));
        }
        $this->assign('title', '绑定身份证');
        return $this->fetch();
    }


    /**
     * QQ登录
     * @return void
     * @throws Exception
     */
    public function oauth_qq()
    {
        $this->oauth_login(OauthUserModel::QQ);
    }

    /**
     * 微信登录
     * @return void
     * @throws Exception
     */
    public function oauth_wx()
    {
        $this->oauth_login(OauthUserModel::WX);
    }

    /**
     * 新浪微博登录
     * @return void
     * @throws Exception
     */
    public function oauth_sinawb()
    {
        $this->oauth_login(OauthUserModel::SINAWB);
    }

    /**
     * 第三方登录回调
     * @param string $login_type
     * @throws Exception
     */
    public function oauth_callback($login_type = '')
    {
        $ouath = $this->oauth_init($login_type);
        $ouath->getAccessToken();
        $userinfo = $ouath->userinfo();
        $result = $this->api('Member', 'oauth_login', array_merge($userinfo, ['login_type' => $login_type]));
        $this->set_token($result['data']['token']);
        $this->set_member($result['data']['member']);
        $this->set_must_bind_phone($result['data']['must_bind']);
        $wx_login = $this->get_wx_login();
        if ($wx_login){
            $this->set_wx_login(null);
            $this->redirect($wx_login);
        }else{
            $this->redirect(folder_url('User/index'));
        }

    }

    /**
     * 第三方登录
     * @param string $login_type
     * @throws Exception
     */
    private function oauth_login($login_type)
    {
        $ouath = $this->oauth_init($login_type);
        $ouath->setConfig(['callback' => controller_url('oauth_callback', ['login_type' => $login_type], true, true)]);
        $this->redirect($ouath->getRequestCodeURL());
    }

    /**
     * 初始化第三方登录类
     * @param $login_type
     * @return ThinkOauth
     * @throws Exception
     */
    private function oauth_init($login_type)
    {
        switch ($login_type) {
            case OauthUserModel::QQ:
                $name = 'qq';
                break;
            case OauthUserModel::WX:
                $name = 'weixin';
                break;
            case OauthUserModel::SINAWB:
                $name = 'sinawb';
                break;
            default:
                $this->error();
                $name = '';
                break;
        }

        return ThinkOauth::instance($name);
    }


    /**
     * 超时订单取消
     * @throws \think\Exception
     * @throws \think\exception\DbException
     */
    public function timer(){
        OrderShop::timeout_order_cancel();//超时订单取消
        OrdersShop::timeout_order_cancel();//超时订单取消
    }






}
