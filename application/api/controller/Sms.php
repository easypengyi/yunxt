<?php

namespace app\api\controller;

use Exception;
use Think\Cache;
use think\Config;
use tool\SmsTool;
use helper\ValidateHelper;
use app\common\model\SmsCode;
use app\common\controller\ApiController;
use app\common\model\Member as MemberModel;
use app\common\model\SmsCode as SmsCodeModel;
use app\common\model\Configure as ConfigureModel;
use app\common\model\OauthUser as OauthUserModel;


/**
 * 短信 API
 */
class Sms extends ApiController
{
    /**
     * 发送短信验证码接口
     * @return void
     * @throws Exception
     */
    public function sms()
    {
        $telephone = $this->get_param('telephone');
        // 验证码用途 1 注册 2 忘记密码 3 设置支付密码 4 更换手机号码(原)  5 更换手机号码(新) 6 绑定手机号码 7 绑定收款账户 8 短信验证登录
        $type = intval($this->get_param('type'));

        ValidateHelper::is_mobile($telephone) OR output_error('手机号格式不正确！');

        $register = $this->check($telephone, $type);

        $code = SmsCodeModel::load_code($telephone, $type);
        if ($code) {
            $interval = ConfigureModel::getValue('sms_interval_time');
            $second   = time() - $code['create_time'];
            $second < $interval * 60 AND output_error('两次短信时间不能间隔低于' . $interval . '分钟！');
        }

        $effective = ConfigureModel::getValue('sms_effective_time');
        $sms       = SmsTool::instance();
        if ($sms->is_open()) {
            $code    = rand(111111, 999999);
            $result  = $sms->send($telephone, $type, [$code, $effective / 60]);
            $message = '验证码发送成功！';
        } else {
            $code    = 123456;
            $result  = true;
            $message = '验证码发送成功！暂无短信验证码接口，固定为123456！';
        }
        $result AND $result = SmsCodeModel::insert_code($telephone, $code, $sms->get_result(), $type, $effective);
        $result AND output_success($message, ['register' => $register]);
        output_error('验证码发送失败！');
    }

    /**
     * 验证手机验证码接口
     * @return void
     * @throws Exception
     */
    public function verification()
    {
        $telephone = $this->get_param('telephone');
        $code      = $this->get_param('code');
        $type      = intval($this->get_param('type'));

        $this->check($telephone, $type);

        $result = SmsCodeModel::check_code($telephone, $code, $type);
        $result OR output_error('验证码错误或已过期！');

        // 缓存数据
        $verify = $code . md5($this->member_id . $telephone . time());
        $data   = ['telephone' => $telephone, 'type' => $type, 'member_id' => $this->member_id];
        Cache::tag('api')->set($verify, $data, 86400);

        output_success('', ['verify' => $verify]);
    }

    /**
     * 类型验证
     * @param $telephone
     * @param $type
     * @return bool
     * @throws Exception
     */
    private function check($telephone, $type)
    {
        $register = false;
        switch ($type) {
            case SmsCodeModel::CHANGE_PHONE_NEW:
                // 更换手机号码(新)
            case SmsCodeModel::REGISTER:
                // 注册
                $result = MemberModel::check_phone($telephone);
                $result AND output_error('手机号码已经注册！');
                break;
            case SmsCodeModel::FORGET_PWD:
                // 忘记密码
                $result = MemberModel::check_phone($telephone);
                $result OR output_error('手机号码未注册！');
                $register = true;
                break;
            case SmsCodeModel::CHANGE_PHONE_OLD:
                // 更换手机号码(原)
            case SmsCodeModel::SET_PAY_PWD:
                // 设置支付密码
                $this->check_login();
                $result = MemberModel::check_phone($telephone, $this->member_id);
                $result OR output_error('手机号码与账户不符！');
                $register = true;
                break;
            case SmsCode::BIND_PHONE:
                // 绑定手机号码
                $this->check_login();

                $result = MemberModel::check_bind_mobile($this->member_id);
                $result AND output_error('已经绑定手机号码！');

                $member_id = MemberModel::find_member($telephone);
                if (!empty($member_id)) {
                    Config::get('custom.oauth_merge_account') OR output_error('手机号码已经注册！');
                    $result = OauthUserModel::check_bind($this->oauth_from, $member_id);
                    $result AND output_error('该手机号码已经被绑定！');
                }
                break;
            case SmsCodeModel::LOGIN:
            case SmsCodeModel::BIND_RECEIVABLE_ACCOUNT:
                // 绑定收款账户
                $register = MemberModel::check_phone($telephone);
                break;
            default:
                output_error('短信验证码类型错误！');
                break;
        }
        return $register;
    }
}
