<?php

namespace app\common\model;

use app\common\core\BaseModel;
use think\exception\DbException;
use think\Exception as ThinkException;

/**
 * 通告 模型
 */
class Announcement extends BaseModel
{
    protected $type = ['del' => 'boolean'];

    protected $insert = ['del' => false];


    //-------------------------------------------------- 静态方法

    /**
     * @return array
     * @throws DbException
     */
    public static function data_list()
    {
        $field = ['id', 'name','content'];

        $where['del']        = false;
        $where['enable']     = true;

        $order = ['sort' => 'asc'];

        $list = self::all_list($field, $where, $order);

        return $list->toArray();
    }



    //-------------------------------------------------- 实例方法

    //-------------------------------------------------- 读取器方法

    //-------------------------------------------------- 追加属性读取器方法


    //-------------------------------------------------- 修改器方法

    //-------------------------------------------------- 关联加载方法
}
