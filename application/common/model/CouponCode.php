<?php

namespace app\common\model;

use app\common\core\BaseModel;
use helper\StrHelper;
use helper\TimeHelper;
use think\Exception;
use think\exception\DbException;
use think\exception\PDOException;
use think\model\relation\BelongsTo;

/**
 * 优惠激活码 模型
 */
class CouponCode extends BaseModel
{
    // 状态
    // 未领取
    const STATUS_NO_HANDLE = 0;
    // 已领取
    const STATUS_RECEIVE = 1;

    protected $insert = ['activation_code', 'status' => self::STATUS_NO_HANDLE];

    protected $autoWriteTimestamp = true;

    //-------------------------------------------------- 静态方法

    /**
     * 领取成功
     * @param $code_id
     * @param $member_id
     * @return bool
     * @throws Exception
     * @throws DbException
     * @throws PDOException
     */
    public static function receive_finish($code_id, $member_id)
    {
        $where['code_id'] = $code_id;
        $where['status']  = self::STATUS_NO_HANDLE;

        $model = self::get($where);
        if (empty($model)) {
            return false;
        }
        $data['member_id']    = $member_id;
        $data['status']       = self::STATUS_RECEIVE;
        $data['receive_time'] = time();

        return $model->save($data) != 0;
    }

    /**
     * 状态数组
     * @return array
     */
    public static function status_array()
    {
        return [
            self::STATUS_NO_HANDLE => '未领取',
            self::STATUS_RECEIVE   => '已领取',
        ];
    }

    //-------------------------------------------------- 实例方法

    //-------------------------------------------------- 读取器方法

    //-------------------------------------------------- 追加属性读取器方法

    //-------------------------------------------------- 修改器方法

    /**
     * 关联会员 修改器
     * @param Member $model
     * @return Member
     */
    public function setMemberRelation($model)
    {
        $this->hidden(['member_id']);
        return $model;
    }

    /**
     * 激活码 修改器
     * @param $value
     * @return string
     */
    public function setActivationCodeAttr($value)
    {
        if (!is_null($value)) {
            return $value;
        }

        return StrHelper::random_string('alnum_lower');
    }

    //-------------------------------------------------- 关联加载方法

    /**
     * 关联会员
     * @return BelongsTo
     */
    public function member()
    {
        $relation = $this->belongsTo(Member::class, 'member_id');
        $relation->field(['member_id', 'member_nickname', 'member_tel']);
        return $relation;
    }
}
