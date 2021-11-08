<?php

namespace app\common\model;

use app\common\core\BaseModel;
use think\exception\DbException;
use think\Exception as ThinkException;

/**
 * 短信验证码 模型
 */
class SmsCode extends BaseModel
{
    // 注册
    const REGISTER = 1;
    // 忘记密码
    const FORGET_PWD = 2;
    // 设置支付密码
    const SET_PAY_PWD = 3;
    // 更换手机号码(原)
    const CHANGE_PHONE_OLD = 4;
    // 更换手机号码(新)
    const CHANGE_PHONE_NEW = 5;
    // 绑定手机号码
    const BIND_PHONE = 6;
    // 绑定收款账户
    const BIND_RECEIVABLE_ACCOUNT = 7;
    // 短信验证登录
    const LOGIN = 8;

    protected $type = ['effective' => 'boolean'];

    //-------------------------------------------------- 静态方法

    /**
     * 添加验证码
     * @param $telephone
     * @param $code
     * @param $response
     * @param $type
     * @param $effective
     * @return int
     * @throws ThinkException
     */
    public static function insert_code($telephone, $code, $response, $type, $effective)
    {
        static::effective_code($telephone, $type);

        $data['telephone']   = $telephone;
        $data['code']        = $code;
        $data['type']        = $type;
        $data['ip']          = request()->ip();
        $data['data']        = serialize($response);
        $data['effective']   = true;
        $data['create_time'] = time();
        $data['expiry_time'] = time() + $effective;

        $model = self::create($data);

        return !empty($model);
    }

    /**
     * 验证短信验证码
     * @param $telephone
     * @param $code
     * @param $type
     * @return bool
     * @throws ThinkException
     */
    public static function check_code($telephone, $code, $type)
    {
        $where['expiry_time'] = ['>=', time()];
        $where['effective']   = true;
        $where['type']        = $type;
        $where['code']        = $code;
        $where['telephone']   = $telephone;

        return self::check($where);
    }

    /**
     * 读取验证码
     * @param $telephone
     * @param $type
     * @return static
     * @throws DbException
     */
    public static function load_code($telephone, $type)
    {
        $where['effective']   = true;
        $where['expiry_time'] = ['>=', time()];
        $where['telephone']   = $telephone;
        $where['type']        = $type;

        $query = self::where($where)->order(['create_time' => 'desc']);
        $model = self::get($query);

        if (empty($model)) {
            return null;
        }

        return $model;
    }

    /**
     * 设置短信验证码无效
     * @param $telephone
     * @param $type
     * @return bool
     * @throws ThinkException
     */
    private static function effective_code($telephone, $type)
    {
        $where['telephone'] = $telephone;
        $where['type']      = $type;

        $data['effective'] = false;

        return self::where($where)->update($data) != 0;
    }

    //-------------------------------------------------- 实例方法

    //-------------------------------------------------- 读取器方法

    //-------------------------------------------------- 追加属性读取器方法

    //-------------------------------------------------- 修改器方法

    //-------------------------------------------------- 关联加载方法
}
