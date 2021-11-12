<?php

namespace app\common\model;

use app\common\model\Member as MemberModel;
use Exception;
use think\Config;
use think\Session;
use tool\HashidsTool;
use helper\StrHelper;
use app\common\Constant;
use helper\ValidateHelper;
use app\common\core\BaseModel;
use think\exception\DbException;
use think\exception\PDOException;
use think\Exception as ThinkException;
use think\Db;

/**
 * 会员 模型
 */
class Member extends BaseModel
{

    const FIRST_PRICE = 980;  //联合创始人
    const SECOND_PRICE = 1130; //全球合伙人
    const THREE_PRICE = 1330;  //执行董事
    const FOUR_PRICE = 1580;  //代理人
    const SEVEN_PRICE = 1800;  //体验官
    const FIVE_PRICE = 2388;  //游客

    const FIRST_RATE = 0.20;   //推荐奖励
    const SECOND_RATE = 0.17;   //推荐奖励
    const THREE_RATE = 0.14;   //推荐奖励
    const FOUR_RATE = 0.11;   //推荐奖励
    const SEVEN_RATE = 0.08;  //推荐奖励
    const FIVE_RATE = 0.05;  //推荐奖励

    const LEVEL_RATE = 0.03; //育成奖3%

    protected $type = [
        'enable'     => 'boolean',
        'del'        => 'boolean',
        'public'     => 'boolean',
        'push'       => 'boolean',
    ];

    protected $insert = ['del' => false, 'enable' => true, 'public' => true, 'push' => true];

    protected $hidden = ['member_salt', 'member_pwd', 'member_pay_pwd'];

    protected $file = ['member_headpic_id' => 'member_headpic'];

    protected $file_head = ['member_headpic_id'];

    protected $autoWriteTimestamp = true;

    //-------------------------------------------------- 静态方法

    /**
     * 初始化处理
     * @access protected
     * @return void
     */
    protected static function init()
    {
        parent::init();

        static::afterInsert(
            function (&$model) {
                /** @var static $model */
                $member_id = $model->getAttr('member_id');
                self::set_invitation($member_id);
            }
        );
    }


    /**
     * @param $member_id
     * @throws DbException
     * @throws PDOException
     * @throws ThinkException
     */
    public static function set_invitation($member_id){
        //添加邀请人
        $invitation_id =  Session::get('invitation_id');
        if (empty($invitation_id)) {
            $invitation_id = 0;
        } else {
            $invitation_id = MemberModel::find_invitation_id($invitation_id);
        }
        $group_id = Config::get('member_rule.youke_group');
        MemberGroupRelation::bind_group($group_id, $member_id, $invitation_id);

        //添加路径
        if($invitation_id > 0){
            list($path, $group, $all_path, $all_path_group) = self::getMemberPath($invitation_id, $group_id[0]);
            MemberGroupRelationModel::where(['member_id'=>$member_id])
                ->update(['top_id'=>$invitation_id, 'path'=>$path, 'path_group'=>$group, 'all_path'=>$all_path, 'all_path_group'=>$all_path_group]);
        }
    }




    /**
     * 会员注册
     * @param        $telephone
     * @param        $pwd
     * @param        $nickname
     * @param int    $headpic_id
     * @param int    $sex
     * @return static
     */
    public static function register($telephone, $pwd, $nickname, $headpic_id = 0, $sex = Constant::SEX_NO)
    {
        $data = [
            'member_tel'        => $telephone,
            'member_pwd'        => $pwd,
            'member_nickname'   => $nickname,
            'member_realname'   => $nickname,
            'member_headpic_id' => $headpic_id,
        ];

        $model = self::create($data);

        $model->interface_handle();

        return $model;
    }

    /**
     * 会员登录 使用手机号码登录
     * @param $mobile
     * @param $password
     * @return static
     * @throws DbException
     */
    public static function login($mobile, $password)
    {
        $field = [
            'member_pwd',
            'member_salt',
            'enable',
            'member_id',
            'member_tel',
            'member_nickname',
            'member_realname',
            'member_headpic_id',
            'balance',
            'commission',
            'push',
            'create_time',
            'uid',
            'is_center',
            'area',
            'account',
            'real_name',
            'blank',
        ];

        $where['member_tel'] = $mobile;
        $where['del']        = false;

        $query = self::field($field)->where($where);
        $model = self::get($query);

        if (empty($model) || !$model->password_check($password)) {
            return null;
        }

        $model->interface_handle();

        return $model;
    }

    /**
     * 获取会员信息
     * @param      $key
     * @param bool $show_all
     * @return static
     * @throws DbException
     */
    public static function member_info($key, $show_all = false)
    {
        $field = [
            'enable',
            'member_id',
            'member_tel',
            'member_nickname',
            'member_realname',
            'member_headpic_id',
            'balance',
            'commission',
            'public',
            'push',
            'create_time',
            'uid',
            'is_center',
            'area',
            'account',
            'real_name',
            'blank',
        ];

        $show_all OR $where['del'] = false;
        ValidateHelper::is_mobile($key) ? $where['member_tel'] = $key : $where['member_id'] = $key;

        $query = self::field($field)->where($where)->order(['del' => 'asc']);
        $model = self::get($query);

        if (empty($model)) {
            return null;
        }

        $model->interface_handle();

        return $model;
    }

    /**
     * 获取其他会员信息
     * @param     $user_id
     * @return static
     * @throws DbException
     */
    public static function user_info($user_id)
    {
        $field = [
            'member_id',
            'member_tel',
            'member_nickname',
            'member_realname',
            'member_headpic_id',
            'public',
            'balance',
            'commission',
            'public',
            'push',
            'create_time',
            'uid',
            'is_center',
        ];

        $where = ['member_id' => $user_id];

        $query = self::field($field)->where($where);
        $model = self::get($query);

        if (empty($model)) {
            return null;
        }

        $model->interface_handle(false);

        return $model;
    }

    /**
     * 验证手机号码是否注册
     * @param $telephone
     * @param $member_id
     * @return bool
     * @throws ThinkException
     */
    public static function check_phone($telephone, $member_id = 0)
    {
        $where['del']        = false;
        $where['member_tel'] = $telephone;
        empty($member_id) OR $where['member_id'] = $member_id;

        return self::check($where);
    }

    public static function check_city($city,$member_tel,$true)
    {
        if ($true){
            $where['member_tel'] = ['neq',$member_tel];
            $where['area'] = $city;
            $where['del']        = false;
        }else{
            $where['area'] = $city;
            $where['del']        = false;
        }
        return self::where($where)->find();
    }

    /**
     * 验证支付密码是否正确
     * @param $member_id
     * @param $password
     * @return bool
     * @throws DbException
     */
    public static function check_pay_password($member_id, $password)
    {
        $model = self::get($member_id);
        if (empty($model)) {
            return false;
        }

        return $model->pay_password_check($password);
    }

    /**
     * 验证会员是否存在
     * @param $member_id
     * @return bool
     * @throws ThinkException
     */
    public static function check_member($member_id)
    {
        $where = ['member_id' => $member_id, 'del' => false];

        return self::check($where);
    }

    /**
     * 验证是否绑定手机号码
     * @param $member_id
     * @return bool
     * @throws ThinkException
     */
    public static function check_bind_mobile($member_id)
    {
        $where = ['member_id' => $member_id, 'member_tel' => ['<>', '']];

        return self::check($where);
    }



    /**
     * 验证是否绑定身份证
     * @param $member_id
     * @return bool
     * @throws ThinkException
     */
    public static function check_bind_uid($member_id)
    {
        $where = ['member_id' => $member_id, 'uid' => ['<>', '']];

        return self::check($where);
    }

    /**
     * 支付密码是否存在判断
     * @param $member_id
     * @return bool
     * @throws ThinkException
     */
    public static function pay_password_exists($member_id)
    {
        $where = ['member_id' => $member_id, 'member_pay_pwd' => ['<>', '']];

        return self::check($where);
    }

    /**
     * 余额增加
     * @param $member_id
     * @param $balance
     * @return bool
     * @throws ThinkException
     */
    public static function balance_inc($member_id, $balance)
    {
        if (empty($balance)) {
            return true;
        }

        $where = ['member_id' => $member_id];

        return self::where($where)->setInc('balance', $balance) != 0;
    }

    /**
     * 余额减少
     * @param $member_id
     * @param $balance
     * @return bool
     * @throws ThinkException
     */
    public static function balance_dec($member_id, $balance)
    {
        if (empty($balance)) {
            return true;
        }

        $where = ['member_id' => $member_id, 'balance' => ['>=', $balance]];

        return self::where($where)->setDec('balance', $balance) != 0;
    }

    /**
     * 佣金增加
     * @param $member_id
     * @param $commission
     * @return bool
     * @throws ThinkException
     */
    public static function commission_inc($member_id, $commission)
    {
        if (empty($commission)) {
            return true;
        }
        $where = ['member_id' => $member_id];

        return self::where($where)->setInc('commission', $commission) != 0;
    }


    /**
     * 佣金减少
     * @param $member_id
     * @param $commission
     * @return bool
     * @throws ThinkException
     */
    public static function commission_dec($member_id, $commission)
    {
        if (empty($commission)) {
            return true;
        }

        $where = ['member_id' => $member_id, 'commission' => ['>=', $commission]];

        return self::where($where)->setDec('commission', $commission) != 0;
    }

    /**
     * 修改头像
     * @param $member_id
     * @param $file_id
     * @return bool
     * @throws DbException
     * @throws ThinkException
     */
    public static function change_headpic($member_id, $file_id)
    {
        $query = self::field(['member_id', 'member_headpic_id'])->where(['member_id' => $member_id]);
        $model = self::get($query);

        if (empty($model)) {
            return false;
        }

        $model->setAttr('member_headpic_id', $file_id);
        return $model->save() != 0;
    }

    /**
     * 修改昵称
     * @param $member_id
     * @param $name
     * @return bool
     * @throws DbException
     * @throws ThinkException
     */
    public static function change_nickname($member_id, $name)
    {
        $query = self::field(['member_id', 'member_nickname'])->where(['member_id' => $member_id]);
        $model = self::get($query);

        if (empty($model)) {
            return false;
        }

        if ($model->getAttr('member_nickname') === $name) {
            return true;
        }

        $model->setAttr('member_nickname', $name);
        return $model->save() != 0;
    }

    /**
     * 修改姓名
     * @param $member_id
     * @param $name
     * @return bool
     * @throws DbException
     * @throws ThinkException
     */
    public static function change_realname($member_id, $name)
    {
        $query = self::field(['member_id', 'member_realname'])->where(['member_id' => $member_id]);
        $model = self::get($query);

        if (empty($model)) {
            return false;
        }

        if ($model->getAttr('member_realname') === $name) {
            return true;
        }

        $model->setAttr('member_realname', $name);
        return $model->save() != 0;
    }





    /**
     * 修改信息公开状态
     * @param $member_id
     * @param $public
     * @return bool
     * @throws DbException
     * @throws ThinkException
     */
    public static function change_public($member_id, $public)
    {
        $query = self::field(['member_id'])->where(['member_id' => $member_id]);
        $model = self::get($query);

        if (empty($model)) {
            return false;
        }

        $model->setAttr('public', $public);
        return $model->save() != 0;
    }

    /**
     * 修改推送状态
     * @param $member_id
     * @param $push
     * @return bool
     * @throws DbException
     * @throws ThinkException
     */
    public static function change_push($member_id, $push)
    {
        $query = self::field(['member_id'])->where(['member_id' => $member_id]);
        $model = self::get($query);

        if (empty($model)) {
            return false;
        }

        $model->setAttr('push', $push);
        return $model->save() != 0;
    }

    /**
     * 修改手机号码
     * @param $member_id
     * @param $telephone
     * @param $mobile
     * @return bool
     * @throws DbException
     * @throws ThinkException
     */
    public static function change_mobile($member_id, $telephone, $mobile)
    {
        $query = self::field(['member_id'])->where(['member_tel' => $telephone, 'member_id' => $member_id]);
        $model = self::get($query);

        if (empty($model)) {
            return false;
        }

        $model->setAttr('member_tel', $mobile);
        return $model->save() != 0;
    }

    /**
     * 会员密码修改
     * @param      $member_id
     * @param      $opassword
     * @param      $npassword
     * @return bool
     * @throws DbException
     * @throws ThinkException
     */
    public static function change_password($member_id, $opassword, $npassword)
    {
        $model = self::get($member_id);

        if (empty($model)) {
            return false;
        }

        if (!$model->password_check($opassword)) {
            return false;
        }

        $model->setAttr('member_pwd', $npassword);
        return $model->save() != 0;
    }

    /**
     * 会员密码设置
     * @param     $telephone
     * @param     $password
     * @return int
     * @throws DbException
     * @throws ThinkException
     */
    public static function set_password($telephone, $password)
    {
        $model = self::get(['member_tel' => $telephone, 'del' => false]);

        if (empty($model)) {
            return 0;
        }

        $model->setAttr('member_pwd', $password);
        return $model->save() != 0 ? $model->getAttr('member_id') : 0;
    }

    /**
     * 修改支付密码
     * @param int    $member_id 用户id
     * @param string $opassword 旧密码
     * @param string $npassword 新密码
     * @return bool
     * @throws DbException
     * @throws ThinkException
     */
    public static function change_pay_password($member_id, $opassword, $npassword)
    {
        $model = self::get($member_id);

        if (empty($model)) {
            return false;
        }

        if (!$model->pay_password_check($opassword)) {
            return false;
        }

        $model->setAttr('member_pay_pwd', $npassword);
        return $model->save() != 0;
    }

    /**
     * 设置支付密码
     * @param $member_id
     * @param $password
     * @return bool
     * @throws DbException
     * @throws ThinkException
     */
    public static function set_pay_password($member_id, $password)
    {
        $model = self::get($member_id);

        if (empty($model)) {
            return false;
        }

        $model->setAttr('member_pay_pwd', $password);
        return $model->save() != 0;
    }

    /**
     * 设置身份证
     * @param $member_id
     * @param $uid
     * @return bool
     * @throws DbException
     * @throws ThinkException
     */
    public static function set_uid($member_id, $uid)
    {
        $model = self::get($member_id);

        if (empty($model)) {
            return false;
        }
        $model->setAttr('uid', $uid);
        return $model->save() != 0;
    }

    /**
     * 获取邀请人ID
     * @param $invitation
     * @return int
     * @throws Exception
     */
    public static function find_invitation_id($invitation)
    {
        return intval(HashidsTool::instance('invitation')->decode($invitation));
    }

    /**
     * 邀请码生成
     * @param $member_id
     * @return string
     * @throws Exception
     */
    public static function create_invitation($member_id)
    {
        return HashidsTool::instance('invitation')->encode($member_id);
    }

    /**
     * 邀请码解码
     * @param $member_id
     * @return string
     * @throws Exception
     */
    public static function decrypt_invitation($member_id)
    {
        return HashidsTool::instance('invitation')->decode($member_id);
    }



    /**
     * 排除未注册会员
     * @param $member
     * @return array
     */
    public static function exclude_no_register($member)
    {
        $where['member_id'] = ['in', $member];
        return self::where($where)->column('member_id');
    }

    /**
     * 查找会员ID
     * @param $telephone
     * @return int
     */
    public static function find_member($telephone)
    {
        $where['del']        = false;
        $where['member_tel'] = $telephone;

        return self::where($where)->value('member_id');
    }

    /**
     * 查找会员ID
     * @param $telephone
     * @return int
     */
    public static function find_member_city($city)
    {
        // $where['del']        = false;
        $where['area'] = $city;

        // return self::where($where)->value('member_id');
        return Db::name('member_own_areas')->where($where)->value('member_id');
    }

    /**
     * 查找会员身份证ID
     * @param $telephone
     * @return int
     */
    public static function find_member_uid($member_id)
    {
        $where['del']        = false;
        $where['member_id'] = $member_id;

        return self::where($where)->find();
    }

    /**
     * 删除会员
     * @param $member_id
     * @return void
     * @throws PDOException
     * @throws ThinkException
     */
    public static function member_delete($member_id)
    {
        self::where(['member_id' => $member_id])->update(['del' => true]);
        OauthUser::delete_oauth($member_id);
        MemberToken::delete_token($member_id);
    }

    /**
     * 检查会员是否激活
     * @param $member_id
     * @return bool
     * @throws ThinkException
     */
    public static function check_activation($member_id)
    {
        $where['member_id']  = $member_id;
        return self::check($where);
    }

    /**
     * 修改唾液盒编号
     * @param $member_id
     * @param $code
     * @return bool
     * @throws DbException
     * @throws PDOException
     * @throws ThinkException
     */
    public static function change_code($member_id, $code)
    {
        $query = self::field(['member_id'])->where(['member_id' => $member_id]);
        $model = self::get($query);

        if (empty($model)) {
            return false;
        }

        $model->setAttr('box_code', $code);
        return $model->save() != 0;
    }

    //-------------------------------------------------- 实例方法

    /**
     * 接口输出数据处理
     * @param bool $self
     * @return void
     */
    private function interface_handle($self = true)
    {
        $this->hidden(['enable', 'update_time']);

        if ($self) {
            $this->append(['is_pay_password', 'bind_mobile', 'invitation_code']);
        }
    }

    /**
     * 验证密码是否正确
     * @param $password
     * @return bool
     */
    public function password_check($password)
    {
        $data     = $this->getData();
        $password = $this->password_create($password, $data['member_salt']);

        return $password == $data['member_pwd'];
    }

    /**
     * 生成密码
     * @param $password
     * @param $salt
     * @return string
     */
    private function password_create($password, $salt)
    {
        return md5(md5($password) . md5($salt));
    }

    /**
     * 验证支付密码是否正确
     * @param $password
     * @return bool
     */
    private function pay_password_check($password)
    {
        $data     = $this->getData();
        $password = $this->pay_password_create($password, $data['create_time']);

        return $password == $data['member_pay_pwd'];
    }

    /**
     * 生成支付密码
     * @param $password
     * @param $salt
     * @return string
     */
    private function pay_password_create($password, $salt)
    {
        return md5(md5($password) . md5($salt));
    }

    //-------------------------------------------------- 读取器方法

    //-------------------------------------------------- 追加属性读取器方法

    /**
     * 邀请码读取器
     * @param $value
     * @param $data
     * @return string
     * @throws Exception
     */
    public function getInvitationCodeAttr($value, $data)
    {
        if (!is_null($value)) {
            return $value;
        }

        return self::create_invitation($data['member_id']);
    }


    /**
     * 用户组名称 读取器
     * @param $value
     * @param $data
     * @return string
     * @throws DbException
     */
    public function getGroupNameAttr($value, $data)
    {
        if (!is_null($value)) {
            return $value;
        }

        $list = MemberGroup::user_group_array($data['member_id']);

        return implode('、', array_column($list, 'group_name'));
    }



    /**
     * 团队人数 读取器
     * @param $value
     * @param $data
     * @return string
     * @throws DbException
     */
    public function getTeamNumberAttr($value, $data)
    {
        if (!is_null($value)) {
            return $value;
        }
        $a = MemberGroupRelation::all_list(['member_id', 'top_id']);
        $new_data = [];
        foreach ($a as $b) {
            if ($b['top_id'] == $data['member_id']) {
                $new_data[] = $b['member_id'];
            }
            if (in_array($b['top_id'], $new_data)) {
                $new_data[] = $b['member_id'];
            }
        }
        return count($new_data);

    }



    /**
     * 是否设置支付密码 读取器
     * @param $value
     * @param $data
     * @return bool
     * @throws ThinkException
     */
    public function getIsPayPasswordAttr($value, $data)
    {
        if (!is_null($value)) {
            return $value;
        }

        return self::pay_password_exists($data['member_id']);
    }

    /**
     * 是否绑定手机号码 读取器
     * @param $value
     * @param $data
     * @return bool
     */
    public function getBindMobileAttr($value, $data)
    {
        if (!is_null($value)) {
            return $value;
        }

        return !empty($data['member_tel']);
    }

    //-------------------------------------------------- 修改器方法

    /**
     * 密码设置 修改器
     * @param $value
     * @return string
     */
    public function setMemberPwdAttr($value)
    {
        if (empty($value)) {
            $this->setAttr('member_salt', '');
            return '';
        }

        $salt = StrHelper::random_string('alpha', 10);

        $this->setAttr('member_salt', $salt);
        return $this->password_create($value, $salt);
    }

    /**
     * 支付密码密码设置 修改器
     * @param $value
     * @param $data
     * @return string
     */
    public function setMemberPayPwdAttr($value, $data)
    {
        if (empty($value)) {
            return '';
        }

        return $this->pay_password_create($value, $data['create_time']);
    }

    //-------------------------------------------------- 关联加载方法

    public function group()
    {
        $relation = $this->belongsTo(MemberGroupRelation::class, 'member_id', 'member_id');
        $relation->field(['member_id', 'top_id']);
        return $relation;
    }

    /**
     * 获取角色和对应的价格
     *
     * @param $name
     * @return array
     */
    public static function getMemberPrice($name){
        switch ($name){
            case '代理人':
                $member_group_id = MemberGroupRelation::four;
                $member_price = MemberModel::FOUR_PRICE;
                break;
            case '执行董事':
                $member_group_id = MemberGroupRelation::three;
                $member_price = MemberModel::THREE_PRICE;
                break;
            case '全球合伙人':
                $member_group_id = MemberGroupRelation::second;
                $member_price = MemberModel::SECOND_PRICE;
                break;
            case '联合创始人':
                $member_group_id =  MemberGroupRelation::first;
                $member_price = MemberModel::FIRST_PRICE;
                break;
            default:
                $member_group_id =  MemberGroupRelation::four;
                $member_price = MemberModel::FOUR_PRICE;
                break;
        }

        return [$member_group_id, $member_price];
    }

    /**
     * 获取路径
     *
     * @param $top_id
     * @param $group_id
     * @return array
     */
    public static function getMemberPath($top_id, $group_id){
        //对应关系
        $top_info = MemberGroupRelation::get_top($top_id);
        //推荐人等级大于被推人，
        $path = [0,0,0,0,0,0];
        $group = [0,0,0,0,0,0];
        $top_path = explode(',', $top_info['path']);
        $top_group = explode(',', $top_info['path_group']);
        //游客推荐
        if($top_info['group_id'] == 7){
            if ($group_id == 7) {
                $path[0] = $top_path[0];
                $group[0] = 5;
                $path[1] = $top_path[1];
                $group[1] = $top_path[1] > 0 ? 4: 0;
                $path[2] = $top_path[2];
                $group[2] = $top_path[2] > 0 ? 3: 0;
                $path[3] = $top_path[3];
                $group[3] = $top_path[3] > 0 ? 2: 0;
            }else if ($group_id == 2) {
                $path[0] = $top_path[0];
                $group[0] = 5;
                $path[1] = $top_path[1];
                $group[1] = $top_path[1] > 0 ? 4: 0;
                $path[2] = $top_path[2];
                $group[2] = $top_path[2] > 0 ? 3: 0;
            }else if ($group_id == 3) {
                $path[0] = $top_path[0];
                $group[0] = 5;
                $path[1] = $top_path[1];
                $group[1] = $top_path[1] > 0 ? 4: 0;
            }else if ($group_id == 4) {
                $path[0] = $top_path[0];
                $group[0] = 5;
            }
        }else{
            if($top_info['group_id'] > $group_id) {
                if ($top_info['group_id'] == 5) {
                    $path[0] = $top_info['member_id'];
                    $group[0] = 5;
                } else if ($top_info['group_id'] == 4) {
                    $path[0] = $top_path[0];
                    $group[0] = 5;
                    $path[1] = $top_info['member_id'];
                    $group[1] = 4;
                } else if ($top_info['group_id'] == 3) {
                    $path[0] = $top_path[0];
                    $group[0] = 5;
                    $path[1] = $top_path[1];
                    $group[1] = 4;
                    $path[2] = $top_info['member_id'];
                    $group[2] = 3;
                }else if ($top_info['group_id'] == 2) {
                    $path[0] = $top_path[0];
                    $group[0] = 5;
                    $path[1] = $top_path[1];
                    $group[1] = 4;
                    $path[2] = $top_info[2];
                    $group[2] = 3;
                    $path[3] = $top_info['member_id'];
                    $group[3] = 2;
                }
            }else if($top_info['group_id'] == $group_id){
                $path = $top_path;
                $group = $top_group;
            }else{
                if ($group_id == 2) {
                    $path[0] = $top_path[0];
                    $group[0] = 5;
                    $path[1] = $top_path[1];
                    $group[1] = $top_path[1] > 0 ? 4: 0;
                    $path[2] = $top_path[2];
                    $group[2] = $top_path[2] > 0 ? 3: 0;;
                }else if ($group_id == 3) {
                    $path[0] = $top_path[0];
                    $group[0] = 5;
                    $path[1] = $top_path[1];
                    $group[1] = $top_path[1] > 0 ? 4: 0;
                }else if ($group_id == 4) {
                    $path[0] = $top_path[0];
                    $group[0] = 5;
                }
            }
        }
        return [implode(',', $path), implode(',', $group), $top_info['all_path'].$top_id.',', $top_info['all_path_group'].$top_info['group_id'].',', ];
    }
}
