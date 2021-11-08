<?php

namespace app\common\model;

use think\Queue;
use think\db\Query;
use helper\TimeHelper;
use tool\payment\PaymentOrder;
use app\common\core\BaseModel;
use think\exception\DbException;
use think\exception\PDOException;
use app\common\job\RefundCallback;
use think\model\relation\BelongsTo;
use think\Exception as ThinkException;
use think\model\Collection as ModelCollection;

/**
 * 商城订单 模型
 */
class ActivityOrderShop extends BaseModel
{
    // 状态
    // 状态-无
    // 状态-已失效
    const STATUS_INVALID = 1;
    // 状态-待支付
    const STATUS_WAIT_PAY = 2;
    // 状态-检测中
    const STATUS_CHECKING = 5;
    // 状态-已完成
    const STATUS_FINISH = 6;


    // 分类
    // 分类-全部
    const CATEGORY_ALL = 0;


    // 订单类型
    // 类型-基础
    const TYPE_BASE = 1;

    // 自动类型转换
    protected $type = ['del' => 'boolean', 'hide' => 'boolean', 'address' => 'serialize', 'invoice' => 'serialize'];

    protected $insert = [
        'order_sn',
        'order_time',
        'timeout',
        'amount',
        'del'           => false,
        'status'        => self::STATUS_WAIT_PAY,
        'refund_status' => 0,
    ];

    protected $file = ['product_image_id' => 'product_image'];

    //-------------------------------------------------- 静态方法

    /**
     * 前端订单列表
     * @param        $member_id
     * @param        $category
     * @param string $keyword
     * @return array
     * @throws DbException
     */
    public static function client_list($member_id, $category, $keyword = '')
    {
        $where['del']       = false;
        $where['hide']      = false;
        $where['member_id'] = $member_id;
        empty($keyword) OR $where['order_sn|product_name'] = $keyword;

        switch ($category) {
            case self::CATEGORY_ALL:
                $where['status'] = ['not in', [self::STATUS_INVALID]];
                break;
            case self::STATUS_WAIT_PAY:
                $where['status'] = self::STATUS_WAIT_PAY;
                break;
            case self::STATUS_CHECKING:
                $where['status'] = self::STATUS_CHECKING;
                break;
            case self::STATUS_FINISH:
                $where['status']        = self::STATUS_FINISH;
                break;
        }

        $field = [
            'order_id',
            'order_sn',
            'courier_sn',
            'amount',
            'product_id',
            'product_name',
            'product_image_id',
            'unit_price',
            'original_unit_price',
            'status',
            'refund_status',
            'order_time',
            'user_name',
            'user_phone',
        ];
        $order = ['order_time' => 'desc'];

        $query = self::field($field);
        $list  = self::page_list($where, $order, $query);
        if (!$list->isEmpty()) {
            $list->load(['Report']);
        }

        return $list->toArray();
    }


    public static function order3_list($member_id)
    {
        $where['del']       = false;
        $where['hide']      = false;
        $where['mechanism_id'] = $member_id;
        empty($keyword) OR $where['order_sn|product_name'] = $keyword;

        $field = [
            'order_id',
            'order_sn',
            'courier_sn',
            'amount',
            'product_id',
            'product_name',
            'product_image_id',
            'unit_price',
            'original_unit_price',
            'status',
            'refund_status',
            'order_time',
        ];
        $order = ['order_time' => 'desc'];

        $query = self::field($field);
        $list  = self::page_list($where, $order, $query);
        if (!$list->isEmpty()) {
            $list->load(['Report']);
        }

        return $list->toArray();
    }


    /**
     * 详情
     * @param     $order_id
     * @param int $member_id
     * @return static
     * @throws DbException
     */
    public static function detail($order_id, $member_id = 0)
    {
        $where['order_id'] = $order_id;
        empty($member_id) OR $where['member_id'] = $member_id;
        $where['hide'] = false;
        $where['del']  = false;

        $field = [
            'order_id',
            'order_type',
            'member_id',
            'order_sn',
            'courier_sn',
            'payment_id',
            'money',
            'amount',
            'status',
            'refund_status',
            'order_time',
            'finish_time',
            'payment_time',
            'remark',
            'product_id',
            'product_name',
            'product_image_id',
        ];

        $query = self::field($field)->where($where);
        $model = self::get($query);

        if (empty($model)) {
            return null;
        }

        $model->eagerlyResult($model, ['Member']);
        return $model;
    }





    /**
     * 商城下单 批量下单
     * @param $order
     * @return array|bool
     * @throws PDOException
     * @throws ThinkException
     */
    public static function order_place_batch($order)
    {
        $data = [
            'order_id' => [],
            'order_sn' => [],
            'money'    => '0.00',
            'no_pay'   => false,
        ];
        foreach ($order as $k => $v) {
            $o = self::create($v);

            $amount = $o->getAttr('amount');
            if (empty($amount) || $amount == '0.00') {
                $o->order_pay_finish(0, '');
            } else {
                $data['order_id'][] = $o->getAttr('order_id');
                $data['order_sn'][] = $o->getAttr('order_sn');
                $data['money']      = bcadd($data['money'], $amount, 2);
            }
        }

        $data['order_id'] = implode(',', $data['order_id']);
        $data['order_sn'] = implode(',', $data['order_sn']);
        $data['no_pay']   = $data['money'] == '0.00';

        return $data;
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
        $where['order_id']  = ['in', $order_id];
        $where['del']       = false;
        $where['status']    = self::STATUS_WAIT_PAY;

        $list = self::all_list([], $where, ['order_id' => 'desc']);

        return $list;
    }



    /**
     * 超时订单取消
     * @return void
     * @throws DbException
     * @throws ThinkException
     */
    public static function timeout_order_cancel()
    {
        $where['status']  = self::STATUS_WAIT_PAY;
        $where['timeout'] = ['<', time()];

        $query = self::where($where);
        $list  = self::all_list($query);

        foreach ($list as $v) {
            $v->order_cancel();
        }
    }

    /**
     * 订单批量取消
     * @param int  $member_id
     * @param int  $order_id
     * @param bool $return
     * @return boolean
     * @throws DbException
     */
    public static function order_cancel_batch($member_id = 0, $order_id = null, $return = false)
    {
        $where['status'] = self::STATUS_WAIT_PAY;
        empty($member_id) OR $where['member_id'] = $member_id;
        is_null($order_id) OR $where['order_id'] = ['in', $order_id];

        $list = self::all_list([], $where);
        if (empty($list)) {
            return false;
        }

        foreach ($list as $v) {
            $result = $v->order_cancel();
            if (!$result && $return) {
                return false;
            }
        }

        return true;
    }

    /**
     * 订单完成
     * @param $order_id
     * @return bool
     * @throws PDOException
     * @throws ThinkException
     */
    public static function order_finish($order_id)
    {
        $where['order_id'] = $order_id;
        $where['status']   = self::STATUS_CHECKING;

        return self::where($where)->update(['status' => self::STATUS_FINISH, 'finish_time' => time()]) != 0;
    }

    /**
     * 未支付订单数量
     * @param $member_id
     * @return int
     * @throws ThinkException
     */
    public static function order_no_pay_number($member_id)
    {
        if (empty($member_id)) {
            return 0;
        }

        $where['member_id'] = $member_id;
        $where['del']       = false;
        $where['hide']      = false;
        $where['status']    = self::STATUS_WAIT_PAY;
        return self::where($where)->count();
    }

    /**
     * 待发货订单数量
     * @param $member_id
     * @return int
     * @throws ThinkException
     */
    public static function order_no_deliver_number($member_id)
    {
        if (empty($member_id)) {
            return 0;
        }

        $where['member_id']     = $member_id;
        $where['del']           = false;
        $where['hide']          = false;
        $where['status']        = self::STATUS_ALREADY_PAY;
        $where['refund_status'] = ['in', [self::REFUND_STATUS_NO, self::REFUND_STATUS_FAIL]];
        return self::where($where)->count();
    }

    /**
     * 待评价订单数量
     * @param $member_id
     * @return int
     * @throws ThinkException
     */
    public static function order_no_evaluate_number($member_id)
    {
        if (empty($member_id)) {
            return 0;
        }

        $where['member_id']     = $member_id;
        $where['del']           = false;
        $where['hide']          = false;
        $where['status']        = self::STATUS_FINISH;
        $where['refund_status'] = ['in', self::REFUND_STATUS_NO, self::REFUND_STATUS_FAIL];
        return self::where($where)->count();
    }

    /**
     * 退货退款订单数量
     * @param $member_id
     * @return int
     * @throws ThinkException
     */
    public static function order_refund_number($member_id)
    {
        if (empty($member_id)) {
            return 0;
        }

        $where['member_id']     = $member_id;
        $where['del']           = false;
        $where['hide']          = false;
        $where['refund_status'] = ['in', [self::REFUND_STATUS_APPLY]];
        return self::where($where)->count();
    }

    /**
     * 订单付款验证
     * @param $order_id
     * @param $member_id
     * @return bool
     * @throws ThinkException
     */
    public static function order_payment_check($order_id, $member_id)
    {
        if (empty($member_id) || empty($order_id)) {
            return false;
        }

        $where['member_id'] = $member_id;
        $where['del']       = false;
        $where['hide']      = false;
        $where['order_id']  = ['in', $order_id];
        $where['status']    = ['<>', self::STATUS_WAIT_PAY];

        return self::check($where);
    }

    /**
     * 下单缓存key
     * @param      $member_id
     * @param      $apptype
     * @return string
     */
    public static function order_cache_key($member_id, $apptype)
    {
        return 'order_' . md5($member_id . $apptype) . '_' . time();
    }

    /**
     * 下单缓存key检测
     * @param      $order_key
     * @param      $member_id
     * @param      $apptype
     * @return bool
     */
    public static function order_cache_key_check($order_key, $member_id, $apptype)
    {
        $array = explode('_', $order_key);

        if (count($array) !== 3) {
            return false;
        }

        return $array[1] === md5($member_id . $apptype);
    }

    /**
     * 订单状态数组
     * @return array
     */
    public static function order_status_array()
    {
        return [
            self::STATUS_INVALID             => '已失效',
            self::STATUS_WAIT_PAY             => '待支付',
            self::STATUS_CHECKING             => '待检测',
            self::STATUS_FINISH               => '已完成',
        ];
    }



    /**
     * 检查订单是否已购
     * @param $member_id
     * @param $product_id
     * @return bool
     * @throws ThinkException
     */
    public static function check_purchased($member_id, $product_id)
    {
        $where['member_id']     = $member_id;
        $where['product_id']    = $product_id;
        $where['status']        = ['not in', [self::STATUS_NO, self::STATUS_INVALID, self::STATUS_WAIT_PAY]];
        $where['refund_status'] = ['not in', [self::REFUND_STATUS_SUCCESS, self::REFUND_STATUS_FINISH]];

        return self::check($where);
    }

    /**
     * 订单已购信息
     * @param $member_id
     * @param $product_id
     * @return OrderShop|null
     * @throws DbException
     */
    public static function order_purchased($member_id, $product_id)
    {
        $where['member_id']     = $member_id;
        $where['product_id']    = $product_id;
        $where['status']        = ['not in', [self::STATUS_NO, self::STATUS_INVALID, self::STATUS_WAIT_PAY]];
        $where['refund_status'] = ['not in', [self::REFUND_STATUS_SUCCESS, self::REFUND_STATUS_FINISH]];

        return self::get($where);
    }

    /**
     * 订单已购列表
     * @param $member_id
     * @param $product_id
     * @return string
     * @throws DbException
     */
    public static function order_purchased_list($member_id)
    {
        $field = ['order_id', 'product_id as order_product_id'];

        $where['member_id']     = $member_id;
        $where['status']        = ['not in', [self::STATUS_NO, self::STATUS_INVALID, self::STATUS_WAIT_PAY]];
        $where['refund_status'] = ['not in', [self::REFUND_STATUS_SUCCESS, self::REFUND_STATUS_FINISH]];

        return self::field($field, false, self::getTable())->where($where)->buildSql();
    }

    /**
     * 订单分类已购买数量
     * @param     $category_id
     * @param     $level
     * @param int $member_id
     * @return int|string
     * @throws DbException
     * @throws ThinkException
     */
    public static function category_number($category_id, $level, $member_id = 0)
    {
        $where['member_id']     = $member_id;
        $where['status']        = ['not in', [self::STATUS_NO, self::STATUS_INVALID, self::STATUS_WAIT_PAY]];
        $where['refund_status'] = ['not in', [self::REFUND_STATUS_SUCCESS, self::REFUND_STATUS_FINISH]];

        switch ($level) {
            case 1:
                $where[] = [
                    'exp',
                    ProductCategory::where_in_raw(['pid' => $category_id], 'category_id', 'product_category_id'),
                ];
                break;
            case 2:
                $where['product_category_id'] = $category_id;
                break;
            default:
                break;
        }

        return self::where($where)->count();
    }

    //-------------------------------------------------- 实例方法

    /**
     * 订单取消处理
     * @return boolean
     * @throws PDOException
     * @throws ThinkException
     */
    public function order_cancel()
    {
        if ($this->getAttr('status') !== self::STATUS_WAIT_PAY) {
            return false;
        }

        if (!$this->save(['status' => self::STATUS_INVALID])) {
            return false;
        }

        return true;
    }

    /**
     * 订单退款申请处理
     * @return bool
     * @throws PDOException
     * @throws ThinkException
     */
    public function order_refund_apply()
    {
        if (!$this->save(['refund_status' => self::REFUND_STATUS_APPLY])) {
            return false;
        }

        return true;
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
        $member_id  = $this->getAttr('member_id');
        $money      = $this->getAttr('amount');
        $order_id   = $this->getAttr('order_id');
        $status     = $this->getAttr('status');


        if ($status != self::STATUS_WAIT_PAY) {
            return true;
        }

        $data['payment_time']   = time();
        $data['transaction_sn'] = $transaction_sn;
        $data['payment_id']     = $payment_id;
        $data['status']         = self::STATUS_CHECKING;

        if (!$this->save($data)) {
            return false;
        }

        Message::admin2_order_message($member_id, $money, $order_id);

        return true;
    }




    //-------------------------------------------------- 读取器方法

    /**
     * @param $value
     * @param $data
     * @return string
     * @throws \Exception
     */
    public function getTopUidAttr($value,$data)
    {
        if (!is_null($value)) {
            return $value;
        }
        return  Member::create_invitation($data['top_id']);
    }

    /**
     * @param $value
     * @param $data
     * @return string
     * @throws \Exception
     */
    public function getTopNameAttr($value,$data)
    {
        if (!is_null($value)) {
            return $value;
        }
        return  Member::get($data['top_id'])['member_realname'];
    }

    /**
     * @param $value
     * @param $data
     * @return string
     * @throws \Exception
     */
    public function getBossUidAttr($value,$data)
    {
        if (!is_null($value)) {
            return $value;
        }
        return  Member::create_invitation($data['fboss_id']);
    }

    /**
     * @param $value
     * @param $data
     * @return string
     * @throws \Exception
     */
    public function getBossNameAttr($value,$data)
    {
        if (!is_null($value)) {
            return $value;
        }
        return  Member::get($data['fboss_id'])['member_realname'];
    }


    /**
     * @param $value
     * @param $data
     * @return string
     * @throws \Exception
     */
    public function getCenterUidAttr($value,$data)
    {
        if (!is_null($value)) {
            return $value;
        }
        return  Member::create_invitation($data['center_id']);
    }

    /**
     * @param $value
     * @param $data
     * @return string
     * @throws \Exception
     */
    public function getCenterNameAttr($value,$data)
    {
        if (!is_null($value)) {
            return $value;
        }
        return  Member::get($data['center_id'])['member_realname'];
    }


    //-------------------------------------------------- 追加属性读取器方法

    //-------------------------------------------------- 修改器方法

    /**
     * 订单号 修改器
     * @param $value
     * @return string
     */
    public function setOrderSnAttr($value)
    {
        if (!is_null($value)) {
            return $value;
        }

        $microtime = TimeHelper::microtime();
        return 'LCSD' . substr($microtime, 1, 4) . (date('Y') + 1111) . substr($microtime, 9, 4);
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
     * @param $data
     * @return int
     * @throws DbException
     */
    public function setTimeoutAttr($value, $data)
    {
        if (!is_null($value)) {
            return $value;
        }

        return $data['order_time'] + Configure::getValue('default_order_timeout_time') * 60;
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

    /**
     * 检测报告修改器
     * @param Distribution $model
     * @return Distribution
     */
    public function setReportRelation($model)
    {
        if (is_null($model)) {
            $model = new Report();
        }
        return $model;
    }

    //-------------------------------------------------- 关联加载方法

    /**
     * 关联会员 一对一 属于
     * @return BelongsTo
     */
    public function member()
    {
        $relation = $this->belongsTo(Member::class, 'member_id');
        $relation->field(['member_id', 'member_nickname', 'member_tel', 'member_headpic_id','member_realname']);
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

    /**
     * 关联检测报告 一对一 属于
     * @return BelongsTo
     */
    public function report()
    {
        $relation = $this->belongsTo(Report::class, 'order_id', 'order_id');
        $relation->field(['order_id', 'file_id']);
        return $relation;
    }

    /**
     * 关联订单评价 一对一 属于
     * @return BelongsTo
     */
    public function evaluate()
    {
        $relation = $this->belongsTo(ProductEvaluate::class, 'order_id', 'order_id');
        $relation->field(['order_id', 'content', 'score', 'create_time']);
        return $relation;
    }
}
