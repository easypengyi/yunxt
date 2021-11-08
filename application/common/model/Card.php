<?php

namespace app\common\model;

use app\common\core\BaseModel;
use helper\HttpHelper;
use think\Exception as ThinkException;
use think\exception\DbException;

/**
 * 活动模型
 */
class Card extends BaseModel
{



    protected $type = ['del' => 'boolean','admin_ids' => 'serialize',];


    protected $file = ['image_id' => 'image','detail_image_ids' => ['detail_image', true]];


    //-------------------------------------------------- 静态方法

    /**
     * @param
     * @return array
     * @throws DbException
     */
    public static function card_list($city_name)
    {
        $field = ['id', 'name', 'image_id','city','area'];
        if ($city_name){
            $where['city'] = $city_name;
        }
        $where['del']        = false;
        $where['enable']     = true;
        $where['type']       =  Card::TYPE_card;

        $order = ['sort' => 'asc'];

        $list = self::all_list($field, $where, $order);

        return $list->toArray();
    }

    /**
     * 列表
     * @param string $keyword
     * @param        $sort_type
     * @param int    $category_id
     * @param        $category_level
     * @param int    $member_id
     * @return array
     * @throws DbException
     */
    public static function activity_list($where)
    {
        $field = [
            'id',
            'name',
            'image_id',
            'province',
            'city',
            'area',
            'address',
            'time',
            'start_time'
        ];
        $query = self::field($field);
        $list = self::page_list($where, [], $query);
        if (!$list->isEmpty()) {
        }
        return $list->toArray();
    }


    /**
     * 活动详情
     * @param $id
     * @return Card|null
     * @throws DbException
     */
    public static function detail($id)
    {
        $field = [
            'id',
            'name',
            'image_id',
            'province',
            'city',
            'area',
            'address',
            'time',
            'start_time',
            'end_time',
            'phone',
            'longitude',
            'latitude',
            'detail_image_ids',
            'detail_id',
            'phone',
            'description',
            'admin_ids'
        ];
        $where['id'] = $id;
        $where['del'] = false;
        $where['enable'] = true;
        $query = self::field($field)->where($where);
        $model = self::get($query);

        if (empty($model)) {
            return null;
        }

        $model->append(['detail_url','people_list']);

        return $model;
    }


    /**
     * 销售数量增加
     * @param $product_id
     * @return bool
     * @throws ThinkException
     */
    public static function sold_number_inc($id)
    {
        return self::where(['id' => $id])->setInc('sold_number') != 0;
    }


    /**
     * 下单商品列表
     * @param $product_id
     * @return array
     * @throws DbException
     */
    public static function order_caed_list($id)
    {
        $where = ['id' => ['in', $id], 'enable' => true, 'del' => false];
        $order = ['id' => 'desc'];
        $list = self::all_list([], $where,$order);

        return $list->toArray();
    }


    /**
     * @param $product_id
     * @param $number
     * @return bool
     * @throws \think\Exception
     */
    public static function frozen_stock($product_id, $number)
    {
        $where['id'] = $product_id;
        $where['stock']      = ['>=', $number];

        return self::where($where)->setDec('stock', $number) != 0;
    }
    //-------------------------------------------------- 实例方法

    //-------------------------------------------------- 读取器方法

    /**
     * 商品详情链接 读取器
     * @param $value
     * @param $data
     * @return string
     */
    public function getDetailUrlAttr($value, $data)
    {
        $this->hidden(['detail_id']);

        if (!is_null($value)) {
            return $value;
        }

        return HttpHelper::article_url($data['detail_id']);
    }


    /**
     * 商品详情链接 读取器
     * @param $value
     * @param $data
     * @return string
     */
    public function getPeopleListAttr($value, $data)
    {
        $this->hidden(['detail_id']);

        if (!is_null($value)) {
            return $value;
        }

        $ids = unserialize($data['admin_ids']);
        $where['id'] = ['in',$ids];
        $list = ActivityPeople::where($where)->select();

        return $list;
    }



    //-------------------------------------------------- 追加属性读取器方法


    //-------------------------------------------------- 修改器方法

    //-------------------------------------------------- 关联加载方法

}
