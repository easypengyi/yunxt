<?php

namespace app\common\model;

use app\common\core\BaseModel;
use app\common\model\Member as MemberModel;
use think\exception\DbException;

/**
 * 会员组 模型
 */
class MemberGroup extends BaseModel
{

    // 核销人员
    const STAFF = 6;

    protected $type = ['enable' => 'boolean'];

    //-------------------------------------------------- 静态方法

    /**
     * 用户组--数组
     * @param bool $enable
     * @return array
     */
    public static function group_array($enable = true)
    {
        $enable AND $where['enable'] = true;

        return self::where($where)->column('group_name', 'group_id');
    }

    public static function select_stock()
    {
        return [
            1           => '有',
            2           => '无',
        ];
    }

    /**
     * 根据会员ID获取会员组--数组
     * @param      $member_id
     * @param bool $enable
     * @return array
     * @throws DbException
     */
    public static function user_group_array($member_id, $enable = true)
    {
        $where['member_id'] = $member_id;
        $enable AND $where['enable'] = true;

        $query = self::field(['group_id', 'group_name'], false, self::getTable());
        self::table_join(MemberGroupRelation::getTable(), ['group_id' => 'group_id'], $query);
        $list = self::all_list($query, $where, []);

        return $list->column(null, 'group_id');
    }

    /**
     * 会员用户组ID组
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
