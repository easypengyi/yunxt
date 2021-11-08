<?php

namespace app\common\model;

use app\common\core\BaseModel;
use think\exception\DbException;

/**
 * 按钮 模型
 */
class Button extends BaseModel
{
    // 按钮类型
    // WAP首页
    const TYPE_WAP_HOME = 2;

    // 跳转类型
    // 分类
    const SKIP_CATEGORY_PRODUCT = 1;
    // 观光车
    const SKIP_CAR = 2;
    // 票付通商品
    const SKIP_TICKET_PRODUCT = 3;

    protected $type = ['del' => 'boolean', 'enable' => 'boolean'];

    protected $insert = ['del' => false];

    protected $file = ['image_id' => 'image'];

    protected $autoWriteTimestamp = true;

    //-------------------------------------------------- 静态方法

    /**
     * 按钮列表
     * @param $type
     * @return array
     * @throws DbException
     */
    public static function button_list($type)
    {
        $field = ['button_id', 'name', 'image_id', 'skip', 'content'];

        $where['del']    = false;
        $where['enable'] = true;
        $where['type']   = $type;

        $order = ['sort' => 'asc'];

        $list = self::all_list($field, $where, $order);

        return $list->toArray();
    }

    /**
     * 广告类型组
     * @return array
     */
    public static function type_group()
    {
        return [
            self::TYPE_WAP_HOME,
        ];
    }

    /**
     * 广告类型数组
     * @return array
     */
    public static function type_array()
    {
        return [
            self::TYPE_WAP_HOME => 'H5首页',
        ];
    }

    /**
     * 按钮跳转组
     * @return array
     */
    public static function skip_group()
    {
        return [
            self::SKIP_CATEGORY_PRODUCT,
            self::SKIP_CAR,
            self::SKIP_TICKET_PRODUCT,
        ];
    }

    /**
     * 按钮跳转数组
     * @return array
     */
    public static function skip_array()
    {
        return [
            self::SKIP_CATEGORY_PRODUCT => '分类商品',
            self::SKIP_CAR              => '观光车',
            self::SKIP_TICKET_PRODUCT   => '票付通商品',
        ];
    }

    /**
     * 按钮类型跳转关系数组
     */
    public static function type_skip_array()
    {
        return [
            self::TYPE_WAP_HOME => [
                self::SKIP_CATEGORY_PRODUCT,
                self::SKIP_CAR,
                self::SKIP_TICKET_PRODUCT,
            ],
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
            case self::SKIP_CATEGORY_PRODUCT:
                $model = ProductCategory::get($data['content']);
                $value = empty($model) ? '' : $model->getAttr('name');
                break;
            case self::SKIP_CAR:
            case self::SKIP_TICKET_PRODUCT:
                $value = '';
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
