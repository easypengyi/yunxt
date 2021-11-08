<?php

namespace app\common\model;

use app\common\core\BaseModel;
use think\exception\DbException;
use think\Exception as ThinkException;

/**
 * 商品分类 模型
 */
class ProductCategory extends BaseModel
{
    protected $type = ['enable' => 'boolean'];

    protected $file = ['image_id' => 'image'];

    protected $autoWriteTimestamp = true;

    //-------------------------------------------------- 静态方法

    /**
     * 商品分类列表
     * @param int $pid
     * @param int $member_id
     * @return array
     * @throws DbException
     */
    public static function category_list($pid = 0, $member_id = 0)
    {
        $field = ['category_id', 'name', 'image_id', 'level', 'pid'];

        $where = ['enable' => true, 'pid' => $pid];

        $order = ['sort' => 'asc'];

        $list = self::all_list($field, $where, $order);
        if (!$list->isEmpty()) {
            $list->each(
                function ($item) use ($member_id) {
                    /** @var static $item */
                    $item->setAttr('current_member_id', $member_id);
                    $item->append(['product_number', 'order_number']);
                }
            );
        }
        return $list->toArray();
    }

    /**
     * 所有直接商品分类列表
     * @return array
     * @throws DbException
     */
    public static function all_category_list()
    {
        $field = ['category_id', 'name', 'image_id'];

        $where = ['enable' => true, 'level' => 2];

        $order = ['sort' => 'asc'];

        $list = self::all_list($field, $where, $order);

        return $list->toArray();
    }

    /**
     * 所有的商品分类
     * @return array
     * @throws DbException
     */
    public static function all_product_category()
    {
        $list = self::all_list([], [], ['sort' => 'asc']);

        return $list->toArray();
    }

    /**
     * 商品分类数组
     * @param      $level
     * @param bool $enable
     * @return array
     */
    public static function category_array($level, $enable = true)
    {
        $where = ['level' => $level];
        $enable AND $where['enable'] = true;

        return self::where($where)->column('name', 'category_id');
    }

    /**
     * 验证分类
     * @param $category_id
     * @param $level
     * @return bool
     * @throws ThinkException
     */
    public static function check_category($category_id, $level)
    {
        $where = ['category_id' => $category_id, 'level' => $level];
        return self::check($where);
    }

    /**
     * 过滤分类
     * @param      $level
     * @param bool $enable
     * @return array
     * @throws DbException
     */
    public static function category_filter($level, $enable = true)
    {
        $where['level'] = $level;
        $enable AND $where['enable'] = true;

        return self::where($where)->column('name', 'category_id');
    }

    //-------------------------------------------------- 实例方法

    //-------------------------------------------------- 读取器方法

    //-------------------------------------------------- 追加属性读取器方法

    /**
     * 分类商品数量 读取器
     * @param $value
     * @param $data
     * @return int|string
     * @throws DbException
     * @throws ThinkException
     */
    public function getProductNumberAttr($value, $data)
    {
        if (!is_null($value)) {
            return $value;
        }

        return Product::category_number($data['category_id'], $data['level']);
    }

    /**
     * 分类订单数量 读取器
     * @param $value
     * @param $data
     * @return int|string
     * @throws DbException
     * @throws ThinkException
     */
    public function getOrderNumberAttr($value, $data)
    {
        if (!is_null($value)) {
            return $value;
        }

        return OrderShop::category_number($data['category_id'], $data['level'], $data['current_member_id']);
    }

    //-------------------------------------------------- 修改器方法

    //-------------------------------------------------- 关联加载方法
}
