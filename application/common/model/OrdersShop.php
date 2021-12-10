<?php

namespace app\common\model;

use app\common\model\Member as MemberModel;
use app\common\model\MemberGroup as MemberGroupModel;
use app\common\model\MemberGroupRelation as MemberGroupRelationModel;
use app\common\model\OrdersShop as OrdersShopModel;
use helper\StrHelper;
use think\Log;
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
use tool\PaymentTool;
/**
 * 商城订单 模型
 */
class OrdersShop extends BaseModel
{
    // 状态
    // 状态-无
    const STATUS_NO = 0;
    // 状态-已失效
    const STATUS_INVALID = 1;
    // 状态-待支付
    const STATUS_WAIT_PAY = 2;
    // 状态-待配送 已付款
    const STATUS_ALREADY_PAY = 3;
    // 状态-配送中
    const STATUS_ALREADY_DISTRIBUTION = 4;
    // 状态-检测中
    const STATUS_CHECKING = 5;
    // 状态-已完成
    const STATUS_FINISH = 6;
    // 状态-已评价
    const STATUS_EVALUATE = 7;

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

    // 分类
    // 分类-全部
    const CATEGORY_ALL = 0;
    // 分类-待付款
    const CATEGORY_WAIT_PAY = 1;
    // 分类-待配送 已付款
    const CATEGORY_ALREADY_PAY = 2;
    // 分类-配送中
    const CATEGORY_ALREADY_DISTRIBUTION = 3;
    // 分类-检测中
    const CATEGORY_CHECKING = 4;
    // 分类-待评价
    const CATEGORY_WIAT_EVALUATE = 5;

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
        'refund_status' => self::REFUND_STATUS_NO,
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
            case self::STATUS_ALREADY_DISTRIBUTION:
                $where['status'] = self::STATUS_ALREADY_DISTRIBUTION;
                break;
            case self::STATUS_ALREADY_PAY:
                $where['status']        = self::STATUS_ALREADY_PAY;
                $where['refund_status'] = ['in', [self::REFUND_STATUS_NO, self::REFUND_STATUS_FAIL]];
                break;
            case self::STATUS_FINISH:
                $where['status']        = self::STATUS_FINISH;
                $where['refund_status'] = ['in', [self::REFUND_STATUS_NO, self::REFUND_STATUS_FAIL]];
                break;
            default:
                $where['status'] = self::STATUS_NO;
                break;
        }

        $field = [
            'order_id',
            'order_sn',
            'courier_sn',
            'amount',
            'product_id',
            'product_name',
            'product_num',
            'product_image_id',
            'unit_price',
            'original_unit_price',
            'status',
            'refund_status',
            'order_time',
            'distribution_id',
            'address'

        ];
        $order = ['order_time' => 'desc'];

        $query = self::field($field);
        $list  = self::page_list($where, $order, $query);
        if (!$list->isEmpty()) {
            $list->load(['Distribution']);
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
     * 可退款订单信息
     * @param $order_id
     * @param $member_id
     * @return static
     * @throws DbException
     */
    public static function refund_order_info($order_id, $member_id)
    {
        $field = ['order_id', 'refund_status', 'package'];

        $where = [
            'order_id'      => $order_id,
            'member_id'     => $member_id,
            'status'        => ['in', [self::STATUS_ALREADY_PAY]],
            'refund_status' => ['in', [self::REFUND_STATUS_NO, self::REFUND_STATUS_FAIL]],
        ];

        $query = self::field($field)->where($where);
        $model = self::get($query);
        if (empty($model)) {
            return null;
        }
        return $model;
    }

    /**
     * 可评价订单信息
     * @param $order_id
     * @param $member_id
     * @return static
     * @throws DbException
     */
    public static function evaluate_order_info($order_id, $member_id)
    {
        $field = ['order_id', 'refund_status'];

        $where = [
            'order_id'      => $order_id,
            'member_id'     => $member_id,
            'status'        => self::STATUS_FINISH,
            'refund_status' => ['in', [self::REFUND_STATUS_NO, self::REFUND_STATUS_FAIL]],
        ];

        $query = self::field($field)->where($where);
        $model = self::get($query);
        if (empty($model)) {
            return null;
        }
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
     * 已完成订单数量
     * @param $member_id
     * @return int
     * @throws ThinkException
     */
    public static function public1($member_id)
    {
        if (empty($member_id)) {
            return 0;
        }

        $where['member_id']     = $member_id;
        $where['del']           = false;
        $where['hide']          = false;
        $where['status']        = self::STATUS_FINISH;
        $where['refund_status'] = ['in', self::REFUND_STATUS_NO, self::REFUND_STATUS_FAIL];
        return self::where($where)->sum('product_num');
    }

    /**
     * 昨日已完成订单数量
     * @param $member_id
     * @return int
     * @throws ThinkException
     */
    public static function public2($member_id)
    {
        if (empty($member_id)) {
            return 0;
        }

        $where['member_id']     = $member_id;
        $where['del']           = false;
        $where['hide']          = false;
        $where['status']        = self::STATUS_FINISH;
        $where['refund_status'] = ['in', self::REFUND_STATUS_NO, self::REFUND_STATUS_FAIL];
        return self::where($where)->whereTime('finish_time','yesterday')->sum('product_num');
    }



    /**
     * 所有已完成订单数量
     * @param $member_id
     * @return int
     * @throws ThinkException
     */
    public static function boss1()
    {
        $where['del']           = false;
        $where['hide']          = false;
        $where['status']        = self::STATUS_FINISH;
        $where['refund_status'] = ['in', self::REFUND_STATUS_NO, self::REFUND_STATUS_FAIL];
        return self::where($where)->sum('product_num');
    }
    /**
     * 订单隐藏
     * @param $order_id
     * @param $member_id
     * @return bool
     * @throws PDOException
     * @throws ThinkException
     */
    public static function order_hide($order_id, $member_id)
    {
        $where['hide']      = false;
        $where['order_id']  = ['in', $order_id];
        $where['member_id'] = $member_id;
        $where[]            = function (Query $query) {
            $query->where(['status' => ['in', [self::STATUS_INVALID, self::STATUS_FINISH, self::STATUS_EVALUATE]]])
                ->whereOr(['refund_status' => self::REFUND_STATUS_FINISH]);
        };

        return self::where($where)->update(['hide' => true]) != 0;
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
     * 订单状态修改完成
     * @param $status
     * @return bool
     * @throws PDOException
     * @throws ThinkException
     */
    public static function order_status($order_id,$data)
    {
        if (!$data){
            return false;
        }
        return self::where(['order_id'=>$order_id])->update($data);
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
            self::STATUS_INVALID             => '已取消',
            self::STATUS_WAIT_PAY             => '待支付',
            self::STATUS_ALREADY_PAY          => '待发货',
            self::STATUS_ALREADY_DISTRIBUTION => '已发货',
            self::STATUS_FINISH               => '已完成',
        ];
    }

    /**
     * 订单退款状态数组
     */
    public static function order_refund_status_array()
    {
        return [
            self::REFUND_STATUS_NO      => '无退款',
            self::REFUND_STATUS_APPLY   => '退款申请中',
            self::REFUND_STATUS_SUCCESS => '退款申请成功',
            self::REFUND_STATUS_FAIL    => '退款申请失败',
            self::REFUND_STATUS_FINISH  => '退款到账成功',
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
//        if ($this->getAttr('status') !== self::STATUS_WAIT_PAY) {
//            return false;
//        }

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
        $order_id   = $this->getAttr('order_id');
        $status     = $this->getAttr('status');
        $product_id = $this->getAttr('product_id');
        if ($status != self::STATUS_WAIT_PAY) {
            return true;
        }

        $data['payment_time']   = time();
        $data['transaction_sn'] = $transaction_sn;
        $data['payment_id']     = $payment_id;
        $data['status']         = self::STATUS_ALREADY_PAY;

        if (!$this->save($data)) {
            return false;
        }

        Product::sold_number_inc($product_id);

        $order =  OrdersShopModel::get($order_id);


        if ($product_id == Product::PRODUCT_ID){
            $group  = MemberGroupRelation::get_top($member_id);
            if ($group){
                    $member_name = MemberModel::user_info($member_id)['member_realname'];
                    $top = MemberGroupRelation::get_top($group['top_id']);
                    if ($top){
//                        if ($top['group_id'] != MemberGroupRelationModel::seven){
//                            $this->newReward($group['top_id'],$order['unit_price'],$order['product_num'],$member_name,$group['group_id']);
                            $this->newReward($group, $top, $order['unit_price'], $order['product_num'],$member_name, $order_id);
//                        }
                    }
            }
        }
        return true;

    }

    /**
     * 3.0逻辑
     *
     * @param $member_group
     * @param $member_top
     * @param $money
     * @param $num
     * @param $member_name
     * @param $order_id
     * @throws DbException
     * @throws ThinkException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public static function  newReward($member_group, $member_top, $money, $num, $member_name, $order_id){

        $reward = $num * Product::PRODUCT_PRICE;
        self::commonReward($reward, $member_name, $order_id);

        //游客购买
        $other_commission = 0;
        if($member_group['group_id'] == MemberGroupRelationModel::seven){
            //游客直推
            if($member_top['group_id'] == MemberGroupRelationModel::seven){
                $other_commission = $money * $num * 0.05;//游客可得到零售价5%=119.4元
            }else if($member_top['group_id'] == MemberGroupRelationModel::five){
                $other_commission = $money * $num * 0.08;//体验官可得到零售价8%=191元
            }
            if($other_commission > 0){
                $other_commission = StrHelper::ceil_decimal($other_commission, 2);
                Member::commission_inc($member_top['member_id'], $other_commission);
                MemberCommission::insert_log($member_top['member_id'], MemberCommission::maker5, $other_commission, $other_commission, '来自'.$member_name.'的奖金'.$other_commission, $order_id);
            }
        }else{ //游客以上购买
            $amount = $num * $money;
            if($member_group['group_id'] >= $member_top['group_id']){
                switch ($member_top['group_id']){
                    case MemberGroupRelation::first:
                        $member_rate = MemberModel::FIRST_RATE;
                        break;
                    case MemberGroupRelation::second:
                        $member_rate = MemberModel::SECOND_RATE;
                        break;
                    case MemberGroupRelation::three:
                        $member_rate = MemberModel::THREE_RATE;
                        break;
                    case MemberGroupRelation::four:
                        $member_rate = MemberModel::FOUR_RATE;
                        break;
                    default:
                        $member_rate = MemberModel::FOUR_RATE;
                        break;
                }
                //推荐奖励
                $commission =  StrHelper::ceil_decimal($amount * $member_rate / 100, 2);
                Member::commission_inc($member_group['top_id'], $commission);
                MemberCommission::insert_log($member_group['top_id'], MemberCommission::recommend, $commission, $commission, '来自'.$member_name.'的推荐奖￥'.$commission, $order_id);
            }

            //育成奖
            $parent_info = MemberGroupRelationModel::get_top($member_group['top_id']);
            if(!is_null($parent_info)){
                //代理人以上，并且被推入与推荐人的上级是同级或以上才有奖励哦
                $parent_group_id = MemberGroupRelationModel::get_group_id($parent_info['top_id']);
                if(($parent_group_id == MemberGroupRelationModel::seven || $member_group['group_id'] >= $parent_group_id) && $parent_info['top_id']>0){
                    $commission =  StrHelper::ceil_decimal($amount * MemberModel::LEVEL_RATE / 100, 2);
                    Member::commission_inc($parent_info['top_id'], $commission);
                    MemberCommission::insert_log($parent_info['top_id'], MemberCommission::level, $commission, $commission, '来自'.$member_name.'的育成奖￥'.$commission, $order_id);
                }
            }
        }

        //极差奖励
        if(!empty($member_group['path'])){
            $path = explode(',', $member_group['path']);
            $group = explode(',', $member_group['path_group']);
            $unit_price = $money;
            //有代理哦
            if($path[3] > 0 && $group[3] == MemberGroupRelation::four){
                $commission =  ($unit_price - MemberModel::FOUR_PRICE) * $num;
                $commission = $commission - $other_commission;//需要扣除游客或者体验官的奖励
                $commission =  StrHelper::ceil_decimal($commission, 2);
                Member::commission_inc($path[3], $commission);
                MemberCommission::insert_log($path[3], MemberCommission::maker5, $commission, $commission, '来自'.$member_name.'的奖金￥'.$commission, $order_id);
                $unit_price = MemberModel::FOUR_PRICE;
            }
            //有董事哦
            if($path[2] > 0 && $group[2] == MemberGroupRelation::three){
                $commission =  ($unit_price - MemberModel::THREE_PRICE) * $num;
                Member::commission_inc($path[2], $commission);
                MemberCommission::insert_log($path[2], MemberCommission::maker5, $commission, $commission, '来自'.$member_name.'的奖金￥'.$commission, $order_id);
                $unit_price = MemberModel::THREE_PRICE;
            }
            //有合伙人哦
            if($path[1] > 0 && $group[1] == MemberGroupRelation::second){
                $commission =  ($unit_price - MemberModel::SECOND_PRICE) * $num;
                Member::commission_inc($path[1], $commission);
                MemberCommission::insert_log($path[1], MemberCommission::maker5, $commission, $commission, '来自'.$member_name.'的奖金￥'.$commission, $order_id);
                $unit_price = MemberModel::SECOND_PRICE;
            }
            //有创始人哦
            if($path[0] > 0 && $group[0] == MemberGroupRelation::first){
                $commission =  ($unit_price - MemberModel::FIRST_PRICE) * $num;
                Member::commission_inc($path[0], $commission);
                MemberCommission::insert_log($path[0], MemberCommission::maker5, $commission, $commission, '来自'.$member_name.'的奖金￥'.$commission, $order_id);
                //$unit_price = MemberModel::SECOND_PRICE;
            }
        }
    }

    /**
     * 全国加权分红
     *
     * @param $reward
     * @param $member_name
     * @param $order_id
     */
    public static function  commonReward($reward, $member_name, $order_id){
        $reward1 = StrHelper::ceil_decimal($reward, 2)*0.03;//联合创始人奖金池
        $reward2 = StrHelper::ceil_decimal($reward, 2)*0.02;//全球合伙人奖金池
        $reward3 = StrHelper::ceil_decimal($reward, 2)*0.01;//执行董事奖金池
        $data_log = [];

        Reword::commission_inc('first_commission', $reward1);
        $data_log[1]['member_id']   = 0;
        $data_log[1]['type']        = MemberCommission::first;
        $data_log[1]['amount']      = $reward1;
        $data_log[1]['value']       = $reward1;
        $data_log[1]['description'] = '来自'.$member_name.'的业绩分红';
        $data_log[1]['relation_id'] = $order_id;
        $data_log[1]['create_time'] = time();

        Reword::commission_inc('second_commission', $reward2);
        $data_log[2]['member_id']   = 0;
        $data_log[2]['type']        = MemberCommission::second;
        $data_log[2]['amount']      = $reward2;
        $data_log[2]['value']       = $reward2;
        $data_log[2]['description'] = '来自'.$member_name.'的业绩分红';
        $data_log[2]['relation_id'] = $order_id;
        $data_log[2]['create_time'] = time();

        Reword::commission_inc('three_commission', $reward3);
        $data_log[3]['member_id']   = 0;
        $data_log[3]['type']        = MemberCommission::three;
        $data_log[3]['amount']      =$reward3;
        $data_log[3]['value']       = $reward3;
        $data_log[3]['description'] = '来自'.$member_name.'的业绩分红';
        $data_log[3]['relation_id'] = $order_id;
        $data_log[3]['create_time'] = time();

        MemberCommission::insert_log_all($data_log);
    }


    public static function  reward($top_id,$money,$num,$member_name,$member_group_id){


        $reward = $num*Product::PRODUCT_PRICE;

        $reward1 = StrHelper::ceil_decimal($reward, 2)*0.03;//联合创始人奖金池

        $reward2 = StrHelper::ceil_decimal($reward, 2)*0.02;//全球合伙人奖金池

        $reward3 = StrHelper::ceil_decimal($reward, 2)*0.01;//执行董事奖金池

        $data_log = [];

        Reword::commission_inc('first_commission', $reward1);
        $data_log[1]['member_id']   = 0;
        $data_log[1]['type']        = MemberCommission::first;
        $data_log[1]['amount']      = $reward1;
        $data_log[1]['value']       = $reward1;
        $data_log[1]['description'] = '来自'.$member_name.'的业绩分红';
        $data_log[1]['relation_id'] = 0;
        $data_log[1]['create_time'] = time();

        Reword::commission_inc('second_commission', $reward2);
        $data_log[2]['member_id']   = 0;
        $data_log[2]['type']        = MemberCommission::second;
        $data_log[2]['amount']      = $reward2;
        $data_log[2]['value']       = $reward2;
        $data_log[2]['description'] = '来自'.$member_name.'的业绩分红';
        $data_log[2]['relation_id'] = 0;
        $data_log[2]['create_time'] = time();

        Reword::commission_inc('three_commission', $reward3);
        $data_log[3]['member_id']   = 0;
        $data_log[3]['type']        = MemberCommission::three;
        $data_log[3]['amount']      =$reward3;
        $data_log[3]['value']       = $reward3;
        $data_log[3]['description'] = '来自'.$member_name.'的业绩分红';
        $data_log[3]['relation_id'] = 0;
        $data_log[3]['create_time'] = time();

        MemberCommission::insert_log_all($data_log);

        $array = self::sort($top_id);
        if ($array){
            foreach ($array as $key => $row) {
                $edition[$key] = $key;
            }
            array_multisort($edition, SORT_DESC,$array);//从大到小排序

            $group_id = 0;
            $new_array = [];
            foreach ($array as $k => &$val){
                if ($val['group_id'] > $group_id){
                    $val['price'] = MemberGroupModel::where(['group_id'=>$val['group_id']])->find()['group_price'];
                    array_push($new_array,$val);
                    $group_id = $val['group_id'];
                }
            }
            $bottom_id = 0;
            foreach ($new_array as $k=>&$val){
                $val['bottom_id'] = $bottom_id;
                $bottom_id =  $val['member_id'];
            }

            $new_array = array_column($new_array,null,'member_id');
            foreach ($new_array as $k=>$val){
                $cj = $val['price']*$num;
                if ($val['bottom_id'] == 0){

                    $commission = $money*$num -  $cj ; //活动推荐奖
                    if ( $commission > 0){
                        Member::commission_inc($val['member_id'], $commission);
                        MemberCommission::insert_log($val['member_id'], MemberCommission::recommend, $commission, $commission, '来自'.$member_name.'的奖金', '');
                    }
                    if ($member_group_id < $val['group_id']){
                        $commission = Product::PRODUCT_PRICE*$num*0.01;//百分之一管理奖
                        Member::commission_inc($val['member_id'], $commission);
                        MemberCommission::insert_log($val['member_id'], MemberCommission::maker5, $commission, $commission, '来自'.$member_name.'的奖金', '');
                    }
                }else{
                    $commission =   ($new_array[$val['bottom_id']]['price']*$num - $cj);
                    Member::commission_inc($val['member_id'], $commission);
                    MemberCommission::insert_log($val['member_id'], MemberCommission::recommend, $commission, $commission, '来自'.$member_name.'的奖金', '');

                    if ($member_group_id < $val['group_id']){
                        $commission = Product::PRODUCT_PRICE*$num*0.01;//百分之一管理奖
                        Member::commission_inc($val['member_id'], $commission);
                        MemberCommission::insert_log($val['member_id'], MemberCommission::maker5, $commission, $commission, '来自'.$member_name.'的奖金', '');
                    }
                }

            }
        }

    }


    public static function sort1($id,$data)
    {
        $arr = [];
        foreach($data as $k => $v){
            //从小到大 排列
            if($v['member_id'] == $id){
                $arr[$k]['member_id'] = $v['member_id'];
                $arr[$k]['group_id'] =  $v['group_id'];
                if($v['top_id'] > 0){
                    $arr = array_merge(self::sort1($v['top_id'],$data), $arr);
                }
            }
        }
        return $arr;
    }
    public static function sort($top_id)
    {
        $where['group_id'] = ['neq',MemberGroupRelationModel::seven];
        $data = $a = MemberGroupRelationModel::all_list([],$where);
        $arr = self::sort1($top_id,$data);
        return $arr;
    }


    /**
     * @param $payment_id
     * @param $transaction_sn
     * @return bool
     * @throws PDOException
     * @throws ThinkException
     */
    public function order1_pay_finish($payment_id, $transaction_sn)
    {

        $status     = $this->getAttr('status');

        if ($status != self::STATUS_WAIT_PAY) {
            return true;
        }

        $data['payment_time']   = time();
        $data['transaction_sn'] = $transaction_sn;
        $data['payment_id']     = $payment_id;
        $data['status']         = self::STATUS_ALREADY_PAY;

        if (!$this->save($data)) {
            return false;
        }
        return true;
    }

    /**
     * 订单退款处理
     * @param $status
     * @return bool
     * @throws PDOException
     * @throws ThinkException
     */
    public function order_refund($status)
    {
        $data['refund_status'] = $status ? self::REFUND_STATUS_SUCCESS : self::REFUND_STATUS_FAIL;
        if ($status) {
            $this->getAttr('status') == self::STATUS_ALREADY_PAY AND $data['status'] = self::STATUS_FINISH;
            // $param = ['type' => PaymentOrder::TYPE_SHOP, 'order_id' => $this->getAttr('order_id')];
            // Queue::push(RefundCallback::class, $param);
            PaymentTool::instance([])->refund(PaymentOrder::TYPE_PUBLIC, $this->getAttr('order_id'));//by shiqiren
        }

        return $this->save($data) !== false;
    }

    /**
     * 订单退款彻底完成
     * @return bool
     * @throws PDOException
     * @throws ThinkException
     */
    public function order_refund_finish()
    {
        if ($this->getAttr('refund_status') !== self::REFUND_STATUS_SUCCESS) {
            return true;
        }

        $data = ['refund_finish_time' => time(), 'refund_status' => self::REFUND_STATUS_FINISH];
        $this->save($data);
        return true;
    }

    //-------------------------------------------------- 读取器方法

    public function getProvinceNameAttr($value,$data)
    {
        if (!is_null($value)) {
            return $value;
        }
        return Region::where(['id'=>$data['province']])->find()['name'];
    }
    public function getCityNameAttr($value,$data)
    {
        if (!is_null($value)) {
            return $value;
        }
        return Region::where(['id'=>$data['city']])->find()['name'];
    }
    public function getDistrictNameAttr($value,$data)
    {
        if (!is_null($value)) {
            return $value;
        }
        return Region::where(['id'=>$data['district']])->find()['name'];
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
        return 'YDN' . substr($microtime, 1, 4) . (date('Y') + 1111) . substr($microtime, 9, 4);
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
