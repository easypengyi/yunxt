<?php

namespace app\common\model;

use think\Request;
use app\common\core\BaseModel;
use think\exception\DbException;
use think\Exception as ThinkException;

/**
 * 会员token 模型
 */
class MemberToken extends BaseModel
{
    //-------------------------------------------------- 静态方法

    /**
     * 新增token
     * @param string $token
     * @param        $member_id
     * @param        $oauth_from
     * @param int    $app_type
     * @return bool
     * @throws ThinkException
     */
    public static function insert_token($token, $member_id, $oauth_from, $app_type)
    {
        self::delete_token($member_id, $app_type);

        $data = [
            'member_id'  => $member_id,
            'token'      => $token,
            'app_type'   => $app_type,
            'oauth_from' => $oauth_from,
            'ip'         => Request::instance()->ip(),
            'login_time' => time(),
        ];

        $model = self::create($data);

        return !empty($model);
    }

    /**
     * 获取token对象
     * @param string $token
     * @return static
     * @throws DbException
     */
    public static function load_token($token)
    {
        if (empty($token)) {
            return null;
        }
        return self::get(['token' => $token]);
    }

    /**
     * token 时间变更
     * @param $token
     * @return bool
     * @throws ThinkException
     */
    public static function change_time($token)
    {
        $where['token']     = $token;
        $data['ip']         = request()->ip();
        $data['login_time'] = time();

        return self::where($where)->update($data) != 0;
    }

    /**
     * 根据token删除
     * @param     $token
     * @param int $app_type
     * @return bool
     * @throws ThinkException
     */
    public static function delete_by_token($token, $app_type = 0)
    {
        $where['token'] = ['in', $token];
        empty($app_type) OR $where['app_type'] = $app_type;

        return self::where($where)->delete() != 0;
    }

    /**
     * 删除token
     * @param     $member_id
     * @param int $app_type
     * @return bool
     * @throws ThinkException
     */
    public static function delete_token($member_id, $app_type = 0)
    {
        $where['member_id'] = ['in', $member_id];
        empty($app_type) OR $where['app_type'] = $app_type;

        return self::where($where)->delete() != 0;
    }

    /**
     * 获取会员token
     * @param $member_id
     * @param $oauth_from
     * @param $app_type
     * @return MemberToken|null
     * @throws DbException
     */
    public static function member_token($member_id, $oauth_from, $app_type)
    {
        $field = ['token'];

        $where = ['member_id' => $member_id, 'oauth_from' => $oauth_from, 'app_type' => $app_type];

        $query = self::field($field)->where($where);
        $model = self::get($query);

        return $model;
    }
    //-------------------------------------------------- 实例方法

    //-------------------------------------------------- 读取器方法

    //-------------------------------------------------- 追加属性读取器方法

    //-------------------------------------------------- 修改器方法

    //-------------------------------------------------- 关联加载方法
}
