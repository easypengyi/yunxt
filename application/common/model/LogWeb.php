<?php

namespace app\common\model;

use app\common\core\BaseModel;
use think\model\relation\MorphTo;

/**
 * 访问日志 模型
 */
class LogWeb extends BaseModel
{
    // 类型-无
    const TYPE_NO = 0;
    // 类型-管理员
    const TYPE_ADMIN = 1;
    // 类型-会员
    const TYPE_MEMBER = 2;

    //-------------------------------------------------- 静态方法

    /**
     * 类型数组
     * @return array
     */
    public static function type_array()
    {
        return [
            self::TYPE_NO     => '无',
            self::TYPE_ADMIN  => '管理员',
            self::TYPE_MEMBER => '会员',
        ];
    }

    //-------------------------------------------------- 实例方法

    //-------------------------------------------------- 读取器方法

    //-------------------------------------------------- 追加属性读取器方法

    /**
     * 操作者名称
     * @param $value
     * @param $data
     * @return string
     */
    public function getOperatorNameAttr($value, $data)
    {
        if (!is_null($value)) {
            return $value;
        }

        switch ($data['type']) {
            case self::TYPE_NO:
                $value = '';
                break;
            case self::TYPE_ADMIN:
                $operator = $this->getRelation('operator');
                $value    = $operator['admin_username'];
                break;
            case self::TYPE_MEMBER:
                $operator = $this->getRelation('operator');
                $value    = $operator['member_nickname'];
                break;
            default:
                $value = '';
                break;
        }

        return $value;
    }

    //-------------------------------------------------- 修改器方法

    //-------------------------------------------------- 关联加载方法

    /**
     * 关联操作者 属于
     * @return MorphTo
     */
    public function operator()
    {
        return $this->morphTo(
            ['type', 'operator_id'],
            [
                self::TYPE_ADMIN  => Admin::class,
                self::TYPE_MEMBER => Member::class,
            ]
        );
    }
}
