<?php

namespace app\common\model;

use app\common\core\BaseModel;
use think\exception\DbException;

/**
 * 余额明细 模型
 */
class MemberAccount extends BaseModel
{
    // 类型
    const TYPE_CASH = 1; //提现

    protected $autoWriteTimestamp = true;

    /**
     * 插入日志
     * @param        $member_id
     * @param        $type
     * @param        $value
     * @param string $description
     * @param int    $relation_id
     * @return static
     */
    public static function insert_log($member_id, $type, $amount, $before_amount, $after_amount, $relation_id = 0)
    {
        $data['member_id']   = $member_id;
        $data['type']        = $type;
        $data['amount']      = $amount;
        $data['before_amount']       = $before_amount;
        $data['after_amount']       = $after_amount;
        $data['relation_id'] = $relation_id;
        return self::create($data);
    }
}
