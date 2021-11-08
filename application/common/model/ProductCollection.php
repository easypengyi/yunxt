<?php

namespace app\common\model;

use app\common\core\BaseModel;
use think\exception\DbException;
use think\model\relation\BelongsTo;
use think\Exception as ThinkException;

/**
 * 商品收藏 模型
 */
class ProductCollection extends BaseModel
{
    protected $autoWriteTimestamp = true;

    //-------------------------------------------------- 静态方法

    /**
     * 商品收藏列表
     * @param        $member_id
     * @param string $keyword
     * @return array
     * @throws DbException
     */
    public static function collection_list($member_id, $keyword = '')
    {
        $where = ['member_id' => $member_id];

        if (!empty($keyword)) {
            $where[] = ['exp', Product::where_in_raw(['name' => ['like', '%' . $keyword . '%']], 'product_id')];
        }

        $order = ['create_time' => 'desc'];

        $query = self::field(['product_id']);
        $list  = self::page_list($where, $order, $query);

        if (!$list->isEmpty()) {
            $list->load(['Product']);
            $list->each(
                function ($item) {
                    /** @var static $item */
                    $item->data($item->getRelation('product')->toArray(), true);
                    $item->hidden(['product'], true);
                }
            );
        }

        return $list->toArray();
    }

    /**
     * 商品收藏数量
     * @param $member_id
     * @return int
     * @throws ThinkException
     */
    public static function collection_count($member_id)
    {
        $where['member_id'] = $member_id;
        return self::where($where)->count();
    }

    /**
     * 添加商品收藏
     * @param $member_id
     * @param $product_id
     * @return bool
     */
    public static function collection_insert($member_id, $product_id)
    {
        $data['member_id']  = $member_id;
        $data['product_id'] = $product_id;

        $model = self::create($data);

        return !empty($model);
    }

    /**
     * 商品收藏取消
     * @param $member_id
     * @param $product_id
     * @return bool
     * @throws ThinkException
     */
    public static function collection_delete($member_id, $product_id)
    {
        $where['member_id']  = $member_id;
        $where['product_id'] = ['in', $product_id];

        return self::where($where)->delete() != 0;
    }

    /**
     * 判断是否收藏商品
     * @param $member_id
     * @param $product_id
     * @return bool
     * @throws ThinkException
     */
    public static function check_exists($member_id, $product_id)
    {
        $where['member_id']  = $member_id;
        $where['product_id'] = ['in', $product_id];

        return self::check($where);
    }

    //-------------------------------------------------- 实例方法

    //-------------------------------------------------- 读取器方法

    //-------------------------------------------------- 追加属性读取器方法

    //-------------------------------------------------- 修改器方法

    /**
     * 商品 修改器
     * @param Product $model
     * @return Product
     */
    public function setProductRelation($model)
    {
        $this->hidden(['product_id']);
        return $model;
    }

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
            'description',
            'image_id',
            'sold_number',
            'current_price',
            'original_price',
        ];

        $relation = $this->belongsTo(Product::class, 'product_id');
        $relation->field($field);
        return $relation;
    }
}
