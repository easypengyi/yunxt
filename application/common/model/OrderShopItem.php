<?php

namespace app\common\model;

use app\common\core\BaseModel;

/**
 * 商城订单内容 模型
 */
class OrderShopItem extends BaseModel
{
    protected $type = ['sold' => 'boolean'];

    protected $insert = ['sold' => false];

    protected $file = ['product_image_id' => 'product_image'];

    protected $autoWriteTimestamp = true;

    //-------------------------------------------------- 静态方法

    /**
     * 商品销售数量
     * @param      $product_id
     * @return int
     */
    public static function product_sold_number($product_id)
    {
        $where['product_id'] = $product_id;
        $where['sold']       = true;

        return intval(self::where($where)->cache(true, 120)->sum('number'));
    }

    /**
     * 规格销售数量
     * @param $standard_id
     * @return int
     */
    public static function standard_sold_number($standard_id)
    {
        $where['standard_id'] = $standard_id;
        $where['sold']        = true;

        return intval(self::where($where)->cache(true, 120)->sum('number'));
    }

    //-------------------------------------------------- 实例方法

    //-------------------------------------------------- 读取器方法

    //-------------------------------------------------- 追加属性读取器方法

    //-------------------------------------------------- 修改器方法

    //-------------------------------------------------- 关联加载方法
}
