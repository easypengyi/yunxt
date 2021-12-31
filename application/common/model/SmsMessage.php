<?php

namespace app\common\model;

use app\common\core\BaseModel;

/**
 * 发送短信提醒
 */
class SmsMessage extends BaseModel
{

    protected $autoWriteTimestamp = true;

    /**
     * 插入
     * @param        $member_id
     * @param        $type
     * @param        $value
     * @param string $description
     * @param int    $relation_id
     * @return static
     */
    public static function insert_log($member_id, $type, $option, $result)
    {
        $data['member_id']   = $member_id;
        $data['type']        = $type;
        $data['option']      = $option;
        $data['result']       = $result;
        return self::create($data);
    }
}
