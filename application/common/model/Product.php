<?php

namespace app\common\model;

use helper\HttpHelper;
use app\common\core\BaseModel;
use think\exception\DbException;
use think\exception\PDOException;
use think\model\relation\BelongsTo;
use think\Exception as ThinkException;

/**
 * 商品 模型
 */
class Product extends BaseModel
{
    // 排序
    // 默认
    const SORT_DEFAULT = 1;
    // 销量
    const SORT_SOLD_NUMBER = 2;
    // 价格递增
    const SORT_PRICE_ASC = 3;
    // 价格递减
    const SORT_PRICE_DESC = 4;
    // 已购递增
    const SORT_PURCHASED_ASC = 5;
    // 已购递减
    const SORT_PURCHASED_DESC = 6;

    // NMN特殊商品ID数组
    const PRODUCT_ID = 6;
   //
    const PRODUCT_PRICE = 980;

    protected $type = ['del' => 'boolean', 'enable' => 'boolean'];

    protected $insert = ['del' => false, 'praise_rate' => 5];

    protected $file = ['image_id' => 'image','share_image_id' => 'share_image','detail_image_ids' => ['detail_image', true]];

    protected $autoWriteTimestamp = true;

    //-------------------------------------------------- 静态方法

    /**
     * 商品列表
     * @param string $keyword
     * @param        $sort_type
     * @param int    $category_id
     * @param        $category_level
     * @param int    $member_id
     * @return array
     * @throws DbException
     */
    public static function product_list($keyword, $sort_type, $category_id = 0, $member_id = 0, $category_level = 0)
    {
        $field = [
            'product_id',
            'category_id',
            'name',
            'image_id',
            'share_image_id',
            'description',
            'praise_rate',
            'sold_number',
            'current_price',
            'original_price',

        ];

        $where['enable'] = true;
        $where['del']    = false;

        if (!empty($category_id)) {
            switch ($category_level) {
                case 1:
                    $where['category_id'] = $category_id;
                    break;
                case 2:
                    $where['category_id'] = $category_id;
                    break;
                default:
                    $where['category_id'] = $category_id;
                    break;
            }
        }
        empty($keyword) OR $where['name'] = ['like', '%' . $keyword . '%'];

        $query = self::field($field);

        switch ($sort_type) {
            case self::SORT_PRICE_DESC:
                $order = ['current_price' => 'desc', 'sort' => 'asc'];
                break;
            case self::SORT_PRICE_ASC:
                $order = ['current_price' => 'asc', 'sort' => 'asc'];
                break;
            case self::SORT_SOLD_NUMBER:
                $order = ['sold_number' => 'desc', 'sort' => 'asc'];
                break;
            case self::SORT_PURCHASED_ASC:
                $order = [];
                if (!empty($member_id)) {
                    $query     = self::field($field, false, self::getTable());
                    $sql       = OrderShop::order_purchased_list($member_id) . ' as `order`';
                    $condition = self::table_field('product_id') . '= order.order_product_id';
                    self::table_join($sql, $condition, $query, 'LEFT');
                    $order = ['order_id' => 'asc', 'sort' => 'asc'];
                }
                break;
            case self::SORT_PURCHASED_DESC:
                $order = [];
                if (!empty($member_id)) {
                    $query     = self::field($field, false, self::getTable());
                    $sql       = OrderShop::order_purchased_list($member_id) . ' as `order`';
                    $condition = self::table_field('product_id') . '= order.order_product_id';
                    self::table_join($sql, $condition, $query, 'LEFT');
                    $order = ['order_id' => 'desc', 'sort' => 'asc'];
                }
                break;
            case self::SORT_DEFAULT:
            default:
                $order = ['sort' => 'asc'];
                break;
        }

        $list = self::page_list($where, $order, $query);
        if (!$list->isEmpty()) {
            $list->load(['Category']);
            $list->each(
                function ($item) use ($member_id) {
                    /** @var static $item */
                    $item->setAttr('current_member_id', $member_id);
                    $item->append(['purchased', 'report']);
                }
            );
        }
        return $list->toArray();
    }

    /**
     * 商品详情
     * @param $product_id
     * @param $member_id
     * @return static
     * @throws DbException
     */
    public static function product_detail($product_id, $member_id = 0)
    {
        $field = [
            'product_id',
            'name',
            'description',
            'image_id',
            'share_image_id',
            'detail_image_ids',
            'enable',
            'detail_id',
            'praise_rate',
            'sold_number',
            'current_price',
            'original_price',
            'stock'
        ];

        $query = self::field($field)->where(['product_id' => $product_id]);
        $model = self::get($query);

        if (empty($model)) {
            return null;
        }

        $model->setAttr('current_member_id', $member_id);
        $model->append(['detail_url', 'collection', 'sell', 'share', 'purchased', 'report']);

        return $model;
    }

    /**
     * 商品删除
     * @param $product_id
     * @throws DbException
     * @throws PDOException
     * @throws ThinkException
     */
    public static function product_delete($product_id)
    {
        $model = self::get(['product_id' => $product_id]);
        if (empty($model)) {
            return;
        }

        $model->save(['enable' => false, 'del' => true]);
    }

    /**
     * 销售数量增加
     * @param $product_id
     * @return bool
     * @throws ThinkException
     */
    public static function sold_number_inc($product_id)
    {
        return self::where(['product_id' => $product_id])->setInc('sold_number') != 0;
    }

    /**
     * 下单商品列表
     * @param $product_id
     * @return array
     * @throws DbException
     */
    public static function order_product_list($product_id)
    {
        $where = ['product_id' => ['in', $product_id], 'enable' => true, 'del' => false];

        $list = self::all_list([], $where);

        return $list->toArray();
    }

    /**
     * 商品库存冻结
     * @param $product_id
     * @param $number
     * @return bool
     * @throws ThinkException
     */
    public static function frozen_stock($product_id, $number)
    {
        $where['product_id'] = $product_id;
        $where['stock']      = ['>=', $number];

        return self::where($where)->setDec('stock', $number) != 0;
    }

    /**
     * 商品库存返还
     * @param $product_id
     * @param $ticket_id
     * @param $number
     * @return bool
     * @throws ThinkException
     */
    public static function stock_back($product_id, $ticket_id, $number)
    {
        $where['product_id'] = $product_id;
        $where['ticket_id']  = $ticket_id;

        return self::where($where)->setInc('stock', $number) != 0;
    }

    /**
     * 分类商品数量
     * @param $category_id
     * @param $level
     * @return int|string
     * @throws DbException
     * @throws ThinkException
     */
    public static function category_number($category_id, $level)
    {
        $where['enable'] = true;
        $where['del']    = false;
        switch ($level) {
            case 1:
                $where['category_id'] = $category_id;
                break;
            case 2:
                $where['category_id'] = $category_id;
                break;
            default:
                break;
        }

        return self::where($where)->count();
    }

    //-------------------------------------------------- 实例方法

    //-------------------------------------------------- 读取器方法

    //-------------------------------------------------- 追加属性读取器方法

    /**
     * 商品上下架状态 读取器
     * @param $value
     * @param $data
     * @return bool
     */
    public function getSellAttr($value, $data)
    {
        $this->hidden(['enable']);

        if (!is_null($value)) {
            return $value;
        }

        return $data['enable'];
    }

    /**
     * 商品详情链接 读取器
     * @param $value
     * @param $data
     * @return string
     */
    public function getDetailUrlAttr($value, $data)
    {
        $this->hidden(['detail_id']);

        if (!is_null($value)) {
            return $value;
        }

        return HttpHelper::article_url($data['detail_id']);
    }

    /**
     * 商品是否收藏 读取器
     * @param $value
     * @param $data
     * @return bool
     * @throws ThinkException
     */
    public function getCollectionAttr($value, $data)
    {
        $this->hidden(['current_member_id']);

        if (!is_null($value)) {
            return $value;
        }

        return ProductCollection::check_exists($data['current_member_id'], $data['product_id']);
    }

    /**
     * 分享信息 读取器
     * @param $value
     * @param $data
     * @return array
     * @throws DbException
     * @throws ThinkException
     */
    public function getShareAttr($value, $data)
    {
        if (!is_null($value)) {
            return $value;
        }

        return [
            'image'       => self::file_info($data['image_id'])['full_url'],
            'title'       => $data['name'],
            'description' => empty($data['description']) ? $data['name'] : $data['description'],
            'url'         => url('mobile/Product/detail', ['product_id' => $data['product_id']], true, true),
        ];
    }

    /**
     * 商品是否已购 读取器
     * @param $value
     * @param $data
     * @return bool
     * @throws ThinkException
     */
    public function getPurchasedAttr($value, $data)
    {
        $this->hidden(['current_member_id']);

        if (!is_null($value)) {
            return $value;
        }

        return OrderShop::check_purchased($data['current_member_id'], $data['product_id']);
    }

    /**
     * 商品报告信息 读取器
     * @param $value
     * @param $data
     * @return Report|\stdClass|null
     * @throws DbException
     */
    public function getReportAttr($value, $data)
    {
        if (!is_null($value)) {
            return $value;
        }

        return Report::product_report($data['current_member_id'], $data['product_id']);
    }

    //-------------------------------------------------- 修改器方法

    //-------------------------------------------------- 关联加载方法


}
