<?php

namespace app\common\model;

use think\db\Query;
use app\common\core\BaseModel;
use think\exception\DbException;
use think\model\relation\HasMany;
use think\model\Collection as ModelCollection;

/**
 * 栏目 模型
 */
class Column extends BaseModel
{
    // 位置
    // 位置-全部
    const POSITION_ALL = 0;
    // 位置-首页
    const POSITION_HOME = 1;
    // 位置-热门
    const POSITION_HOT = 2;

    // 类型
    // 类型-单张(32:9)
    const TYPE_ONE = 1;
    // 类型-两张(1:1)
    const TYPE_TWO = 2;
    // 类型-四张(16:9)
    const TYPE_THREE = 3;
    // 类型-四张(1:2)
    const TYPE_FOUR = 4;

    protected $type = ['del' => 'boolean'];

    protected $insert = ['del' => false];

    protected $file = ['image_id' => 'image'];

    //-------------------------------------------------- 静态方法

    /**
     * 栏目列表
     * @param $position
     * @return array
     * @throws DbException
     */
    public static function column_list($position)
    {
        $field = ['column_id', 'type', 'position'];

        $where['del']    = false;
        $where['enable'] = true;

        $where[] = function (Query $query) use ($position) {
            $query->where(['position' => $position]);
            $query->whereOr('position', self::POSITION_ALL);
        };

        $list = self::all_list($field, $where, ['sort' => 'asc']);
        if (!$list->isEmpty()) {
            $list->load(['Item']);
        }

        return $list->toArray();
    }

    /**
     * 栏目位置组
     * @return array
     */
    public static function position_group()
    {
        return [
            self::POSITION_ALL  => '全部',
            self::POSITION_HOME => '首页',
            self::POSITION_HOT  => '热门',
        ];
    }

    /**
     * 栏目类型组
     * @return array
     */
    public static function type_group()
    {
        return [
            self::TYPE_ONE   => '单张(32:9)',
            self::TYPE_TWO   => '两张(1:1)',
            self::TYPE_THREE => '四张(16:9)',
            self::TYPE_FOUR  => '四张(1:2)',
        ];
    }

    /**
     * 栏目图片尺寸组
     * @return array
     */
    public static function image_size_group()
    {
        return [
            self::TYPE_ONE   => '750*211',
            self::TYPE_TWO   => '375*375',
            self::TYPE_THREE => '375*210',
            self::TYPE_FOUR  => '375*187',
        ];
    }

    //-------------------------------------------------- 实例方法

    //-------------------------------------------------- 读取器方法

    //-------------------------------------------------- 追加属性读取器方法

    //-------------------------------------------------- 修改器方法

    /**
     * 栏目内容 修改器
     * @param ModelCollection $value
     * @return ModelCollection
     */
    public function setItemRelation($value)
    {
        $value->hidden(['column_id']);
        return $value;
    }

    //-------------------------------------------------- 关联加载方法

    /**
     * 关联栏目内容
     * @return HasMany
     */
    public function item()
    {
        $field = [
            'column_id',
            'name',
            'skip',
            'image_id',
            'url',
            'content',
        ];

        $relation = $this->hasMany(ColumnItem::class, 'column_id');
        $relation->field($field);
        $relation->where(['enable' => true, 'del' => false]);
        $relation->order('sort', 'asc');
        return $relation;
    }
}
