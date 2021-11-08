<?php

namespace app\common\model;

use app\common\core\BaseModel;
use think\exception\DbException;
use think\Exception as ThinkException;

/**
 * 广告 模型
 */
class Banner extends BaseModel
{
    // 广告类型
    // 学院广告
    const TYPE_WEB_HOME = 3;
    // 首页广告
    const TYPE_WAP_HOME = 4;

    // 首页广告
    const TYPE_WAP_PRODUCT = 5;

    // 跳转类型
    // 跳转图片
    const SKIP_IMAGE = 1;
    // 跳转链接
    const SKIP_URL = 2;
    // 跳转商品
    const SKIP_PRODUCT = 3;

    protected $type = ['del' => 'boolean'];

    protected $insert = ['del' => false];

    protected $file = ['image_id' => 'image'];

    //-------------------------------------------------- 静态方法

    /**
     * 首页广告
     * @param $banner_type
     * @return array
     * @throws DbException
     */
    public static function banner_list($banner_type)
    {
        $field = ['id', 'name', 'image_id', 'url', 'content', 'skip'];

        $where['del']        = false;
        $where['enable']     = true;
        $where['type']       = $banner_type;
        $where['start_time'] = ['<=', time()];
        $where['end_time']   = ['>=', time()];

        $order = ['sort' => 'asc'];

        $list = self::all_list($field, $where, $order);

        return $list->toArray();
    }

    /**
     * banner点击
     * @param $id
     * @throws ThinkException
     */
    public static function banner_click($id)
    {
        self::where(['id' => $id])->setInc('click_num');
    }

    /**
     * 广告类型组
     * @return array
     */
    public static function banner_type_group()
    {
        return [
            self::TYPE_WEB_HOME,
            self::TYPE_WAP_HOME,
            self::TYPE_WAP_PRODUCT,
        ];
    }

    /**
     * 广告跳转数组
     * @return array
     */
    public static function banner_skip_array()
    {
        return [
            self::SKIP_IMAGE   => '图片展示',
            self::SKIP_URL     => '跳转链接',
            self::SKIP_PRODUCT => '推荐商品',
        ];
    }

    /**
     * 广告类型跳转关系数组
     */
    public static function banner_type_skip_array()
    {
        return [
            self::TYPE_WEB_HOME => [
                self::SKIP_IMAGE,
                self::SKIP_URL,
                self::SKIP_PRODUCT,
            ],
            self::TYPE_WAP_HOME => [
                self::SKIP_IMAGE,
                self::SKIP_URL,
                self::SKIP_PRODUCT,
            ],
            self::TYPE_WAP_PRODUCT => [
                self::SKIP_IMAGE,
                self::SKIP_URL,
                self::SKIP_PRODUCT,
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
            case self::SKIP_PRODUCT:
                $model = Product::get($data['content']);
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
