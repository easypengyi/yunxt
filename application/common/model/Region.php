<?php

namespace app\common\model;

use Think\Cache;
use app\common\core\BaseModel;
use think\exception\DbException;
use think\Exception as ThinkException;

/**
 * 区域信息 模型
 */
class Region extends BaseModel
{
    // 省
    const PROVINCE = 1;
    // 市
    const CITY = 2;
    // 区
    const DISTRICT = 3;

    //-------------------------------------------------- 静态方法

    /**
     * 缓存全部省市区数据 以省市区三级格式
     * @param int  $level
     * @param bool $update 更新
     * @return array
     * @throws DbException
     * @throws ThinkException
     */
    public static function area_cache($level = self::DISTRICT, $update = false)
    {
        if ($level > self::DISTRICT) {
            $level = self::DISTRICT;
        } else {
            if ($level < self::PROVINCE) {
                $level = self::PROVINCE;
            }
        }
        $data = $update ? false : Cache::get(__CLASS__ . __FUNCTION__ . $level);

        if (empty($data)) {
            $data = self::area($level);
            Cache::tag(self::getCacheTag())->set(__CLASS__ . __FUNCTION__ . $level, $data);
        }

        return $data;
    }

    /**
     * 获取全部数据 以省市区三级格式输出
     * @param $level
     * @return array
     * @throws DbException
     * @throws ThinkException
     */
    public static function area($level)
    {
        $field = ['id', 'type', 'name', 'pid'];

        $where = ['type' => ['in', range(self::PROVINCE, $level)]];

        $order = ['type' => 'asc', 'pid' => 'desc'];

        $list = self::all_list($field, $where, $order);

        $province = [];
        $city     = [];
        $district = [];

        foreach ($list as $k => $v) {
            $parent_id = $v->getAttr('pid');
            $type      = $v->getAttr('type');
            $v->hidden(['pid', 'type']);
            if ($type == self::PROVINCE) {
                $province[] = $v->toArray();
            } else {
                if ($type == self::CITY) {
                    $city[$parent_id][] = $v->toArray();
                } else {
                    $district[$parent_id][] = $v->toArray();
                }
            }
        }

        foreach ($province as $key => $val) {
            $val['list'] = [];
            if (isset($city[$val['id']]) && $level >= self::CITY) {
                $val['list'] = $city[$val['id']];

                foreach ($val['list'] as $key_1 => $val_1) {
                    $val_1['list'] = [];

                    if (isset($district[$val_1['id']]) && $level >= self::DISTRICT) {
                        $val_1['list'] = $district[$val_1['id']];
                    } elseif ($level >= self::DISTRICT) {
                        unset($val['list'][$key_1]);
                        continue;
                    }

                    $val['list'][$key_1] = $val_1;
                }

                $val['list'] = array_values($val['list']);
            } elseif ($level >= self::CITY) {
                unset($province[$key]);
                continue;
            }

            $province[$key] = $val;
        }

        return array_values($province);
    }

    /**
     * 补全地址信息
     * @param      $id
     * @param null $type
     * @return array
     * @throws DbException
     */
    public static function fill_region($id, $type = null)
    {
        $region = self::get_region($id, $type);
        if (empty($region)) {
            return [];
        }
        $type = $region->getAttr('type');

        $result = [];
        switch ($type) {
            case self::PROVINCE:
                $result = [
                    'province_id'   => $region->getAttr('id'),
                    'province_name' => $region->getAttr('name'),
                ];
                break;
            case self::CITY:
                $result = [
                    'city_id'   => $region->getAttr('id'),
                    'city_name' => $region->getAttr('name'),
                ];
                break;
            case self::DISTRICT:
                $result = [
                    'district_id'   => $region->getAttr('id'),
                    'district_name' => $region->getAttr('name'),
                ];
                break;
        }
        if ($type > self::PROVINCE) {
            $parent_result = self::fill_region($region->getAttr('pid'), $type - 1);
            if (empty($parent_result)) {
                return [];
            }
            $result = array_merge($parent_result, $result);
        }
        return $result;
    }

    /**
     * 区域信息关系验证
     * @param int $province_id
     * @param int $city_id
     * @param int $district_id
     * @return bool
     * @throws DbException
     */
    public static function region_relation_check($province_id = 0, $city_id = 0, $district_id = 0)
    {
        if (empty($province_id)) {
            return true;
        }

        $region = self::get_region($province_id, self::PROVINCE);
        if (empty($region)) {
            return false;
        }

        if (empty($city_id)) {
            return true;
        }

        $region = self::get_region($city_id, self::CITY, $province_id);
        if (empty($region)) {
            return false;
        }

        if (empty($district_id)) {
            return true;
        }

        $region = self::get_region($district_id, self::DISTRICT, $city_id);
        if (empty($region)) {
            return false;
        }

        return true;
    }

    /**
     * 区域是否存在
     * @param      $id
     * @param null $level
     * @return bool
     * @throws ThinkException
     */
    public static function region_exists($id, $level = null)
    {
        if (empty($id)) {
            return false;
        }

        $where['id'] = $id;
        is_null($level) OR $where['type'] = $level;

        return self::check($where);
    }

    /**
     * 获取区域信息对象
     * @param      $id
     * @param null $type
     * @param null $pid
     * @return static
     * @throws DbException
     */
    private static function get_region($id, $type = null, $pid = null)
    {
        $where['id'] = $id;
        is_null($type) OR $where['type'] = $type;
        is_null($pid) OR $where['pid'] = $pid;

        return self::get($where, [], [true, null, self::getCacheTag()]);
    }

    /**
     * 获取子列表数组
     * @param $type
     * @param $pid
     * @return array
     */
    public static function children_array($type, $pid)
    {
        if (empty($pid)) {
            return [];
        }

        $where['pid'] = $pid;
        empty($type) OR $where['type'] = $type;

        return self::where($where)->column('name', 'id');
    }

    //-------------------------------------------------- 实例方法

    //-------------------------------------------------- 读取器方法


    //-------------------------------------------------- 追加属性读取器方法

    //-------------------------------------------------- 修改器方法

    //-------------------------------------------------- 关联加载方法
}
