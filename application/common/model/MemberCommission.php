<?php

namespace app\common\model;

use think\Exception;
use helper\StrHelper;
use app\common\core\BaseModel;
use think\exception\DbException;
use think\Log;

/**
 * 佣金明细 模型
 */
class MemberCommission extends BaseModel
{
    // 类型
    // 商城
    const SHOP = 1;
    // 提现
    const WITHDRAWALS = 2;

    //分销奖 上对下 980*数量
    const maker3 = 3;

    //批发奖  级别产品差额
    const maker4 = 4;

    //管理奖  上对下
    const maker5 = 5;

    //维护奖  下对上
    const maker6 = 6;

    //职级奖
    const maker7 = 7;

    //报单奖
    const maker8 = 8;

    //活动推荐奖
    const recommend = 10;


    //开发奖 平级推或者下推上
    const maker14 = 14;

    //城市特权奖
    const city = 15;

    //联合创始人奖金池
    const first = 11;

    //联合创始人奖金池
    const second = 12;

    //执行董事奖金池
    const three = 13;


    protected $autoWriteTimestamp = true;

    //-------------------------------------------------- 静态方法


    /**
     * @param $member_id
     * @return mixed
     * @throws \think\db\exception\BindParamException
     * @throws \think\exception\PDOException
     */
    public static function total_type_income($member_id)
    {
        $query =
 "SELECT
  SUM( CASE WHEN type = ".self::WITHDRAWALS." THEN  `value` ELSE 0 END) as WITHDRAWALS,
  SUM( CASE WHEN type = ".self::maker3." THEN   `value` ELSE 0 END) as maker3,
  SUM( CASE WHEN type = ".self::maker4." THEN   `value` ELSE 0 END) as maker4,
  SUM( CASE WHEN type = ".self::maker5." THEN   `value` ELSE 0 END) as maker5,
  SUM( CASE WHEN type = ".self::maker6." THEN   `value` ELSE 0 END) as maker6,
  SUM( CASE WHEN type = ".self::maker7." THEN   `value` ELSE 0 END) as maker7,
  SUM( CASE WHEN type = ".self::maker8." THEN   `value` ELSE 0 END) as maker8,
  SUM( CASE WHEN type = ".self::recommend." THEN   `value` ELSE 0 END) as recommend,
  SUM( CASE WHEN type = ".self::maker14." THEN   `value` ELSE 0 END) as maker14,
  SUM( CASE WHEN type = ".self::city." THEN   `value` ELSE 0 END) as city
  FROM  ydn_member_commission WHERE member_id = $member_id";
  return self::query($query);
    }





    /**
     * 佣金明细列表
     * @param $member_id
     * @return array
     * @throws DbException
     */
    public static function record_list($member_id,$type)
    {
        $field = ['commission_id', 'type', 'value', 'description', 'create_time'];

        $where['member_id'] = $member_id;

        $where['type'] = $type ;

        $order = ['create_time' => 'desc'];

        $query = self::field($field);
        $list  = self::page_list($where, $order, $query);

        return $list->toArray();
    }


    public static function record_list1($group_id){
        $field = ['commission_id', 'type', 'value', 'description', 'create_time'];
        switch ($group_id){
            case 3:
                $where['type'] = self::three;
                break;
            case 4:
                $where['type'] = self::second;
                break;
            case 5:
                $where['type'] = self::first;
                break;
        }
        $order = ['create_time' => 'desc'];
        $query = self::field($field);
        $list  = self::page_list($where, $order, $query);

        return $list->toArray();
    }



    /**
     * 佣金明细详情
     * @param $commission_id
     * @param $member_id
     * @return static
     * @throws DbException
     */
    public static function record_detail($commission_id, $member_id)
    {
        $where['commission_id'] = $commission_id;
        $where['member_id']     = $member_id;

        $field = ['commission_id', 'type', 'value', 'description', 'create_time', 'relation_id'];

        $query = self::field($field)->where($where);
        $model = self::get($query);

        if (empty($model)) {
            return null;
        }

        $model->append(['relation']);
        return $model;
    }

    /**
     * 商城支付记录
     * @param $member_id
     * @param $amount
     * @param $relation_id
     * @return MemberCommission|bool
     * @throws DbException
     * @throws Exception
     */
    public static function shop($member_id, $amount, $relation_id)
    {
        $invitation = MemberInvitation::get(['member_id' => $member_id]);
        if (empty($invitation)) {
            return true;
        }

        return self::insert_log($member_id, self::SHOP, $amount, 0, '商城支付', $relation_id, true);
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
        return self::insert_log($member_id, self::WITHDRAWALS, $value, $value, '提现至' . $account, $relation_id);
    }

    /**
     * 插入日志
     * @param        $member_id
     * @param        $type
     * @param        $amount
     * @param        $value
     * @param string $description
     * @param int    $relation_id
     * @param bool   $profit
     * @return static
     */
    public static function insert_log($member_id, $type, $amount, $value, $description = '', $relation_id = 0, $profit = false
    ) {
        $data['description'] = $description;
        $data['member_id']   = $member_id;
        $data['type']        = $type;
        $data['amount']      = $amount;
        $data['value']       = $value;
        $data['relation_id'] = $relation_id;
        return self::create($data);
    }

    /**
     * @param $data
     * @return int|string
     */
    public static function insert_log_all($data){
        return self::insertAll($data);
    }

    /**
     * 会员总收入
     * @param $member_id
     * @return float|int
     */
    public static function total_income($member_id)
    {
        $where['member_id'] = $member_id;

        return self::where($where)->sum('value');
    }







    /**
     * 消费总金额
     * @param $member_id
     * @return float|int
     */
    public static function total_amount($member_id)
    {
        $where['type']      = ['in', [self::SHOP]];
        $where['member_id'] = $member_id;

        return self::where($where)->sum('amount');
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
            case self::SHOP:
                break;
        }

        return $value;
    }

    //-------------------------------------------------- 修改器方法

    //-------------------------------------------------- 关联加载方法
}
