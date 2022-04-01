<?php

namespace app\common\model;

use tool\PaymentTool;
use app\common\core\BaseModel;
use think\exception\DbException;
use think\exception\PDOException;
use think\model\relation\BelongsTo;
use think\Exception as ThinkException;
use think\model\Collection as ModelCollection;

/**
 * 提现订单 模型
 */
class OrderWithdrawals extends BaseModel
{
    // 状态
    // 状态-已失效
    const STATUS_INVALID = 1;
    // 状态-准备提现
    const STATUS_WAIT_PAY = 2;
    // 状态-提现完成
    const STATUS_FINISH = 3;

    const TYPE_BANK = 1;
    const TYPE_WECHAT = 2;
    const TYPE_ALIPAY = 3;
    const TYPE_BALANCE = 4;


    protected $type = ['del' => 'boolean'];

    protected $insert = ['order_sn', 'order_time', 'del' => false];

    protected $file = ['pay_image_id' => 'image'];

    protected $file_head = ['pay_image_id'];

    protected $autoWriteTimestamp = true;

    //-------------------------------------------------- 静态方法

    /**
     * 待支付信息列表
     * @param $order_id
     * @param $member_id
     * @return ModelCollection|static[]
     * @throws DbException
     */
    public static function pay_list($order_id, $member_id)
    {
        $where['member_id']      = $member_id;
        $where['withdrawals_id'] = ['IN', $order_id];
        $where['del']            = false;
        $where['status']         = self::STATUS_WAIT_PAY;

        $order = ['withdrawals_id' => 'desc'];

        $list = self::all_list([], $where, $order);

        return $list;
    }

    /**
     * 提现下单
     * @param $member_id
     * @param $account
     * @param $blank
     * @param $real_name
     * @param $money
     * @param $service_money
     * @return int
     */
    public static function order_place($member_id, $account, $blank, $bank_name, $real_name, $money, $service_money, $type, $file_id)
    {
        $order['account']       = $account;
        $order['real_name']     = $real_name;
        $order['money']         = $money;
        $order['service_money'] = $service_money;
        $order['amount']        = $money + $service_money;
        $order['member_id']     = $member_id;
        $order['status']        = self::STATUS_WAIT_PAY;
        $order['bank_name']     = $bank_name;
        $order['blank']         = $blank;
        $order['type']          = $type;
        $order['pay_image_id']  = $file_id;

        $model = self::create($order);
        if (empty($model)) {
            return 0;
        }

        return $model->getAttr('withdrawals_id');
    }

    /**
     * 订单批量取消
     * @param      $member_id
     * @throws DbException
     */
    public static function order_cancel_batch($member_id = 0)
    {
        $where['status'] = self::STATUS_WAIT_PAY;
        empty($member_id) OR $where['member_id'] = $member_id;

        $list = self::all_list([], $where);

        foreach ($list as $v) {
            $v->order_cancel();
        }
    }

    /**
     * 订单状态数组
     * @return array
     */
    public static function order_status_array()
    {
        return [
            self::STATUS_INVALID  => '已失效',
            self::STATUS_WAIT_PAY => '准备提现',
            self::STATUS_FINISH   => '提现完成',
        ];
    }
    //-------------------------------------------------- 实例方法
    /**
     * 订单提现方式数组
     * @return array
     */
    public static function order_type_array()
    {
        return [
            self::TYPE_BANK  => '银行卡',
            self::TYPE_WECHAT => '微信',
            self::TYPE_ALIPAY   => '支付宝',
            self::TYPE_BALANCE   => '余额'
        ];
    }

    /**
     * 订单取消处理
     * @return void
     * @throws PDOException
     * @throws ThinkException
     */
    public function order_cancel()
    {
        if ($this->getAttr('status') !== self::STATUS_WAIT_PAY) {
            return;
        }

        if (!$this->save(['status' => self::STATUS_INVALID])) {
            return;
        }

        $member_id = $this->getAttr('member_id');
        $amount    = $this->getAttr('amount');

        Member::commission_inc($member_id, $amount);
    }

    /**
     * 订单完成
     * @param $transaction_sn
     * @return bool
     * @throws PDOException
     * @throws ThinkException
     */
    public function order_finish($transaction_sn,$remark="", $before_amount = 0, $after_amount = 0)
    {
        $member_id      = $this->getAttr('member_id');
        $account        = $this->getAttr('account');
        $amount         = $this->getAttr('amount');
        $withdrawals_id = $this->getAttr('withdrawals_id');

        if ($this->getAttr('status') != self::STATUS_WAIT_PAY) {
            return true;
        }

        $data['transaction_sn'] = $transaction_sn;
        $data['payment_time']   = time();
        $data['status']         = self::STATUS_FINISH;
        $data['remark']         = $remark;//增加备注 by shiqiren

        if (!$this->save($data)) {
            return false;
        }

        // 余额变动记录
        MemberCommission::withdrawals($member_id, $amount, $account, $withdrawals_id, $before_amount, $after_amount);
        return true;
    }

    //-------------------------------------------------- 读取器方法

    //-------------------------------------------------- 追加属性读取器方法

    /**
     * 支付类型数据 读取器
     * @param $value
     * @param $data
     * @return string
     */
    public function getPaymentAttr($value, $data)
    {
        $this->hidden(['payment_id']);

        if (!is_null($value)) {
            return $value;
        }

        return PaymentTool::instance()->payment_info($data['payment_id']);
    }

    //-------------------------------------------------- 修改器方法

    /**
     * 订单号 修改器
     * @param $value
     * @param $data
     * @return string
     */
    public function setOrderSnAttr($value, $data)
    {
        if (!is_null($value)) {
            return $value;
        }

        mt_srand($data['member_id']);
        return date('md') .
            substr(implode(null, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8) .
            substr(mt_rand(), 4, 4) .
            rand(111, 999);
    }

    /**
     * 下单时间 修改器
     * @param $value
     * @return int
     */
    public function setOrderTimeAttr($value)
    {
        if (!is_null($value)) {
            return $value;
        }

        return time();
    }

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

    //-------------------------------------------------- 关联加载方法

    /**
     * 关联会员
     * @return BelongsTo
     */
    public function member()
    {
        $relation = $this->belongsTo(Member::class, 'member_id');
        $relation->field(['member_id', 'member_realname', 'member_tel', 'member_headpic_id','real_name']);
        return $relation;
    }
}
