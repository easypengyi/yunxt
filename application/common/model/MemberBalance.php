<?php

namespace app\common\model;

use app\common\core\BaseModel;
use think\exception\DbException;

/**
 * 余额明细 模型
 */
class MemberBalance extends BaseModel
{
    // 类型
    // 后台减少
    const ADMIN_DEC = 1;
    // 后台增加
    const ADMIN_INC = 2;
    // 充值
    const RECHARGE = 3;
    // 提现
    const WITHDRAWALS = 4;
    // 活动报名
    const ACTIVITY_SIGN = 5;
    // 商城支付
    const SHOP = 6;
    //会员开通
    const VIP = 7;

    const balance_inc = 9;

    const give = 10;

    const collect = 11;

    const recharge = 12;

    const reduce = 13;

    const SHOP_CANCEL = 14; //取消订单




    protected $autoWriteTimestamp = true;

    //-------------------------------------------------- 静态方法

    /**
     * 余额明细列表
     * @param $member_id
     * @return array
     * @throws DbException
     */
    public static function record_list($member_id)
    {
        $field = ['balance_id', 'type', 'value', 'description', 'create_time'];

        $where['member_id'] = $member_id;

        $where['type'] = ['in',[self::give,self::collect,self::recharge,self::balance_inc,self::SHOP, self::SHOP_CANCEL]];

        $order = ['create_time' => 'desc'];

        $query = self::field($field);
        $list  = self::page_list($where, $order, $query);
        return $list->toArray();
    }

    /**
     * 余额明细详情
     * @param $balance_id
     * @param $member_id
     * @return static
     * @throws DbException
     */
    public static function record_detail($balance_id, $member_id)
    {
        $where['balance_id'] = $balance_id;
        $where['member_id']  = $member_id;

        $field = ['balance_id', 'type', 'value', 'description', 'create_time', 'relation_id'];

        $query = self::field($field)->where($where);
        $model = self::get($query);

        if (empty($model)) {
            return null;
        }

        $model->append(['relation']);
        return $model;
    }

    /**
     * 后台减少记录
     * @param $member_id
     * @param $value
     * @return static
     */
    public static function admin_dec($member_id, $value)
    {
        return self::insert_log($member_id, self::ADMIN_DEC, -$value, '后台减少');
    }

    /**
     * 后台增加记录
     * @param $member_id
     * @param $value
     * @return static
     */
    public static function admin_inc($member_id, $value)
    {
        return self::insert_log($member_id, self::ADMIN_INC, $value, '后台增加');
    }

    /**
     * 充值记录
     * @param $member_id
     * @param $value
     * @param $payment_name
     * @param $relation_id
     * @return static
     */
    public static function recharge($member_id, $value, $payment_name, $relation_id)
    {
        return self::insert_log($member_id, self::RECHARGE, $value, $payment_name . '充值', $relation_id);
    }

    /**
     * 会员开通记录
     * @param $member_id
     * @param $value
     * @param $payment_name
     * @param $relation_id
     * @return static
     */
    public static function vip($member_id, $value)
    {
        return self::insert_log($member_id, self::VIP, -$value, '开通会员');
    }

    /**
     * 提现记录
     * @param $member_id
     * @param $value
     * @param $account
     * @param $relation_id
     * @return static
     */
    public static function withdrawals($member_id, $value, $account, $relation_id)
    {
        return self::insert_log($member_id, self::WITHDRAWALS, $value, '提现至' . $account, $relation_id);
    }

    /**
     * 活动报名记录
     * @param $member_id
     * @param $value
     * @param $relation_id
     * @return static
     */
    public static function activity_sign($member_id, $value, $relation_id)
    {
        return self::insert_log($member_id, self::ACTIVITY_SIGN, $value, '活动报名', $relation_id);
    }

    /**
     * 商城支付记录
     * @param $member_id
     * @param $value
     * @param $relation_id
     * @return static
     */
    public static function shop($member_id, $value, $relation_id)
    {
        return self::insert_log($member_id, self::SHOP, -$value, '商城支付', $relation_id);
    }

    /**
     * 插入日志
     * @param        $member_id
     * @param        $type
     * @param        $value
     * @param string $description
     * @param int    $relation_id
     * @return static
     */
    public static function insert_log($member_id, $type, $value, $description = '', $relation_id = 0, $remark = '', $before_value = 0, $after_value = 0)
    {
        $data['description'] = $description;
        $data['member_id']   = $member_id;
        $data['type']        = $type;
        $data['value']       = $value;
        $data['before_value'] = $before_value;
        $data['after_value'] = $after_value;
        $data['relation_id'] = $relation_id;
        $data['remark'] = $remark;
        return self::create($data);
    }

    /**
     * 会员总收入
     * @param $member_id
     * @return float|int
     */
    public static function total_income($member_id)
    {
        $where['type']      = ['in', [self::ADMIN_INC, self::RECHARGE]];
        $where['member_id'] = $member_id;

        return self::where($where)->sum('value');
    }

    /**
     * 会员总支出
     * @param $member_id
     * @return float|int
     */
    public static function total_cost($member_id)
    {
        $where['type']      = ['in', [self::ADMIN_DEC, self::WITHDRAWALS, self::ACTIVITY_SIGN, self::SHOP,self::VIP]];
        $where['member_id'] = $member_id;

        return self::where($where)->sum('value');
    }
    //-------------------------------------------------- 实例方法

    //-------------------------------------------------- 读取器方法

    //-------------------------------------------------- 追加属性读取器方法

    /**
     * 关联数据 读取器
     * @param $value
     * @param $data
     * @return mixed
     */
    public function getRelationAttr($value, $data)
    {
        if (!is_null($value)) {
            return $value;
        }

        // TODO 详情数据补充
        switch ($data['type']) {
            case self::ADMIN_DEC:
                break;
        }

        return $value;
    }

    //-------------------------------------------------- 修改器方法

    //-------------------------------------------------- 关联加载方法

    public function admin()
    {
        $relation = $this->belongsTo(Admin::class, 'relation_id', 'admin_id');
        $relation->field(['admin_id', 'admin_username']);
        return $relation;
    }
}
