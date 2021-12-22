<?php

namespace app\api\controller;

use app\common\model\MemberGroup;
use app\common\model\MemberGroupRelation;
use app\common\model\OrderShop;
use app\common\model\OrdersShop;
use app\common\model\OrdersShop as OrdersShopModel;
use Exception;
use think\Cache;
use think\Config;
use think\Log;
use tool\SensitiveTool;
use thinksdk\ThinkOauth;
use app\common\Constant;
use helper\ValidateHelper;
use app\common\controller\ApiController;
use app\common\model\Member as MemberModel;
use app\common\model\SmsCode as SmsCodeModel;
use app\common\model\OauthUser as OauthUserModel;
use app\common\model\UploadFile as UploadFileModel;
use app\common\model\MemberGroup as MemberGroupModel;
use app\common\model\MemberCoupon as MemberCouponModel;
use app\common\model\MemberBalance as MemberBalanceModel;
use app\common\model\CouponTemplate as CouponTemplateModel;
use app\common\model\ProductEvaluate as ProductEvaluateModel;
use app\common\model\MemberInvitation as MemberInvitationModel;
use app\common\model\MemberCommission as MemberCommissionModel;

/**
 * 会员 API
 */
class Member extends ApiController
{
    //-------------------------------------------------- 注册

    /**
     * 注册接口
     * @return void
     * @throws Exception
     */
    public function register()
    {
        $telephone  = $this->get_param('telephone');
        $nickname   = $this->get_param('nickname', '');
        $password   = $this->get_param('password');
        $invitation = $this->get_param('invitation', ''); // 邀请码
        $code       = $this->get_param('code');

        ValidateHelper::is_mobile($telephone) OR output_error('手机号格式不正确！');

        $result = MemberModel::check_phone($telephone);
        $result AND output_error('手机号码已经注册！');

        empty($password) AND output_error('请输入密码！');

        $result = SmsCodeModel::check_code($telephone, $code, SmsCodeModel::REGISTER);
        $result OR output_error('验证码错误或已过期！');

        if (empty($invitation)) {
            $invitation_id = 0;
        } else {
            $invitation_id = MemberModel::find_invitation_id($invitation);
            (empty($invitation_id) AND Config::get('custom.invitation_code_check')) AND output_error('邀请码不存在！');
        }

        empty($nickname) AND $nickname = '会员' . time();

        $member = MemberModel::register($telephone, $password, $nickname);
        empty($member) AND output_error('注册失败！');

        $this->member_id = $member->getAttr('member_id');

        // 邀请 推荐 记录
        empty($invitation_id) OR MemberInvitationModel::insert_invitation($invitation_id, $this->member_id);

        // 邀请 推荐 赠送优惠券
        empty($invitation_id) OR CouponTemplateModel::activity_coupon_send($invitation_id);

        $list = ['member' => $this->supply_user_info($member), 'token' => $this->create_token($this->member_id)];
        output_success('', $list);
    }

    //-------------------------------------------------- 登录

    /**
     * 登录接口
     * @return void
     * @throws Exception
     */
    public function login()
    {
        $telephone = $this->get_param('telephone');
        $password  = $this->get_param('password');

        $member = MemberModel::login($telephone, $password);
        empty($member) AND output_error('用户名或密码错误！');
        $member->getAttr('enable') OR output_error('会员已被禁用！');

        $this->member_id = $member->getAttr('member_id');

//        switch ($this->app_category) {
//            case Constant::CATEGORY_MEMBER:
//                $this->check_group(MemberGroupModel::MEMBER, true, '无登录权限！');
//                break;
//            case Constant::CATEGORY_STAFF:
//                $this->check_group(MemberGroupModel::STAFF, true, '无登录权限！');
//                break;
//            default:
//                output_error('登录客户端错误！');
//                break;
//        }

        $list = [
            'member' => $this->supply_user_info($member),
            'token'  => $this->create_token($this->member_id),
        ];
        output_success('', $list);
    }

    /**
     * 短信验证码登录接口
     * @return void
     * @throws Exception
     */
    public function sms_login()
    {
        $telephone = $this->get_param('telephone');
        $code      = $this->get_param('code');

        $result = SmsCodeModel::check_code($telephone, $code, SmsCodeModel::LOGIN);
        $result OR output_error('验证码错误或已过期！');

        $member = MemberModel::member_info($telephone);
        if (empty($member)) {
            $member = MemberModel::register($telephone, '', $telephone);
        }
        $member->getAttr('enable') OR output_error('会员已被禁用！');

        $this->member_id = $member->getAttr('member_id');

        switch ($this->app_category) {
            case Constant::CATEGORY_MEMBER:
                $this->check_group(MemberGroupModel::MEMBER, true, '无登录权限！');
                break;
            default:
                output_error('登录客户端错误！');
                break;
        }

        $member->setAttr('child', null);

        $list = [
            'member' => $this->supply_user_info($member),
            'token'  => $this->create_token($this->member_id),
        ];
        output_success('', $list);
    }

    /**
     * 注销登录接口
     * @return void
     * @throws Exception
     */
    public function logout()
    {
        $this->check_login();

        $this->out_login($this->member_id);
        output_success();
    }

    //-------------------------------------------------- 第三方登录

    /**
     * 第三方登录接口
     * @return void
     * @throws Exception
     */
    public function oauth_login()
    {

        $openid     = $this->get_param('openid');
        $unionid    = $this->get_param('unionid', '');

        $login_type = $this->get_param('login_type');
        try {
            switch ($login_type) {
                // QQ登录
                case OauthUserModel::QQ:
                    $data = $this->oauth_qq($openid, $unionid);
                    break;
                // 微信登录
                case OauthUserModel::WX:
                    $data = $this->oauth_wx($openid, $unionid);
                    break;
                case OauthUserModel::SINAWB:
                    $data = $this->oauth_sinawb($openid, $unionid);
                    break;
                default:
                    $data = [];
                    break;
            }
        } catch (Exception $e) {
            output_error('登录失败！');
        }

         empty($data) AND output_error('登录失败！');

//          $openid = empty($data['unionid']) ? $data['openid'] : $data['unionid'];

          $openid = empty($data['openid']) ? $data['openid'] : $data['openid'];

          $user   = OauthUserModel::load_oauth($login_type, $openid);

        if (empty($user)) {
            $file    = UploadFileModel::insert_remote_file($data['headpic']);
            $head_id = empty($file) ? 0 : $file['file_id'];

            $member = MemberModel::register('', '', $data['nickname'], $head_id, $data['sex']);
            empty($member) AND output_error('登录失败！');

            $this->member_id = $member->getAttr('member_id');
            $member = MemberModel::member_info($this->member_id);

            $result = OauthUserModel::insert_oauth($login_type, $data['openid'], $this->member_id,$unionid);
            $result OR output_error('登录失败！');
        } else {
            $this->member_id = $user['member_id'];

            $member = MemberModel::member_info($this->member_id);
            empty($member) AND output_error('会员已删除，请联系客服！');
            $member->getAttr('enable') OR output_error('会员已被禁用！');
        }

        $this->oauth_from = $login_type;

        // 子账号数据设置
        $member->setAttr('child', null);

        $list = [
            'member'    => $this->supply_user_info($member),
            'token'     => $this->create_token($this->member_id, $this->oauth_from),
            'must_bind' => Config::get('custom.oauth_must_bind'),
        ];
        output_success('', $list);
    }

    /**
     * 第三方账号绑定情况接口
     * @return void
     * @throws Exception
     */
    public function oauth_list()
    {
        $this->check_login();

        $list = OauthUserModel::oauth_list($this->member_id);

        output_success('', ['list' => $list]);
    }

    /**
     * 第三方登录 QQ 处理
     * @param $openid
     * @param $unionid
     * @return array
     */
    private function oauth_qq($openid, $unionid)
    {
        switch ($this->app_type) {
            case Constant::CLIENT_IOS:
            case Constant::CLIENT_ANDROID:
            case Constant::CLIENT_WAP:
            case Constant::CLIENT_WEB:
                $data = [
                    'openid'   => $openid,
                    'unionid'  => $unionid,
                    'nickname' => $this->get_param('nickname', ''),
                    'headpic'  => $this->get_param('headpic', ''),
                    'sex'      => $this->get_param('sex', Constant::SEX_NO),
                ];
                break;
            default:
                $data = [];
                break;
        }
        return $data;
    }

    /**
     * 第三方登录 微信 处理
     * @param $openid
     * @param $unionid
     * @return array
     * @throws Exception
     */
    private function oauth_wx($openid, $unionid)
    {

        switch ($this->app_type) {
            case Constant::CLIENT_IOS:
            case Constant::CLIENT_ANDROID:
                $oauth = ThinkOauth::instance('weixinapp');
                $oauth->getAccessToken($openid);
                $data = $oauth->userinfo();
                break;
            case Constant::CLIENT_LITE:
                $oauth = ThinkOauth::instance('weixinlite');
                $oauth->getAccessToken($openid);
                $data = $oauth->userinfo();

                $data['nickname'] = $this->get_param('nickname', '');
                $data['headpic']  = $this->get_param('headpic', '');
                $data['sex']      = $this->get_param('sex', Constant::SEX_NO);
                break;
            case Constant::CLIENT_WX:
            case Constant::CLIENT_WEB:
                $data = [
                    'openid'   => $openid,
                    'unionid'  => $unionid,
                    'nickname' => $this->get_param('nickname', ''),
                    'headpic'  => $this->get_param('headpic', ''),
                    'sex'      => $this->get_param('sex', Constant::SEX_NO),
                ];
                break;
            default:
                $data = [];
                break;
        }


        return $data;
    }

    /**
     * 第三方登录 新浪微博 处理
     * @param $openid
     * @param $unionid
     * @return array
     * @throws Exception
     */
    private function oauth_sinawb($openid, $unionid)
    {
        switch ($this->app_type) {
            case Constant::CLIENT_WX:
            case Constant::CLIENT_WEB:
                $data = [
                    'openid'   => $openid,
                    'unionid'  => $unionid,
                    'nickname' => $this->get_param('nickname', ''),
                    'headpic'  => $this->get_param('headpic', ''),
                    'sex'      => $this->get_param('sex', Constant::SEX_NO),
                ];
                break;
            default:
                $data = [];
                break;
        }
        return $data;
    }

    //-------------------------------------------------- 会员信息


    /**
     * 我的团队
     * @return void
     * @throws Exception
     */
    public function team_list()
    {
        $this->check_login();

        $invitation_id = $this->get_param('invitation_id', 0);
        $category  = $this->get_param('category', 1);

        empty($invitation_id) AND $invitation_id = $this->member_id;

        $flag = 1;
        $top = MemberGroupRelation::get(['member_id'=>$invitation_id]);
        if(!is_null($top) && !empty($top['top_id'])){
            $parent = MemberGroupRelation::get(['member_id'=>$top['top_id']]);
            if(!is_null($parent) && !empty($parent['top_id'])){
                $top = MemberGroupRelation::get(['member_id'=>$parent['top_id']]);
                if(!is_null($parent) && $top['member_id'] == $this->member_id){
                    $flag = 2;
                }
            }
        }


        $list = MemberGroupRelation::invitation_list($invitation_id,$category);

        foreach ($list['list'] as $k=>&$v){
            $v['flag'] = $flag;
            $member_ids = $this->teamNum($v['member_id'], 1);
            $v['team_num']   = count($member_ids);
            $v['team_amount'] = $this->teamAmount($member_ids);
        }

        output_success('', $list);
    }

    public function teamAmount($member_ids){
        if(count($member_ids) == 0){
            return '0';
        }
        $wheres['member_id'] = ['in',$member_ids];
        $wheres['status'] = ['gt',OrdersShop::STATUS_WAIT_PAY];

        $num = OrdersShop::where($wheres)->sum('product_num');
        $bd_num = OrderShop::where($wheres)->sum('product_num');//报单数量

        return $num + $bd_num;
    }


    public function teamNum($user_id = 0, $type = 0)
    {
        if(!$user_id){
            $user_id = $this->member_id;
        }

        $a = MemberGroupRelation::all_list(['member_id', 'top_id']);
        $data = [];
        foreach ($a as $b) {
            if ($b['top_id'] == $user_id) {
                $data[] = $b['member_id'];
            }
            if (in_array($b['top_id'], $data)) {
                $data[] = $b['member_id'];
            }
        }
        if($type == 1){
            return $data;
        }
        return count($data);
    }


    /**
     * 我的学员
     * @return void
     * @throws Exception
     */
    public function student_list()
    {
        $this->check_login();

        $invitation_id = $this->get_param('invitation_id', 0);

        empty($invitation_id) AND $invitation_id = $this->member_id;

        $where['status'] = ['not in', [OrdersShopModel::STATUS_INVALID,OrdersShopModel::STATUS_WAIT_PAY]];
        $where['invitation_id'] = $invitation_id;
        $member_ids = OrdersShopModel::where($where)->column('member_id');

        $where = [];
        $where['member_id'] =  ['in',$member_ids];
        $where['del'] =  false;
        $list = MemberModel::page_list($where);

        output_success('', $list);
    }

    public function studentNum()
    {
        $this->check_login();
        $invitation_id = $this->get_param('invitation_id', 0);
        empty($invitation_id) AND $invitation_id = $this->member_id;
        $where['status'] = ['not in', [OrdersShopModel::STATUS_INVALID,OrdersShopModel::STATUS_WAIT_PAY]];
        $where['invitation_id'] = $invitation_id;
        $member_ids = OrdersShopModel::where($where)->column('member_id');

        return count($member_ids);
    }


    /**
     * 获取邀请人信息
     * @return void
     * @throws Exception
     */
    public function invitation_info()
    {
        $invitation_id = $this->get_param('invitation_id');

        if ($invitation_id == ''){
            $invitation_id = $this->member_id;
        }
        $member = MemberModel::user_info($invitation_id);
        empty($member) AND output_error('该用户不存在！');

        output_success('', ['member' => $this->supply_user_info($member, false)]);
    }


    /**
     * 获取用户信息接口
     * @return void
     * @throws Exception
     */
    public function info()
    {
        $this->check_login();

        $member = MemberModel::member_info($this->member_id, true);

        output_success('', ['member' => $this->supply_user_info($member)]);
    }

    /**
     * 获取其他用户信息接口
     * @return void
     * @throws Exception
     */
    public function user_info()
    {
        $user_id = $this->get_param('user_id');

        $member = MemberModel::user_info($user_id);
        empty($member) AND output_error('该用户不存在！');

        output_success('', ['member' => $this->supply_user_info($member, false)]);
    }

    /**
     * 获取其他用户信息接口
     * @return void
     * @throws Exception
     */
    public function user_info1($user_id)
    {

        $member = MemberModel::user_info($user_id);
        empty($member) AND output_error('该用户不存在！');

        output_success('', ['member' => $this->supply_user_info($member, false)]);
    }

    /**
     * 会员评价列表接口
     * @return void
     * @throws Exception
     */
    public function comment_list()
    {
        $this->check_login();

        $list = ProductEvaluateModel::member_list($this->member_id);

        output_success('', $list);
    }

    /**
     * 会员佣金记录列表接口
     * @return void
     * @throws Exception
     */
    public function commission_record_list()
    {
        $this->check_login();

        $type = $this->get_param('type');

        $list = MemberCommissionModel::record_list($this->member_id,$type);

        output_success('', $list);
    }

    /**
     * 会员佣金记录列表接口
     * @return void
     * @throws Exception
     */
    public function commission_record_list1()
    {
        $this->check_login();
        $group_id = MemberGroupRelation::get_group_id($this->member_id);
        $list = MemberCommissionModel::record_list1($group_id);
        output_success('', $list);
    }

    //-------------------------------------------------- 会员信息处理

    /**
     * 会员信息补充
     * @param MemberModel $member
     * @param bool        $self
     * @return mixed
     * @throws Exception
     */
    private function supply_user_info($member, $self = true)
    {
        $member['group_id'] = MemberGroupRelation::get_group_id($this->member_id);
        $member['group_name'] = MemberGroup::where(['group_id'=> $member['group_id']])->find()['group_name'];
        return $member;
    }

    //-------------------------------------------------- 会员余额信息

    /**
     * 余额收入信息
     * @return void
     * @throws Exception
     */
    public function balance_info()
    {
        $this->check_login();

        $data['total_income'] = MemberBalanceModel::total_income($this->member_id);
        $data['total_cost']   = MemberBalanceModel::total_cost($this->member_id);

        output_success('', $data);
    }

    /**
     * 会员余额记录列表
     * 分页
     * @return void
     * @throws Exception
     */
    public function balance_record_list()
    {
        $this->check_login();

        $list = MemberBalanceModel::record_list($this->member_id);

        output_success('', $list);
    }

    /**
     * 会员余额记录详情
     * @return void
     * @throws Exception
     */
    public function balance_record_detail()
    {

        $balance_id = $this->get_param('balance_id');

        $this->check_login();

        $detail = MemberBalanceModel::record_detail($balance_id, $this->member_id);
        empty($detail) AND output_error('余额记录已删除！');

        output_success('', $detail);
    }

    //-------------------------------------------------- 分销商

    /**
     * 邀请列表
     * @return void
     * @throws Exception
     */
    public function invitation_list()
    {
        $this->check_login();

        $level         = $this->get_param('level', 1);
        $invitation_id = $this->get_param('invitation_id', 0);

        empty($invitation_id) AND $invitation_id = $this->member_id;

        $list = MemberInvitationModel::invitation_list($invitation_id, $level);

        output_success('', $list);
    }

    //-------------------------------------------------- 会员信息修改

    /**
     * 会员信息修改接口
     * @return void
     * @throws Exception
     */
    public function member_info_change()
    {
        $this->check_login();
        $account = $this->get_param('account');
        $blank = $this->get_param('blank');
        $real_name = $this->get_param('real_name');
        $file = null;
        if ($this->request->file()) {
            $result = $this->upload_thumb();
            $result['status'] OR output_error($result['message']);
            $file = $result['data'];
        }

        $data = [
            'account' =>$account,
            'blank' => $blank,
            'real_name'=> $real_name,
        ];
        empty($file) OR $data['member_headpic_id'] = $file['file_id'];
        $member = MemberModel::get($this->member_id);
        $member->save($data);
        output_success('会员信息修改成功！');
    }

    /**
     * 修改密码接口
     * @return void
     * @throws Exception
     */
    public function change_pwd()
    {
        $opassword = $this->get_param('opassword');
        $npassword = $this->get_param('npassword');
        $this->check_login();

        $opassword == $npassword AND output_error('原密码与新密码不能相同！');

        $result = MemberModel::change_password($this->member_id, $opassword, $npassword);

        $result OR output_error('原密码错误！修改密码失败！');
        $this->out_login($this->member_id);
        output_success('修改密码成功！');
    }

    /**
     * 设置密码接口 -- 直接修改
     * @return void
     * @throws Exception
     */
    public function set_pwd()
    {
        $telephone = $this->get_param('telephone');
        $password  = $this->get_param('password');
        $code      = $this->get_param('code');

        $result = MemberModel::check_phone($telephone);
        $result OR output_error('手机号码未注册！');

        $result = SmsCodeModel::check_code($telephone, $code, SmsCodeModel::FORGET_PWD);
        $result OR output_error('验证码错误或已过期！');

        $member_id = MemberModel::set_password($telephone, $password);
        empty($member_id) AND output_error('修改密码失败！请重新尝试！');
        $this->out_login($member_id);
        output_success('修改密码成功！');
    }

    /**
     * 设置密码接口 -- 先验证短信后修改
     * @return void
     * @throws Exception
     */
    public function verify_set_pwd()
    {
        $verify   = $this->get_param('verify');
        $password = $this->get_param('password');

        $info = Cache::get($verify);

        if (empty($info) || $info['type'] != SmsCodeModel::FORGET_PWD) {
            output_error('手机验证失败！请重新验证！');
        }

        $member_id = MemberModel::set_password($info['telephone'], $password);
        empty($member_id) AND output_error('修改密码失败！请重新尝试！');
        $this->out_login($member_id);
        Cache::rm($verify);
        output_success('修改密码成功！');
    }

    /**
     * 修改支付密码接口
     * @return void
     * @throws Exception
     */
    public function change_pay_password()
    {
        $opassword = $this->get_param('opassword');
        $npassword = $this->get_param('npassword');
        $this->check_login();

        $opassword == $npassword AND output_error('原密码与新密码不能相同！');

        $result = MemberModel::change_pay_password($this->member_id, $opassword, $npassword);
        $result OR output_error('原支付密码错误！修改支付密码失败！');
        output_success('修改支付密码成功！');
    }

    /**
     * 设置支付密码接口 -- 直接修改
     * @return void
     * @throws Exception
     */
    public function set_pay_password()
    {
        $telephone = $this->get_param('telephone');
        $password  = $this->get_param('password');
        $code      = $this->get_param('code');
        $this->check_login();

        $result = MemberModel::check_phone($telephone, $this->member_id);
        $result OR output_error('手机号码错误！');

        $result = SmsCodeModel::check_code($telephone, $code, SmsCodeModel::SET_PAY_PWD);
        $result OR output_error('验证码错误或已过期！');

        $result = MemberModel::set_pay_password($this->member_id, $password);
        $result OR output_success('支付密码设置失败！');
        output_success('设置支付密码成功！');
    }

    /**
     * 设置支付密码接口 -- 先验证短信后修改
     * @return void
     * @throws Exception
     */
    public function verify_set_pay_password()
    {
        $verify   = $this->get_param('verify');
        $password = $this->get_param('password');
        $this->check_login();

        $info = Cache::get($verify);
        Cache::rm($verify);

        if (empty($info) || $info['type'] != SmsCodeModel::SET_PAY_PWD || $info['member_id'] != $this->member_id) {
            output_error('手机验证失败！请重新验证！');
        }

        $result = MemberModel::set_pay_password($this->member_id, $password);
        $result OR output_success('支付密码设置失败！');
        output_success('设置支付密码成功！');
    }

    /**
     * 绑定手机号码接口
     * @return void
     * @throws Exception
     */
    public function bind_mobile()
    {
        $telephone = $this->get_param('telephone');
        $code      = $this->get_param('code');
        $this->check_login();

        $result = MemberModel::check_bind_mobile($this->member_id);
        $result AND output_error('已经绑定手机号码！');

        $result = SmsCodeModel::check_code($telephone, $code, SmsCodeModel::BIND_PHONE);
        $result OR output_error('验证码错误或已过期！');

        $member_id = MemberModel::find_member($telephone);
        if (empty($member_id)) {
            $result = MemberModel::change_mobile($this->member_id, '', $telephone);
            $result OR output_success('号码绑定失败！');
            if (Config::get('custom.bind_mobile_set_pwd')) {
                $password = $this->get_param('password');
                MemberModel::set_password($telephone, $password);
            }
        } else {
            Config::get('custom.oauth_merge_account') OR output_error('该手机号码已经被绑定！');
            empty($this->oauth_from) AND output_error('非第三方登录无法绑定！');
            $result = OauthUserModel::check_bind($this->oauth_from, $member_id);
            $result AND output_error('该手机号码已经被绑定！');
            try {
                $oauth = OauthUserModel::member_oauth($this->member_id, $this->oauth_from);
                $member_headpic_id = MemberModel::user_info($this->member_id)['member_headpic_id'];
                MemberModel::member_delete($this->member_id);
                OauthUserModel::insert_oauth($oauth['oauth_from'], $oauth['openid'], $member_id,'');
                MemberModel::where(['member_id'=>$member_id])->setField('member_headpic_id',$member_headpic_id);
                $this->member_id = $member_id;
            } catch (Exception $e) {
                output_error('号码绑定失败！');
            }
        }

        output_success('', ['token' => $this->create_token($this->member_id, $this->oauth_from)]);
    }

    /**
     * 绑定身份证
     * @return void
     * @throws Exception
     */
    public function bind_uid()
    {
        $uid = $this->get_param('uid');
        $this->check_login();
        $result = MemberModel::check_bind_uid($this->member_id);
        $result AND output_error('已经绑定身份证！');
        MemberModel::set_uid($this->member_id,$uid);
        output_success('绑定成功');
    }

    /**
     * 更换手机号码接口
     * @return void
     * @throws Exception
     */
    public function change_mobile()
    {
        $verify    = $this->get_param('verify');
        $telephone = $this->get_param('telephone');
        $code      = $this->get_param('code');
        $this->check_login();

        $result = SmsCodeModel::check_code($telephone, $code, SmsCodeModel::CHANGE_PHONE_NEW);
        $result OR output_error('验证码错误或已过期！');

        $info = Cache::get($verify);
        Cache::rm($verify);

        if (empty($info) || $info['type'] != SmsCodeModel::CHANGE_PHONE_OLD || $info['member_id'] != $this->member_id) {
            output_error('手机验证失败！请重新验证！');
        }

        $result = MemberModel::change_mobile($this->member_id, $info['telephone'], $telephone);
        $result OR output_success('修改失败！');
        $this->out_login($this->member_id);
        output_success();
    }

    /**
     * 修改头像接口
     * @return void
     * @throws Exception
     */
    public function change_headpic()
    {
        $this->check_login();

        $result = $this->upload_thumb();
        $result['status'] OR output_error($result['message']);
        $file = $result['data'];

        $headpic = MemberModel::change_headpic($this->member_id, $file['file_id']);

        empty($headpic) AND output_error('上传失败！');
        output_success('', $file);
    }

    /**
     * 修改昵称接口
     * @return void
     * @throws Exception
     */
    public function change_nickname()
    {
        $name = $this->get_param('name');
        $this->check_login();

        empty($name) AND output_error('提交内容不能为空！');

        $result = SensitiveTool::instance()->found_word($name);
        $result AND output_error('存在违禁内容！');

        $result = MemberModel::change_nickname($this->member_id, $name);
        $result OR output_error('修改失败！');
        output_success('修改成功！');
    }

    /**
     * 修改姓名接口
     * @return void
     * @throws Exception
     */
    public function change_realname()
    {
        $name = $this->get_param('name');
        $this->check_login();

        empty($name) AND output_error('提交内容不能为空！');

        $result = SensitiveTool::instance()->found_word($name);
        $result AND output_error('存在违禁内容！');

        $result = MemberModel::change_realname($this->member_id, $name);
        $result OR output_error('修改失败！');
        output_success('修改成功！');
    }



    /**
     * 修改个人信息公开状态接口
     * @return void
     * @throws Exception
     */
    public function change_public()
    {
        $public = boolval($this->get_param('public'));
        $this->check_login();

        $result = MemberModel::change_public($this->member_id, $public);
        $result OR output_error('修改失败！');
        output_success();
    }

    /**
     * 修改推送状态接口
     * $token 用户token
     * @return void
     * @throws Exception
     */
    public function change_push()
    {
        $push = boolval($this->get_param('push'));
        $this->check_login();

        $result = MemberModel::change_push($this->member_id, $push);
        $result OR output_error('修改推送状态失败！');
        output_success('修改推送状态成功！');
    }

    /**
     * 获取用户信息接口
     * @return void
     * @throws Exception
     */
    public function member_info()
    {
        $mobile = $this->get_param('mobile');

        $member = MemberModel::member_info($mobile);
        empty($member) AND output_error('该用户不存在！');

        output_success('', ['member' => $member]);
    }
}
