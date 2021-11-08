<?php

namespace app\common\model;

use app\common\core\BaseModel;

/**
 * 文件删除 模型
 */
class DeleteFile extends BaseModel
{
    protected $autoWriteTimestamp = true;

    //-------------------------------------------------- 静态方法

    /**
     * 添加删除文件
     * @param $path
     * @return static
     */
    public static function insert_file($path)
    {
        $data = ['path' => $path];
        return self::create($data);
    }

    //-------------------------------------------------- 实例方法

    //-------------------------------------------------- 读取器方法

    //-------------------------------------------------- 追加属性读取器方法

    //-------------------------------------------------- 修改器方法

    //-------------------------------------------------- 关联加载方法
}
