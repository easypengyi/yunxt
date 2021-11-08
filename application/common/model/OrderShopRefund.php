<?php

namespace app\common\model;

use app\common\core\BaseModel;

/**
 * 订单退款申请信息 模型
 */
class OrderShopRefund extends BaseModel
{
    protected $file = ['image_ids' => ['image', true]];

    protected $autoWriteTimestamp = true;

    //-------------------------------------------------- 静态方法

    /**
     * 添加会员反馈
     * @param $order_id
     * @param $content
     * @param $image_id
     * @return int
     */
    public static function insert_refund($order_id, $content, $image_id)
    {
        $data['order_id']  = $order_id;
        $data['content']   = $content;
        $data['image_ids'] = $image_id;

        $model = self::create($data);

        if (empty($model)) {
            return 0;
        }

        return $model->getAttr('refund_id');
    }

    //-------------------------------------------------- 实例方法

    //-------------------------------------------------- 读取器方法

    //-------------------------------------------------- 追加属性读取器方法

    //-------------------------------------------------- 修改器方法

    //-------------------------------------------------- 关联加载方法
}
