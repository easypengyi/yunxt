<?php

namespace app\common\model;

use think\db\Query;
use app\common\core\BaseModel;
use think\exception\DbException;
use think\Exception as ThinkException;

/**
 * 优惠券模板 模型
 */
class CouponTemplate extends BaseModel
{
    protected $type = [
        'enable'        => 'boolean',
        'del'           => 'boolean',
        'time_limit'    => 'boolean',
        'product_limit' => 'boolean',
        'receive_limit' => 'boolean',
        'product_ids'   => 'plode',
        'fill'          => 'float',
        'value'         => 'float',
        'activity_send' => 'boolean',
    ];

    protected $insert = ['del' => false];

    protected $autoWriteTimestamp = true;

    //-------------------------------------------------- 静态方法

    /**
     * 可领取优惠券列表接口
     * @param $member_id
     * @return array
     * @throws DbException
     */
    public static function receive_coupon_list($member_id)
    {
        $field = [
            'template_id',
            'coupon_name',
            'coupon_desc',
            'fill',
            'value',
            'time_limit',
            'start_time',
            'end_time',
            'receive_limit',
            'number_limit',
            'start_receive_time',
        ];

        $where = [
            'del'    => false,
            'enable' => true,
            ['exp', self::raw('`receive_number` > `already_receive_number`')],
        ];

        $where[] = function (Query $query) {
            $query->where(['start_time' => ['<=', time()], 'end_time' => ['>', time()], 'time_limit' => true]);
            $query->whereOr('time_limit', false);
        };

        $order = ['start_receive_time' => 'asc', 'create_time' => 'desc'];

        $query = self::field($field);
        $list  = self::page_list($where, $order, $query);
        if (!$list->isEmpty()) {
            $list->each(
                function ($item) use ($member_id) {
                    /** @var static $item */
                    $item->setAttr('current_member_id', $member_id);
                    $item->append(['receive']);
                }
            );
        }
        return $list->toArray();
    }

    /**
     * 商铺优惠券列表
     * @param $money
     * @param $product_id
     * @param $member_id
     * @return array
     * @throws DbException
     */
    public static function store_coupon_list($money, $product_id, $member_id)
    {
        $field = [
            'template_id',
            'coupon_name',
            'coupon_desc',
            'fill',
            'value',
            'time_limit',
            'start_time',
            'end_time',
            'receive_limit',
            'number_limit',
            'start_receive_time',
        ];

        $where = [
            'del'    => false,
            'enable' => true,
            ['exp', self::raw('`receive_number` > `already_receive_number`')],
        ];

        empty($money) OR $where['fill'] = ['<=', $money];

        $where[] = function (Query $query) {
            $query->where(['start_time' => ['<=', time()], 'end_time' => ['>', time()], 'time_limit' => true]);
            $query->whereOr('time_limit', false);
        };

        $where[] = function (Query $query) {
            $query->where(['start_receive_time' => ['<=', time()], 'receive_limit' => true]);
            $query->whereOr('receive_limit', false);
        };

        if (!empty($product_id)) {
            $where[] = function (Query $query) use ($product_id) {
                $query->where(['product_limit' => true, ['exp', self::find_in_set('product_ids', $product_id, 'OR')]]);
                $query->whereOr('product_limit', false);
            };
        }

        $order = ['value' => 'desc', 'create_time' => 'desc'];

        $query = self::field($field);
        $list  = self::page_list($where, $order, $query);
        if (!$list->isEmpty()) {
            $list->each(
                function ($item) use ($member_id) {
                    /** @var static $item */
                    $item->setAttr('current_member_id', $member_id);
                    $item->append(['receive']);
                }
            );
        }
        return $list->toArray();
    }

    /**
     * 全部商品优惠券
     * @param      $product_id
     * @param null $limit
     * @return array
     * @throws DbException
     */
    public static function all_store_coupon($product_id, $limit = null)
    {
        $field = [
            'template_id',
            'coupon_name',
            'coupon_desc',
            'fill',
            'value',
            'time_limit',
            'start_time',
            'end_time',
            'receive_limit',
            'start_receive_time',
        ];

        $where = [
            'del'    => false,
            'enable' => true,
            ['exp', self::raw('`receive_number` > `already_receive_number`')],
        ];

        $where[] = function (Query $query) {
            $query->where(['start_time' => ['<=', time()], 'end_time' => ['>', time()], 'time_limit' => true]);
            $query->whereOr('time_limit', false);
        };

        $where[] = function (Query $query) {
            $query->where(['start_receive_time' => ['<=', time()], 'receive_limit' => true]);
            $query->whereOr('receive_limit', false);
        };

        if (!empty($product_id)) {
            $where[] = function (Query $query) use ($product_id) {
                $query->where(['product_limit' => true, ['exp', self::find_in_set('product_ids', $product_id, 'OR')]]);
                $query->whereOr('product_limit', false);
            };
        }

        $order = ['value' => 'desc', 'create_time' => 'desc'];

        $query = self::field($field);
        is_null($limit) OR $query->limit($limit);
        $list = self::all_list($query, $where, $order);
        return $list->toArray();
    }

    /**
     * 领取优惠券
     * @param $template_id
     * @param $member_id
     * @return bool
     * @throws ThinkException
     */
    public static function receive_coupon($template_id, $member_id)
    {
        $where = [
            'template_id'        => $template_id,
            'del'                => false,
            'enable'             => true,
            'start_receive_time' => ['<=', time()],
            ['exp', self::raw('`receive_number` > `already_receive_number`')],
            [
                'exp',
                self::raw('`number_limit` > (' . MemberCoupon::receive_number($member_id, $template_id, true) . ')'),
            ],
        ];

        return self::where($where)->setInc('already_receive_number') != 0;
    }

    /**
     * 优惠券提醒信息
     * @param $template_id
     * @return CouponTemplate|null
     * @throws DbException
     */
    public static function coupon_remind_info($template_id)
    {
        $where['template_id']        = $template_id;
        $where['receive_limit']      = true;
        $where['start_receive_time'] = ['>=', time()];
        return self::get($where);
    }

    /**
     * 开通推荐活动赠送
     * @param $member_id
     * @return bool
     * @throws DbException
     * @throws ThinkException
     */
    public static function activity_coupon_send($member_id)
    {
        $where = [
            'del'           => false,
            'enable'        => true,
            'activity_send' => true,
            ['exp', self::raw('`receive_number` > `already_receive_number`')],
        ];

        $where[] = function (Query $query) {
            $query->where(['start_time' => ['<=', time()], 'end_time' => ['>', time()], 'time_limit' => true]);
            $query->whereOr('time_limit', false);
        };

        $list = self::all_list([], $where);
        if (empty($list)) {
            return false;
        }

        foreach ($list as $v) {
            $template_id = $v->getAttr('template_id');
            self::receive_coupon($template_id, $member_id);
        }

        return true;
    }

    //-------------------------------------------------- 实例方法

    /**
     * 生成优惠券数据
     */
    public function create_coupon_data()
    {
        return [
            'template_id'   => $this->getAttr('template_id'),
            'coupon_name'   => $this->getAttr('coupon_name'),
            'coupon_desc'   => $this->getAttr('coupon_desc'),
            'fill'          => $this->getAttr('fill'),
            'value'         => $this->getAttr('value'),
            'time_limit'    => $this->getAttr('time_limit'),
            'start_time'    => $this->getAttr('start_time'),
            'end_time'      => $this->getAttr('end_time'),
            'product_limit' => $this->getAttr('product_limit'),
            'product_ids'   => $this->getAttr('product_ids'),
        ];
    }

    //-------------------------------------------------- 读取器方法

    //-------------------------------------------------- 追加属性读取器方法

    /**
     * 赠送优惠券数量 读取器
     * @param $value
     * @param $data
     * @return int
     */
    public function getSendNumberAttr($value, $data)
    {
        if (!is_null($value)) {
            return $value;
        }

        return CouponGive::total_send_number($data['template_id']);
    }

    /**
     * 是否可领取 读取器
     * @param $value
     * @param $data
     * @return bool
     * @throws ThinkException
     */
    public function getReceiveAttr($value, $data)
    {
        $this->hidden(['current_member_id']);

        if (!is_null($value)) {
            return $value;
        }
        if (!empty($data['current_member_id'])) {
            $receive_number = MemberCoupon::receive_number($data['current_member_id'], $data['template_id']);
            return $receive_number < $data['number_limit'];
        }
        return true;
    }

    //-------------------------------------------------- 修改器方法

    //-------------------------------------------------- 关联加载方法
}
