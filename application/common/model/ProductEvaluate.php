<?php

namespace app\common\model;

use app\common\core\BaseModel;
use think\exception\DbException;
use think\model\relation\BelongsTo;

/**
 * 商品评价 模型
 */
class ProductEvaluate extends BaseModel
{
    protected $type = ['content' => 'base64', 'anonymity' => 'boolean'];

    protected $file = ['image_ids' => ['image', true]];

    protected $autoWriteTimestamp = true;

    //-------------------------------------------------- 静态方法

    /**
     * 会员评价列表
     * @param $member_id
     * @return array
     * @throws DbException
     */
    public static function member_list($member_id)
    {
        $where['member_id'] = $member_id;

        $field = [
            'evaluate_id',
            'member_id',
            'product_id',
            'content',
            'image_ids',
            'create_time',
            'score',
            'anonymity',
        ];
        $order = ['create_time' => 'desc'];

        $query = self::field($field);
        $list  = self::page_list($where, $order, $query);

        if (!$list->isEmpty()) {
            $list->load(['Member', 'Product']);
        }

        return $list->toArray();
    }

    /**
     * 商品评论列表
     * @param      $product_id
     * @return array
     * @throws DbException
     */
    public static function evaluate_list($product_id)
    {
        $where['product_id'] = $product_id;

        $field = ['evaluate_id', 'member_id', 'content', 'image_ids', 'create_time', 'score', 'anonymity'];
        $order = ['create_time' => 'desc'];

        $query = self::field($field);
        $list  = self::page_list($where, $order, $query);

        if (!$list->isEmpty()) {
            $list->load(['Member']);
        }

        return $list->toArray();
    }

    /**
     * 添加评论
     * @param $member_id
     * @param $order_id
     * @param $product_id
     * @param $content
     * @param $image_ids
     * @param $score
     * @param $anonymity
     * @return static
     */
    public static function insert_evaluate($member_id, $order_id, $product_id, $content, $image_ids, $score, $anonymity)
    {
        $data = [
            'member_id'  => $member_id,
            'order_id'   => $order_id,
            'content'    => $content,
            'image_ids'  => $image_ids,
            'product_id' => $product_id,
            'score'      => $score,
            'anonymity'  => $anonymity,
        ];
        return self::create($data);
    }

    //-------------------------------------------------- 实例方法

    //-------------------------------------------------- 读取器方法

    //-------------------------------------------------- 追加属性读取器方法

    //-------------------------------------------------- 修改器方法

    /**
     * 关联会员 修改器
     * @param Member $model
     * @return Member
     */
    public function setMemberRelation($model)
    {
        $data = $this->getData();

        if (is_null($model) || (isset($data['anonymity']) && $data['anonymity'])) {
            $data  = ['member_id' => 0, 'member_nickname' => '匿名', 'member_tel' => '', 'member_headpic_id' => 0];
            $model = new Member($data);
        }

        $this->hidden(['member_id']);
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
        $relation->field(['member_id', 'member_nickname', 'member_tel', 'member_headpic_id']);
        return $relation;
    }

    /**
     * 关联商品 一对一 属于
     * @return BelongsTo
     */
    public function product()
    {
        $relation = $this->belongsTo(Product::class, 'product_id');
        $relation->field(['product_id', 'name', 'image_id']);
        return $relation;
    }
}