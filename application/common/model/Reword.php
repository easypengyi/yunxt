<?php

namespace app\common\model;

use helper\TimeHelper;
use app\common\core\BaseModel;
use think\exception\DbException;
use think\Exception as ThinkException;

/**
 * 奖金池模型
 */
class Reword extends BaseModel
{

    //-------------------------------------------------- 静态方法

    public static function commission_inc($configure_name, $commission)
    {
        if (empty($commission)) {
            return true;
        }
        $where = ['configure_name' => $configure_name];

        return self::where($where)->setInc('configure_value', $commission) != 0;
    }

    public static function commission_dec($configure_name, $commission)
    {
        if (empty($commission)) {
            return true;
        }
        $where = ['configure_name' => $configure_name];

        return self::where($where)->setDec('configure_value', $commission) != 0;
    }


    //-------------------------------------------------- 实例方法

    //-------------------------------------------------- 读取器方法

    //-------------------------------------------------- 追加属性读取器方法

    //-------------------------------------------------- 修改器方法

    //-------------------------------------------------- 关联加载方法
}
