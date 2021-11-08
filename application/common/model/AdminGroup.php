<?php

namespace app\common\model;

use app\common\core\BaseModel;
use think\exception\DbException;

/**
 * 管理员用户组 模型
 */
class AdminGroup extends BaseModel
{
    protected $type = ['enable' => 'boolean', 'rules' => 'serialize'];

    //-------------------------------------------------- 静态方法

    /**
     * 用户组--数组
     * @param bool $enable
     * @return array
     */
    public static function group_array($enable = true)
    {
        $where = [];
        $enable AND $where['enable'] = true;

        return self::where($where)->column('group_name', 'group_id');
    }

    /**
     * 根据用户id获取用户组--数组
     * @param int  $admin_id 用户ID
     * @param bool $enable   只取已开启会员组或无限制
     * @return array
     * @throws DbException
     */
    public static function user_group_array($admin_id, $enable = true)
    {
        $where['admin_id'] = $admin_id;
        $enable AND $where['enable'] = true;

        $query = self::field(['group_id', 'group_name', 'rules'], false, self::getTable());
        self::table_join(AdminGroupRelation::getTable(), ['group_id' => 'group_id'], $query);
        $list = self::all_list($query, $where, [], [true, null, self::getCacheTag()]);

        return $list->column(null, 'group_id');
    }

    /**
     * 用户组ID组
     * @param $id
     * @return array
     * @throws DbException
     */
    public static function user_group_group($id)
    {
        $group = self::user_group_array($id);
        return array_keys($group);
    }

    //-------------------------------------------------- 实例方法

    //-------------------------------------------------- 读取器方法

    //-------------------------------------------------- 追加属性读取器方法

    //-------------------------------------------------- 修改器方法

    //-------------------------------------------------- 关联加载方法
}
