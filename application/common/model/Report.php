<?php

namespace app\common\model;

use stdClass;
use app\common\core\BaseModel;
use think\exception\DbException;
use think\model\relation\BelongsTo;


/**
 * 检测报告 模型
 */
class Report extends BaseModel
{
    protected $file = ['file_id' => 'file'];

    protected $autoWriteTimestamp = true;

    //-------------------------------------------------- 静态方法

    /**
     * 商品报告
     * @param $member_id
     * @param $product_id
     * @return Report|stdClass|null
     * @throws DbException
     */
    public static function product_report($member_id, $product_id)
    {
        $order = OrderShop::order_purchased($member_id, $product_id);
        if (empty($order)) {
            return new stdClass();
        }
        $where['order_id'] = $order->getAttr('order_id');

        $model = self::get($where);
        if (empty($model)) {
            return new stdClass();
        }

        return $model;
    }

    //-------------------------------------------------- 实例方法

    //-------------------------------------------------- 读取器方法

    //-------------------------------------------------- 追加属性读取器方法

    //-------------------------------------------------- 修改器方法

    /**
     * 关联订单 修改器
     * @param  $model
     * @return mixed
     */
    public function setOrderShopRelation($model)
    {
        if (is_null($model)) {
            $data  = ['order_id' => 0, 'member_id' => 0, 'order_sn' => '', 'product_name' => ''];
            $model = new OrderShop($data);
        }
        $model->eagerlyResult($model, ['member']);
        return $model;
    }

    //-------------------------------------------------- 关联加载方法

    /**
     * 关联订单
     * @return BelongsTo
     */
    public function orderShop()
    {
        $relation = $this->belongsTo(OrderShop::class, 'order_id');
        $relation->field(['order_id', 'member_id', 'order_sn', 'product_name']);
        return $relation;
    }
}
