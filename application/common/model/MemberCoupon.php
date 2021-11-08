<?php

namespace app\common\model;

use app\common\model\CouponTemplate as CouponTemplateModel;
use think\db\Query;
use app\common\core\BaseModel;
use think\exception\DbException;
use think\exception\PDOException;
use think\Exception as ThinkException;

/**
 * 会员优惠券 模型
 */
class MemberCoupon extends BaseModel
{
    // 状态
    // 状态-未激活
    const STATUS_NO_ACTIVATION = 0;
    // 状态-可使用
    const STATUS_AVAILABLE = 1;
    // 状态-已使用
    const STATUS_ALREADY_USED = 2;

    // 类型
    // 类型-通用
    const TYPE_NO_LIMIT = 1;
    // 类型-专用
    const TYPE_LIMIT = 2;

    protected $type = [
        'time_limit'    => 'boolean',
        'product_limit' => 'boolean',
        'admin_send'    => 'boolean',
        'product_ids'   => 'plode',
    ];

    protected $insert = ['use_time' => 0, 'status' => self::STATUS_AVAILABLE];

    protected $autoWriteTimestamp = true;

    //-------------------------------------------------- 静态方法

    /**
     * 会员优惠券列表
     * @param $member_id
     * @param $type
     * @return array
     * @throws DbException
     */
    public static function member_coupon_list($member_id, $type)
    {
        $field = [
            'coupon_id',
            'time_limit',
            'start_time',
            'end_time',
            'coupon_name',
            'coupon_desc',
            'fill',
            'value',
            'product_limit',
            'product_ids',
        ];

        $where['member_id'] = $member_id;
        $where['status']    = self::STATUS_AVAILABLE;

        $where[] = function (Query $query) {
            $query->where(['end_time' => ['>', time()], 'time_limit' => true]);
            $query->whereOr('time_limit', false);
        };

        switch ($type) {
            case self::TYPE_NO_LIMIT:
                // 通用
                $where['product_limit'] = false;
                break;
            case self::TYPE_LIMIT:
                // 专用
                $where['product_limit'] = true;
                break;
            default:
                return [];
                break;
        }

        $order = ['create_time' => 'DESC'];

        $query = self::field($field);
        $list  = self::page_list($where, $order, $query);
        if (!$list->isEmpty()) {
            $list->append(['invalid', 'product_name']);
        }

        return $list->toArray();
    }

    /**
     * 会员可用优惠券列表
     * @param $member_id
     * @param $money
     * @param $product_id
     * @param $coupon_id
     * @return array
     * @throws DbException
     */
    public static function coupon_use_list($member_id, $money, $product_id, $coupon_id = '')
    {
        $field = [
            'coupon_id',
            'time_limit',
            'start_time',
            'end_time',
            'coupon_name',
            'coupon_desc',
            'fill',
            'value',
        ];

        $where['member_id'] = $member_id;
        $where['status']    = self::STATUS_AVAILABLE;
        $where['fill']      = ['<=', $money];
        empty($coupon_id) OR $where['coupon_id'] = ['not in', $coupon_id];

        $where[] = function (Query $query) {
            $query->where(['start_time' => ['<=', time()], 'end_time' => ['>', time()], 'time_limit' => true]);
            $query->whereOr('time_limit', false);
        };

        $where[] = function (Query $query) use ($product_id) {
            $query->where(['product_limit' => true, ['exp', self::find_in_set('product_ids', $product_id, 'OR')]]);
            $query->whereOr('product_limit', false);
        };

        $order = ['create_time' => 'DESC'];

        $query = self::field($field);
        $list  = self::page_list($where, $order, $query);

        return $list->toArray();
    }

    /**
     * 优惠券是否可用，并获取信息
     * @param       $coupon_id
     * @param       $member_id
     * @param       $money
     * @param array $product_id
     * @return static
     * @throws DbException
     */
    public static function coupon_use_info($coupon_id, $member_id, $money, $product_id)
    {
        $field = ['coupon_id', 'product_limit', 'product_ids', 'value'];

        $where['coupon_id'] = $coupon_id;
        $where['member_id'] = $member_id;
        $where['status']    = self::STATUS_AVAILABLE;
        $where['fill']      = ['<=', $money];

        $where[] = function (Query $query) {
            $query->where(['start_time' => ['<=', time()], 'end_time' => ['>', time()], 'time_limit' => true]);
            $query->whereOr('time_limit', false);
        };

        $query = self::field($field)->where($where);
        $model = self::get($query);

        if (empty($model)) {
            return null;
        }

        if ($model->getAttr('product_limit')) {
            $product = in_array($product_id, $model->getAttr('product_ids'));
            if (empty($product)) {
                return null;
            }
        }

        return $model;
    }

    /**
     * 优惠券添加
     * @param      $template_data
     * @param      $member_id
     * @param bool $admin_send
     * @return MemberCoupon
     * @throws DbException
     */
    public static function coupon_insert($template_data, $member_id, $admin_send = false)
    {
        $data = $template_data;

        $data['member_id']  = $member_id;
        $data['admin_send'] = $admin_send;
        Message::home_coupon($member_id);
        return self::create($data);
    }

    /**
     * 优惠券使用
     * @param $coupon_id
     * @param $member_id
     * @return bool
     * @throws PDOException
     * @throws ThinkException
     */
    public static function coupon_use($coupon_id, $member_id)
    {
        $where['coupon_id'] = $coupon_id;
        $where['member_id'] = $member_id;
        $where['status']    = self::STATUS_AVAILABLE;

        return self::where($where)->update(['status' => self::STATUS_ALREADY_USED, 'use_time' => time()]) != 0;
    }

    /**
     * 优惠券退还
     * @param $coupon_id
     * @return bool
     * @throws PDOException
     * @throws ThinkException
     */
    public static function coupon_back($coupon_id)
    {
        $where['coupon_id'] = $coupon_id;
        $where['status']    = self::STATUS_ALREADY_USED;

        return self::where($where)->update(['status' => self::STATUS_AVAILABLE, 'use_time' => 0]) != 0;
    }

    /**
     * 优惠券数量
     * @param      $member_id
     * @param int  $template_id
     * @return int|string
     * @throws ThinkException
     */
    public static function coupon_number($member_id, $template_id = 0)
    {
        empty($template_id) OR $where['template_id'] = $template_id;

        $where['status'] = ['not in', [self::STATUS_ALREADY_USED]];

        $where[] = function (Query $query) {
            $query->where(['start_time' => ['<=', time()], 'end_time' => ['>', time()], 'time_limit' => true]);
            $query->whereOr('time_limit', false);
        };

        $where['member_id'] = $member_id;
        return self::where($where)->count();
    }

    /**
     * 领取的优惠券数量
     * @param      $member_id
     * @param      $template_id
     * @param bool $fetchSql
     * @return int|string
     * @throws ThinkException
     */
    public static function receive_number($member_id, $template_id, $fetchSql = false)
    {
        $where = ['member_id' => $member_id, 'template_id' => $template_id, 'admin_send' => false];
        return MemberCoupon::fetchSql($fetchSql)->where($where)->count();
    }

    //-------------------------------------------------- 实例方法

    //-------------------------------------------------- 读取器方法

    //-------------------------------------------------- 追加属性读取器方法

    /**
     * 是否失效 读取器
     * @param $value
     * @param $data
     * @return bool
     */
    public function getInvalidAttr($value, $data)
    {
        if (!is_null($value)) {
            return $value;
        }

        if ($data['time_limit']) {
            $value = $data['end_time'] <= time();
        } else {
            $value = false;
        }
        return $value;
    }

    /**
     * 商品名称 读取器
     * @param $value
     * @param $data
     * @return string
     */
    public function getProductNameAttr($value, $data)
    {
        if (!is_null($value)) {
            return $value;
        }

        $product = Product::where(['product_id' => ['in', $data['product_ids']]])->column('name');
        return implode('、', $product);
    }

    //-------------------------------------------------- 修改器方法

    //-------------------------------------------------- 关联加载方法
}
