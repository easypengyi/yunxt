<?php

namespace app\common\model;

use app\common\core\BaseModel;
use think\exception\DbException;
use think\exception\PDOException;
use think\Exception as ThinkException;

/**
 * 后台会员组关系 模型
 */
class AdminGroupRelation extends BaseModel
{
    //-------------------------------------------------- 静态方法

    /**
     * 用户会员组验证
     * @param $member_id
     * @param $group_id
     * @return bool
     * @throws ThinkException
     */
    public static function check_group($member_id, $group_id)
    {
        $where['admin_id'] = $member_id;
        $where['group_id'] = ['in', $group_id];

        return self::check($where);
    }

    /**
     * 添加用户组关联
     * @param      $new_group_id
     * @param      $admin_id
     * @return bool
     * @throws DbException
     * @throws PDOException
     * @throws ThinkException
     */
    public static function bind_group($new_group_id, $admin_id)
    {
        if (empty($new_group_id) || !is_array($new_group_id)) {
            return false;
        }

        $old_group_id = AdminGroup::user_group_group($admin_id);

        $add = array_diff($new_group_id, $old_group_id);
        $del = array_diff($old_group_id, $new_group_id);

        $change = false;

        if (!empty($add)) {
            $data = [];
            foreach ($add as $k => $v) {
                $val['group_id'] = $v;
                $val['admin_id'] = $admin_id;
                $data[]          = $val;
            }

            self::insertAll($data, true);

            $change = true;
        }

        if (!empty($del)) {
            self::delete_group($del, $admin_id);

            $change = true;
        }

        $change AND AdminGroup::cacheClear();

        return true;
    }

    /**
     * 删除用户组关联
     * @param $group_id
     * @param $admin_id
     * @return bool
     * @throws PDOException
     * @throws ThinkException
     */
    private static function delete_group($group_id, $admin_id)
    {
        if (empty($group_id)) {
            return true;
        }

        $where['admin_id'] = $admin_id;
        $where['group_id'] = ['in', $group_id];

        return self::where($where)->delete() != 0;
    }

    //-------------------------------------------------- 实例方法

    //-------------------------------------------------- 读取器方法

    //-------------------------------------------------- 追加属性读取器方法

    //-------------------------------------------------- 修改器方法

    //-------------------------------------------------- 关联加载方法
}
