<?php

namespace app\common\model;

use helper\HttpHelper;
use app\common\core\BaseModel;

/**
 * 路由 模型
 */
class Route extends BaseModel
{
    //-------------------------------------------------- 静态方法

    /**
     * 路由数组
     * @return array
     */
    public static function route_array()
    {
        return self::where(['enable' => true])->order(['sort' => 'asc'])->column('full_url', 'url');
    }

    //-------------------------------------------------- 实例方法

    //-------------------------------------------------- 读取器方法

    //-------------------------------------------------- 追加属性读取器方法

    //-------------------------------------------------- 修改器方法

    /**
     * 原始地址 修改器
     * @param string $value
     * @return string
     */
    public function setFullUrlAttr($value)
    {
        $info = parse_url(strval($value));
        if (isset($info['query'])) {
            parse_str($info['query'], $parameter);
            ksort($parameter);
        } else {
            $parameter = [];
        }

        return HttpHelper::get_url_query($info['path'], $parameter);
    }

    //-------------------------------------------------- 关联加载方法
}
