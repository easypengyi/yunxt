<?php

namespace app\common\model;

use app\common\core\BaseModel;
use think\exception\DbException;
use think\Exception as ThinkException;

/**
 * 视频 模型
 */
class Video extends BaseModel
{



    protected $type = ['del' => 'boolean'];

    protected $insert = ['del' => false];

    protected $file = ['image_id' => 'image'];

    //-------------------------------------------------- 静态方法

    /**
     * 视频列表
     * @param $banner_type
     * @return array
     * @throws DbException
     */
    public static function video_list($type)
    {
        $field = ['id', 'name', 'image_id', 'url','type'];

        $where['del']        = false;
        $where['enable']     = true;
        $where['type']      = $type;

        $order = ['sort' => 'asc'];
        $list = self::all_list($field, $where, $order);

        return $list->toArray();
    }

    /**
     * 点击
     * @param $id
     * @throws ThinkException
     */
    public static function banner_click($id)
    {
        self::where(['id' => $id])->setInc('click_num');
    }

    //-------------------------------------------------- 实例方法

    //-------------------------------------------------- 读取器方法

    //-------------------------------------------------- 追加属性读取器方法


    //-------------------------------------------------- 修改器方法

    //-------------------------------------------------- 关联加载方法
}
