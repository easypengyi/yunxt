<?php

namespace app\common\model;

use app\common\core\BaseModel;
use helper\HttpHelper;
use think\Exception as ThinkException;
use think\exception\DbException;

/**
 * 活动人员模型
 */
class ActivityPeople extends BaseModel
{


    protected $type = ['del' => 'boolean'];

    protected $file = ['image_id' => 'image'];


    //-------------------------------------------------- 静态方法


    /**
     * @param $where
     * @return array
     * @throws DbException
     */
    public static function activity_list($where)
    {
        $field = [
            'id',
            'name',
            'image_id',
            'title',
        ];
        $query = self::field($field);
        $list = self::page_list($where, [], $query);
        if (!$list->isEmpty()) {
        }
        return $list->toArray();
    }



    //-------------------------------------------------- 实例方法

    //-------------------------------------------------- 读取器方法




    //-------------------------------------------------- 追加属性读取器方法


    //-------------------------------------------------- 修改器方法

    //-------------------------------------------------- 关联加载方法

}
