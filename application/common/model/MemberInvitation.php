<?php

namespace app\common\model;

use app\common\model\MemberInvitation as MemberInvitationModel;
use Exception;
use tool\HashidsTool;
use app\common\core\BaseModel;
use think\exception\DbException;
use think\model\relation\BelongsTo;
use think\Exception as ThinkException;

/**
 * 会员邀请 模型
 */
class MemberInvitation extends BaseModel
{
    // 级别
    // 级别-一级分销商
    const LEVEL_FIRST = 1;
    // 级别-二级分销商
    const LEVEL_SECOND = 2;
    //查看下级分销商
    const NEXT_LEVEL = 3;

    //-------------------------------------------------- 静态方法

    /**
     * 邀请列表
     * @param $invitation_id
     * @param $level
     * @return array
     * @throws DbException
     */
    public static function invitation_list($invitation_id, $level = 1)
    {
        $where = [];
        $where['invitation_id'] = $invitation_id;

        switch ($level) {
            case self::LEVEL_FIRST:
                break;
            case self::LEVEL_SECOND:
                $where['invitation_id'] = ['in', self::invitation_array($invitation_id)];
                break;
            case self::NEXT_LEVEL:
                $where[] = ['exp', MemberInvitationModel::where_in_raw(['invitation_id' => $invitation_id], 'member_id')];
                break;
            default:
                break;
        }

        $order = ['member_id' => 'desc'];

        $list = self::page_list($where, $order);
        if (!$list->isEmpty()) {
            $list->load(['Member']);
            $list->append(['total_amount', 'total_income']);
        }
        return $list->toArray();
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
     * 邀请用户数组
     * @param $invitation_id
     * @return array
     */
    public static function invitation_array($invitation_id)
    {
        $where['invitation_id'] = $invitation_id;
        return self::where($where)->column('member_id');
    }

    /**
     * 已邀请注册人数
     * @param $invitation_id
     * @return int
     * @throws ThinkException
     */
    public static function invitation_number($invitation_id)
    {
        return self::where(['invitation_id' => $invitation_id])->count();
    }

    /**
     * 添加邀请信息
     * @param $invitation_id
     * @param $member_id
     * @return bool
     * @throws DbException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public static function insert_invitation($invitation_id, $member_id)
    {
         $res = self::where(['member_id' => $invitation_id,'level'=>self::LEVEL_FIRST])->find();
         if ($res){
             return self::insert(['invitation_id' => $invitation_id, 'member_id' => $member_id,'level'=>self::LEVEL_SECOND]) != 0;
         } else{
             return self::insert(['invitation_id' => $invitation_id, 'member_id' => $member_id]) != 0;
         }

    }

    /**
     * 邀请者ID
     * @param $member_id
     * @return mixed
     */
    public static function invitation_id($member_id)
    {
        $where['member_id'] = $member_id;
        return self::where($where)->value('invitation_id', 0);
    }

    //-------------------------------------------------- 实例方法

    //-------------------------------------------------- 读取器方法

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

    //-------------------------------------------------- 追加属性读取器方法

    /**
     * 总消费 读取器
     * @param $value
     * @param $data
     * @return float|int
     */
    public function getTotalAmountAttr($value, $data)
    {
        if (!is_null($value)) {
            return $value;
        }
        return MemberCommission::total_amount($data['member_id']);
    }

    /**
     * 总收入 读取器
     * @param $value
     * @param $data
     * @return float|int
     */
    public function getTotalIncomeAttr($value, $data)
    {
        if (!is_null($value)) {
            return $value;
        }
        return MemberCommission::total_income($data['member_id']);
    }

    //-------------------------------------------------- 修改器方法

    //-------------------------------------------------- 关联加载方法

    /**
     * 关联会员
     * @return BelongsTo
     */
    public function member()
    {
        $relation = $this->belongsTo(Member::class, 'member_id');
        $relation->field(['member_id', 'member_nickname', 'member_headpic_id', 'create_time', 'member_tel']);
        $relation->where(['del' => false, 'enable' => true]);
        return $relation;
    }
}
