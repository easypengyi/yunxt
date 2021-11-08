<?php

namespace app\common\model;

use app\common\core\BaseModel;
use think\exception\DbException;
use think\exception\PDOException;
use think\Exception as ThinkException;
use think\model\relation\BelongsTo;

/**
 * 用户组关联 模型
 */
class MemberGroupRelation extends BaseModel
{
    //-------------------------------------------------- 静态方法
    const first = 5;//联合合伙人
    const second = 4;//全球合伙人
    const three = 3;  //董事
    const four = 2;  //增长官
    const five = 1;  //创客
    const seven = 7;  //游客

    const two_price = 1580;  //增长官
    const three_price = 1380; //董事
    const four_price = 1180;  //全球合伙人
    const five_price = 980;  //联合合伙人



    /**
     * 邀请列表
     * @param $invitation_id
     * @param $level
     * @return array
     * @throws DbException
     */
    public static function invitation_list($invitation_id, $category = 1)
    {
        $where = [];
        $where['top_id'] = $invitation_id;

        switch ($category) {
            case 1:
                $where['group_id'] = ['neq', self::seven];
                break;
            case 2:
                $where['group_id'] = ['eq', self::seven];
                break;
        }

        $order = ['member_id' => 'desc'];

        $list = self::page_list($where, $order);
        if (!$list->isEmpty()) {
            $list->load(['member']);
            $list->append(['group_name','group_id']);
        }
        return $list->toArray();
    }

    /**
     * 用户会员组验证
     * @param $member_id
     * @param $group_id
     * @return bool
     * @throws ThinkException
     */
    public static function check_group($member_id, $group_id)
    {
        $where['member_id'] = $member_id;
        $where['group_id']  = ['in', $group_id];

        return self::check($where);
    }

    /**
     * 添加用户组关联
     * @param      $new_group_id
     * @param      $member_id
     * @return bool
     * @throws DbException
     * @throws PDOException
     * @throws ThinkException
     */
    public static function bind_group($new_group_id, $member_id,$invitation_id = 0)
    {
        if (empty($new_group_id) || !is_array($new_group_id)) {
            return false;
        }

        $old_group_id = MemberGroup::user_group_group($member_id);

        $add = array_diff($new_group_id, $old_group_id);
        $del = array_diff($old_group_id, $new_group_id);

        $change = false;

        if (!empty($add)) {
            $data = [];
            foreach ($add as $k => $v) {
                $val['group_id']  = $v;
                $val['member_id'] = $member_id;
                $val['top_id'] = $invitation_id;
                $data[]           = $val;
            }

            self::insertAll($data, true);

            $change = true;
        }

        if (!empty($del)) {
            self::delete_group($del, $member_id);

            $change = true;
        }

        $change AND MemberGroup::cacheClear();

        return true;
    }

    /**
     * 删除用户组关联
     * @param $group_id
     * @param $member_id
     * @return bool
     * @throws PDOException
     * @throws ThinkException
     */
    public static function delete_group($group_id, $member_id)
    {
        if (empty($group_id)) {
            return true;
        }

        $where['member_id'] = $member_id;
        $where['group_id']  = ['in', $group_id];

        return self::where($where)->delete() != 0;
    }

    /**
     * @param $member_id
     * @return mixed|string
     * @throws DbException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public static function get_group_id($member_id){
        if (empty($member_id)) {
            return '';
        }
        return self::where(['member_id'=>$member_id])->find()['group_id'];
    }



    public static function get_top($member_id){
        if (empty($member_id)) {
            return '';
        }
        return self::where(['member_id'=>$member_id])->find();
    }

    /**
     * 删除
     * @param $member_id
     * @throws PDOException
     * @throws ThinkException
     */
    public static function delete_member($member_id)
    {
        self::where(['member_id' => $member_id])->delete();
        self::cacheClear($member_id);
    }

    //-------------------------------------------------- 实例方法



    //-------------------------------------------------- 读取器方法
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
    //-------------------------------------------------- 追加属性读取器方法


    //-------------------------------------------------- 修改器方法

    //-------------------------------------------------- 关联加载方法

    //-------------------------------------------------- 多级关联加载方法
    /**
     * 关联会员 一对一 属于
     * @return BelongsTo
     */
    public function member()
    {
        $relation = $this->belongsTo(Member::class, 'member_id');
        $relation->field(['member_id', 'member_realname', 'member_tel', 'member_headpic_id','commission']);
        return $relation;
    }

}
