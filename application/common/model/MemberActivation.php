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
 * 会员开通 模型
 */
class MemberActivation extends BaseModel
{
    // 状态
    // 状态-已失效
    const STATUS_INVALID = 1;
    // 状态-待支付
    const STATUS_WAIT_PAY = 2;
    // 状态-已经支付
    const STATUS_ALREADY_PAY = 3;
    // 状态-配送中
    const STATUS_ALREADY_DISTRIBUTION = 4;
    // 状态-已完成
    const STATUS_FINISH = 5;

    // 退款状态
    // 退款状态-未申请退款
    const REFUND_STATUS_NO = 0;
    // 退款状态-退款申请中
    const REFUND_STATUS_APPLY = 1;
    // 退款状态-退款申请成功
    const REFUND_STATUS_SUCCESS = 2;
    // 退款状态-退款申请失败
    const REFUND_STATUS_FAIL = 3;
    // 退款状态-退款完成
    const REFUND_STATUS_FINISH = 4;

    protected $type = ['del' => 'boolean', 'extra' => 'serialize', 'address' => 'serialize', 'invoice' => 'serialize'];

    protected $insert = [
        'order_sn',
        'order_time',
        'timeout',
        'del'           => false,
        'refund_status' => self::REFUND_STATUS_NO,
    ];

    protected $autoWriteTimestamp = true;

    //-------------------------------------------------- 静态方法

    /**
     * 订单列表
     * 分页
     * @param        $member_id
     * @param int    $status
     * @return array
     * @throws DbException
     */
    public static function order_list($member_id, $status = 0)
    {
        $field = [
            'order_sn',
            'payment_id',
            'distribution_id',
            'courier_sn',
            'amount',
            'status',
            'order_time',
            'address',
            'invoice',
            'box_code',
        ];
        $where = [
            'member_id' => $member_id,
            'del'       => false,
        ];

        empty($status) OR $where['status'] = $status;

        $order = ['order_time' => 'desc'];

        $query = self::field($field);
        $list  = self::page_list($where, $order, $query);

        if (!$list->isEmpty()) {
            $list->append(['payment']);
        }

        return $list->toArray();
    }

    /**
     * 待支付信息列表
     * @param $order_id
     * @param $member_id
     * @return ModelCollection|static[]
     * @throws DbException
     */
    public static function pay_list($order_id, $member_id)
    {
        $where['member_id'] = $member_id;
        $where['order_id']  = ['IN', $order_id];
        $where['del']       = false;
        $where['status']    = self::STATUS_WAIT_PAY;

        $order = ['order_id' => 'desc'];

        $list = self::all_list([], $where, $order);

        return $list;
    }

    /**
     * 下单
     * @param $member_id
     * @param $money
     * @param $address
     * @param $invoice
     * @param $email
     * @return MemberActivation
     */
    public static function order_place($member_id, $money, $address, $invoice, $email)
    {
        $order['amount']     = $money;
        $order['extra']      = [];
        $order['address']    = $address;
        $order['invoice']    = $invoice;
        $order['email']      = $email;
        $order['member_id']  = $member_id;
        $order['payment_id'] = 0;
        $order['status']     = self::STATUS_WAIT_PAY;

        $model = self::create($order);
        if (empty($model)) {
            return new static();
        }

        return $model;
    }

    /**
     * 超时订单取消
     * @return void
     * @throws DbException
     */
    public static function timeout_order_cancel()
    {
        $where['status']  = self::STATUS_WAIT_PAY;
        $where['timeout'] = ['<', time()];

        $list = self::all_list([], $where);

        foreach ($list as $v) {
            $v->order_cancel();
        }
    }

    /**
     * 订单批量取消
     * @param int $member_id
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
            self::STATUS_INVALID              => '已失效',
            self::STATUS_WAIT_PAY             => '待支付',
            self::STATUS_ALREADY_PAY          => '待发货',
            self::STATUS_ALREADY_DISTRIBUTION => '已发货',
            self::STATUS_FINISH               => '已完成',
        ];
    }

    /**
     * 发货订单信息
     * @param $member_id
     * @return MemberActivation|null
     * @throws DbException
     */
    public static function order_distribution_info($member_id)
    {
        $where['member_id'] = $member_id;
        $where['del']       = false;
        $where['status']    = ['in', [self::STATUS_ALREADY_DISTRIBUTION, self::STATUS_FINISH]];

        $model = self::get($where);
        return $model;
    }

    //-------------------------------------------------- 实例方法

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
    }

    /**
     * 订单支付前检查
     * @return bool
     * @throws PDOException
     * @throws ThinkException
     */
    public function order_pay_check()
    {
        $result = true;

        $timeout = $this->getAttr('timeout');

        if (!empty($timeout)) {
            if ($timeout - time() <= 10) {
                $result = false;
            }
        }

        if (!$result) {
            $this->order_cancel();
        }

        return $result;
    }

    /**
     * 订单支付完成
     * @param $payment_id
     * @param $transaction_sn
     * @return bool
     * @throws PDOException
     * @throws ThinkException
     */
    public function order_pay_finish($payment_id, $transaction_sn)
    {
        $member_id = $this->getAttr('member_id');
        $money     = $this->getAttr('amount');
        if ($this->getAttr('status') != self::STATUS_WAIT_PAY) {
            return true;
        }

        $data['transaction_sn'] = $transaction_sn;
        $data['payment_time']   = time();
        $data['payment_id']     = $payment_id;
        $data['status']         = self::STATUS_ALREADY_PAY;

        if (!$this->save($data)) {
            return false;
        }

        Member::where(['member_id' => $member_id])->update(['activation' => true]);
        MemberBalance::vip($member_id, $money);
        Message::home_vip($member_id);
        CouponTemplate::activity_coupon_send($member_id);

        return true;
    }

    /**
     * 订单退款彻底完成
     * @return bool
     * @throws PDOException
     * @throws ThinkException
     */
    public function order_refund_finish()
    {
        if ($this->getAttr('status') !== self::REFUND_STATUS_SUCCESS) {
            return true;
        }

        $this->save(['status' => self::REFUND_STATUS_FINISH]);

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
     * 过期时间 修改器
     * @param $value
     * @return int
     * @throws DbException
     */
    public function setTimeoutAttr($value)
    {
        if (!is_null($value)) {
            return $value;
        }

        return $this->getAttr('order_time') + Configure::getValue('default_order_timeout_time') * 60;
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

    /**
     * 配送方式修改器
     * @param Distribution $model
     * @return Distribution
     */
    public function setDistributionRelation($model)
    {
        if (is_null($model)) {
            $data  = ['id' => 0, 'name' => ''];
            $model = new Distribution($data);
        }
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
        $relation->field(['member_id', 'member_nickname', 'member_tel', 'member_headpic_id']);
        return $relation;
    }

    /**
     * 关联配送类型 一对一 属于
     * @return BelongsTo
     */
    public function distribution()
    {
        $relation = $this->belongsTo(Distribution::class, 'distribution_id');
        $relation->field(['id', 'name']);
        return $relation;
    }
}
