<?php

namespace app\common\model;

use app\common\core\BaseModel;
use think\exception\DbException;
use think\exception\PDOException;
use think\model\relation\BelongsTo;
use think\Exception as ThinkException;

/**
 * 商城购物车 模型
 */
class CartShop extends BaseModel
{
    //-------------------------------------------------- 静态方法

    /**
     * 单商城会员购物车商品列表
     * @param $member_id
     * @return array
     * @throws DbException
     * @throws ThinkException
     */
    public static function single_product_list($member_id)
    {
        $list = self::all_list(['number', 'product_id'], ['member_id' => $member_id], ['time' => 'desc']);
        if ($list->isEmpty()) {
            return [];
        }

        $product_list = [];
        $result       = [];
        foreach ($list as $k => $v) {
            /** @var Product $product */
            $product = $v->getRelation('product');

            $product_id = $product->getAttr('product_id');

            if (empty($product_id)) {
                $v->delete();
                continue;
            }

            /** @var Product $product */
            if (isset($product_list[$product_id])) {
                $product = $product_list[$product_id];
            } else {
                $product                   = Product::get($product_id);
                $product_list[$product_id] = $product;
            }

            $val['number']         = $v->getAttr('number');
            $val['product_id']     = $product_id;
            $val['product_name']   = $product->getAttr('name');
            $val['image']          = $product->getAttr('image');
            $val['stock']          = $product->getAttr('stock');
            $val['current_price']  = $product->getAttr('current_price');
            $val['original_price'] = $product->getAttr('original_price');
            $val['sell']           = $product->getAttr('sell');
            $val['enable']         = $product->getAttr('enable');

            $result[] = $val;
        }

        return $result;
    }

    /**
     * 购物车商品数量
     * @param $member_id
     * @return int
     */
    public static function product_number($member_id)
    {
        return intval(self::where(['member_id' => $member_id])->sum('number'));
    }

    /**
     * 购物车商品类型数量
     * @param $member_id
     * @return int|string
     * @throws ThinkException
     */
    public static function product_count($member_id)
    {
        return self::where(['member_id' => $member_id])->count();
    }

    /**
     * 判断购物车是否存在商品
     * @param $member_id
     * @param $product_id
     * @return bool
     * @throws ThinkException
     */
    public static function check_product_contain($member_id, $product_id)
    {
        return self::check(['member_id' => $member_id, 'product_id' => $product_id]);
    }

    /**
     * 购物车商品删除
     * @param $member_id
     * @param $product_id
     * @return bool
     * @throws PDOException
     * @throws ThinkException
     */
    public static function product_delete($member_id, $product_id)
    {
        $where['member_id']  = $member_id;
        $where['product_id'] = ['in', $product_id];
        return self::where($where)->delete() != 0;
    }

    //-------------------------------------------------- 实例方法

    //-------------------------------------------------- 读取器方法

    //-------------------------------------------------- 追加属性读取器方法

    //-------------------------------------------------- 修改器方法

    //-------------------------------------------------- 关联加载方法

    /**
     * 关联加载商品 一对一 属于
     * @return BelongsTo
     */
    public function product()
    {
        $field = [
            'product_id',
            'name',
            'original_price',
            'current_price',
            'stock',
            'enable',
        ];

        $relation = $this->belongsTo(Product::class, 'product_id');
        $relation->field($field);
        return $relation;
    }
}
