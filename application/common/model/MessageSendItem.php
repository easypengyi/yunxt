<?php

namespace app\common\model;

use app\common\core\BaseModel;

/**
 * 消息发送子表 模型
 */
class MessageSendItem extends BaseModel
{
    //-------------------------------------------------- 静态方法

    /**
     * 添加记录
     * @param $member_id
     * @param $send_id
     * @return int|string
     */
    public static function insert_item($member_id, $send_id)
    {
        $data['member_id'] = $member_id;
        $data['send_id']   = $send_id;
        return self::insert($data, true);
    }

    //-------------------------------------------------- 实例方法

    //-------------------------------------------------- 读取器方法

    //-------------------------------------------------- 追加属性读取器方法

    //-------------------------------------------------- 修改器方法

    //-------------------------------------------------- 关联加载方法
}
