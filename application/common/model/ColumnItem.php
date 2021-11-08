<?php

namespace app\common\model;

use app\common\core\BaseModel;
use think\exception\DbException;

/**
 * 栏目内容 模型
 */
class ColumnItem extends BaseModel
{
    // 跳转类型
    // 跳转图片
    const SKIP_IMAGE = 1;
    // 跳转链接
    const SKIP_URL = 2;
    // 跳转商品
    const SKIP_PRODUCT = 3;
    // 跳转专题
    const SKIP_SERIES = 4;

    protected $type = ['del' => 'boolean'];

    protected $insert = ['del' => false];

    protected $file = ['image_id' => 'image'];

    //-------------------------------------------------- 静态方法

    /**
     * 栏目内容列表
     * @param $column_id
     * @return array
     * @throws DbException
     */
    public static function item_list($column_id)
    {
        $field = ['item_id', 'name', 'image_id', 'url', 'content', 'skip'];

        $where['del']       = false;
        $where['enable']    = true;
        $where['column_id'] = $column_id;

        $list = self::all_list($field, $where, ['sort' => 'asc']);

        return $list->toArray();
    }

    /**
     * 栏目跳转数组
     * @return array
     */
    public static function skip_array()
    {
        return [
            self::SKIP_IMAGE   => '图片展示',
            self::SKIP_URL     => '跳转链接',
            self::SKIP_PRODUCT => '推荐商品',
            self::SKIP_SERIES  => '跳转专题',
        ];
    }

    //-------------------------------------------------- 实例方法

    //-------------------------------------------------- 读取器方法

    //-------------------------------------------------- 追加属性读取器方法

    /**
     * 内容信息 读取器
     * @param $value
     * @param $data
     * @return string
     * @throws DbException
     */
    public function getContentInfoAttr($value, $data)
    {
        if (!is_null($value)) {
            return $value;
        }

        switch ($data['skip']) {
            case self::SKIP_PRODUCT:
                $model = Product::get($data['content']);
                $value = empty($model) ? '' : $model->getAttr('name');
                break;
            case self::SKIP_SERIES:
                $model = ProductSeries::get($data['content']);
                $value = empty($model) ? '' : $model->getAttr('name');
                break;
            default:
                $value = $data['content'];
                break;
        }
        return $value;
    }

    //-------------------------------------------------- 修改器方法

    //-------------------------------------------------- 关联加载方法
}
