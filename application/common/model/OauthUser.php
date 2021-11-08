<?php

namespace app\common\model;

use think\Cache;
use app\common\core\BaseModel;
use think\exception\DbException;
use think\exception\PDOException;
use function think\__include_file;
use think\Exception as ThinkException;

/**
 * 第三方用户 模型
 */
class OauthUser extends BaseModel
{
    // QQ
    const QQ = 1;
    // 微信
    const WX = 2;
    // 新浪微博
    const SINAWB = 3;

    //-------------------------------------------------- 静态方法

    /**
     * 会员第三方账号绑定情况
     * @param $member_id
     * @return array
     */
    public static function oauth_list($member_id)
    {
        $key = __CLASS__ . __FUNCTION__ . $member_id;

        $list = Cache::get($key);
        if (empty($list)) {
            $oauth_list = self::where(['member_id' => $member_id])->column('oauth_from');
            $oauth      = __include_file(CONF_PATH . 'ouath.php');
            $list       = [];
            foreach ($oauth as $k => $v) {
                if (isset($v['id'])) {
                    $v['image'] = base_url($v['image']);
                    $v['bind']  = in_array($v['id'], $oauth_list);
                    $list[]     = $v;
                }
            }
            Cache::set($key, $list);
            Cache::tag(self::getCacheTag(), $key);
            Cache::tag(self::getCacheTag($member_id), $key);
        }

        return $list;
    }

    /**
     * 读取第三方用户信息
     * @param $oauth_from
     * @param $openid
     * @return array
     * @throws DbException
     * @throws ThinkException
     */
    public static function load_oauth($oauth_from, $openid)
    {
        $where['oauth_from'] = $oauth_from;
        $where['openid']     = $openid;

        $query = self::field(['id', 'member_id'])->where($where);
        $model = self::get($query);

        if (empty($model)) {
            return [];
        }

        return $model->toArray();
    }

    /**
     * 会员第三方登录信息
     * @param $member_id
     * @param $oauth_from
     * @return array
     * @throws DbException
     * @throws ThinkException
     */
    public static function member_oauth($member_id, $oauth_from)
    {
        $where['oauth_from'] = $oauth_from;
        $where['member_id']  = $member_id;

        $model = self::get($where);

        if (empty($model)) {
            return [];
        }

        return $model->toArray();
    }

    /**
     * 添加第三方账号
     * @param $oauth_from
     * @param $openid
     * @param $member_id
     * @return bool
     */
    public static function insert_oauth($oauth_from, $openid, $member_id,$unionid)
    {
        $data = [
            'oauth_from'  => $oauth_from,
            'openid'      => $openid,
            'unionid'     =>$unionid,
            'member_id'   => $member_id,
            'create_time' => time(),
        ];

        $model = self::create($data);

        if (empty($model)) {
            return false;
        }

        self::cacheClear($member_id);

        return true;
    }

    /**
     * 验证账号是否绑定第三方登录
     * @param $type
     * @param $member_id
     * @return bool
     * @throws ThinkException
     */
    public static function check_bind($type, $member_id)
    {
        $where['oauth_from'] = $type;
        $where['member_id']  = $member_id;

        return self::check($where);
    }

    /**
     * 删除第三方登录信息
     * @param $member_id
     * @throws PDOException
     * @throws ThinkException
     */
    public static function delete_oauth($member_id)
    {
        self::where(['member_id' => $member_id])->delete();
        self::cacheClear($member_id);
    }

    //-------------------------------------------------- 实例方法

    //-------------------------------------------------- 读取器方法

    //-------------------------------------------------- 追加属性读取器方法

    //-------------------------------------------------- 修改器方法

    //-------------------------------------------------- 关联加载方法
}
