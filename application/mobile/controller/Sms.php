<?php

namespace app\mobile\controller;

use Exception;
use think\Lang;
use think\Config;
use think\captcha\Captcha;
use app\common\controller\MobileController;
use app\common\model\SmsCode as SmsCodeModel;

/**
 * 短信验证码
 */
class Sms extends MobileController
{
    // 注册
    private $reg_verify_id = 'reg_id';
    // 登录
    private $login_verify_id = 'login_id';
    // 修改密码
    private $pwd_verify_id = 'pwd_id';
    // 修改手机号1
    private $phone1_verify_id = 'phone1_id';
    // 修改手机号2
    private $phone2_verify_id = 'phone2_id';
    // 绑定手机号
    private $bind_mobile_id = 'bind_phone_id';

    /**
     * 初始化方法
     * @return void
     * @throws Exception
     */
    protected function _initialize()
    {
        parent::_initialize();
    }

    /**
     * 注册
     * @return void
     * @throws Exception
     */
    public function register()
    {
        $this->verify_check($this->reg_verify_id);
        $this->send_sms(SmsCodeModel::REGISTER);
    }

    /**
     * 忘记/重置登录密码
     * @return void
     * @throws Exception
     */
    public function forget_pwd()
    {
        $this->verify_check($this->pwd_verify_id);
        $this->send_sms(SmsCodeModel::FORGET_PWD);
    }

    /**
     * 设置支付密码
     * @return void
     * @throws Exception
     */
    public function set_pay_pwd()
    {
        $this->send_sms(SmsCodeModel::SET_PAY_PWD);
    }

    /**
     * 绑定收款账户
     * @return void
     * @throws Exception
     */
    public function add_receivables_card()
    {
        $this->send_sms(SmsCodeModel::BIND_RECEIVABLE_ACCOUNT);
    }

    /**
     * 登录
     * @return void
     * @throws Exception
     */
    public function login()
    {
        $this->verify_check($this->login_verify_id);
        $this->send_sms(SmsCodeModel::LOGIN);
    }

    /**
     * 绑定手机号
     * @return void
     * @throws Exception
     */
    public function bind_mobile()
    {
        $this->send_sms(SmsCodeModel::BIND_PHONE);
    }

    /**
     * 修改手机号
     * @return void
     * @throws Exception
     */
    public function change_mobile()
    {
        $this->verify_check($this->phone1_verify_id);
        $this->send_sms(SmsCodeModel::CHANGE_PHONE_OLD);
    }

    /**
     * 修改手机号2
     * @return void
     * @throws Exception
     */
    public function change_mobile2()
    {
        $this->verify_check($this->phone2_verify_id);
        $this->send_sms(SmsCodeModel::CHANGE_PHONE_NEW);
    }

    /**
     * 绑定手机号
     * @return void
     * @throws Exception
     */
    public function bind_phone()
    {
        $this->send_sms(SmsCodeModel::BIND_PHONE);
    }

    /**
     * 发送验证码
     * @param $type
     * @return void
     * @throws Exception
     */
    private function send_sms($type)
    {
        $result = $this->api('Sms', 'sms', ['telephone' => input('mobile', ''), 'type' => $type]);
        $this->success('验证码已发送！', null, $result['data']);
    }

    //-------------------------------------------------- 验证码

    /**
     * 注册验证码
     * @return mixed
     * @throws Exception
     */
    public function reg_verify()
    {
        return $this->verify_build($this->reg_verify_id);
    }

    /**
     * 登录验证码
     * @return mixed
     * @throws Exception
     */
    public function login_verify()
    {
        return $this->verify_build($this->login_verify_id);
    }

    /**
     * 修改密码验证码
     * @return mixed
     * @throws Exception
     */
    public function pwd_verify()
    {
        return $this->verify_build($this->pwd_verify_id);
    }

    /**
     * 修改手机号1验证码
     * @return mixed
     * @throws Exception
     */
    public function phone1_verify()
    {
        return $this->verify_build($this->phone1_verify_id);
    }

    /**
     * 修改手机号2验证码
     * @return mixed
     * @throws Exception
     */
    public function phone2_verify()
    {
        return $this->verify_build($this->phone2_verify_id);
    }

    /**
     * 绑定手机号
     * @return mixed
     * @throws Exception
     */
    public function bind_phone_verify()
    {
        return $this->verify_build($this->bind_mobile_id);
    }

    /**
     * 验证码生成
     * @param $id
     * @return mixed
     * @throws Exception
     */
    private function verify_build($id)
    {
        ob_end_clean();
        $verify = new Captcha(Config::get('verify'));
        return $verify->entry($id);
    }

    /**
     * 验证码验证
     * @param $id
     * @return void
     * @throws Exception
     */
    private function verify_check($id)
    {
        $verify = new Captcha();
        if (!$verify->check(input('verify', ''), $id)) {
            $this->error(Lang::get('verifiy incorrect'));
        }
    }
}
