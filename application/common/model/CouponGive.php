<?php

namespace app\common\model;

use app\common\core\BaseModel;
use think\model\relation\BelongsTo;

/**
 * 优惠赠送记录 模型
 */
class CouponGive extends BaseModel
{
    // 状态
    // 未处理
    const STATUS_NO_HANDLE = 0;
    // 处理中
    const STATUS_HANDLE = 1;
    // 已完成
    const STATUS_FINISH = 2;
    // 以失败
    const STATUS_FAIL = 3;

    protected $type = ['member_all' => 'boolean', 'member_ids' => 'serialize'];

    protected $insert = ['status' => self::STATUS_NO_HANDLE, 'send_number' => 0];

    protected $autoWriteTimestamp = true;

    //-------------------------------------------------- 静态方法

    /**
     * 赠送进行
     * @param $give_id
     * @return static
     */
    public static function give_handle($give_id)
    {
        $where = ['give_id' => $give_id, 'status' => self::STATUS_NO_HANDLE];

        return self::update(['status' => self::STATUS_HANDLE], $where);
    }

    /**
     * 赠送完成
     * @param $give_id
     * @param $number
     * @param $menber_ids
     * @return static
     */
    public static function give_finish($give_id, $number, $menber_ids)
    {
        $where = ['give_id' => $give_id, 'status' => self::STATUS_HANDLE];

        $data = ['status' => self::STATUS_FINISH, 'send_number' => $number, 'member_ids' => $menber_ids];

        return self::update($data, $where);
    }

    /**
     * 赠送失败
     * @param $give_id
     * @return static
     */
    public static function give_fail($give_id)
    {
        $where = ['give_id' => $give_id, 'status' => self::STATUS_HANDLE];

        return self::update(['status' => self::STATUS_FAIL], $where);
    }

    /**
     * 优惠券赠送总数
     * @param $template_id
     * @return int
     */
    public static function total_send_number($template_id)
    {
        $where = ['template_id' => $template_id];
        return self::where($where)->sum('send_number');
    }

    /**
     * 状态数组
     * @return array
     */
    public static function status_array()
    {
        return [
            self::STATUS_NO_HANDLE => '未处理',
            self::STATUS_HANDLE    => '处理中',
            self::STATUS_FINISH    => '已完成',
            self::STATUS_FAIL      => '已失败',
        ];
    }

    //-------------------------------------------------- 实例方法

    //-------------------------------------------------- 读取器方法

    //-------------------------------------------------- 追加属性读取器方法

    //-------------------------------------------------- 修改器方法

    //-------------------------------------------------- 关联加载方法

    /**
     * 关联管理员
     * @return BelongsTo
     */
    public function admin()
    {
        $relation = $this->belongsTo(Admin::class, 'admin_id');
        $relation->field(['admin_id', 'admin_username']);
        return $relation;
    }
}
