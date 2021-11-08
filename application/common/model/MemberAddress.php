<?php

namespace app\common\model;

use stdClass;
use app\common\core\BaseModel;
use think\exception\DbException;
use think\exception\PDOException;
use think\model\relation\BelongsTo;
use think\Exception as ThinkException;

/**
 * 会员地址 模型
 */
class MemberAddress extends BaseModel
{
    protected $type = ['tolerant' => 'boolean'];

    protected $relation_alias = [
        'province_region' => 'province',
        'city_region'     => 'city',
        'district_region' => 'district',
    ];

    protected $autoWriteTimestamp = true;

    //-------------------------------------------------- 静态方法

    /**
     * 获取用户地址列表
     * 分页
     * @param $member_id
     * @return array
     * @throws DbException
     */
    public static function address_list($member_id)
    {
        $field = [
            'address_id',
            'consignee',
            'mobile',
            'address',
            'province_id',
            'city_id',
            'district_id',
            'tolerant',
        ];

        $where = ['member_id' => $member_id];

        $order = ['tolerant' => 'desc', 'create_time' => 'desc', 'update_time' => 'desc'];

        $query = self::field($field);
        $list  = self::page_list($where, $order, $query);

        if (!$list->isEmpty()) {
            $list->load(['provinceRegion', 'cityRegion', 'districtRegion']);
        }

        return $list->toArray();
    }

    /**
     * 地址详情
     * @param $member_id
     * @param $address_id
     * @return static
     * @throws DbException
     */
    public static function address_detail($member_id, $address_id)
    {
        $field = [
            'address_id',
            'consignee',
            'mobile',
            'address',
            'province_id',
            'city_id',
            'district_id',
            'tolerant',
        ];

        $where = ['member_id' => $member_id, 'address_id' => $address_id];

        $query = self::field($field)->where($where);
        $model = self::get($query);

        if (empty($model)) {
            return null;
        }

        $model->eagerlyResult($model, ['provinceRegion', 'cityRegion', 'districtRegion']);

        return $model;
    }

    /**
     * 获取默认地址
     * @param $member_id
     * @return object|static
     * @throws DbException
     */
    public static function default_address($member_id)
    {
        $field = ['address_id', 'consignee', 'mobile', 'address', 'province_id', 'city_id', 'district_id'];

        $where = ['member_id' => $member_id];

        $order = ['tolerant' => 'desc', 'create_time' => 'desc', 'update_time' => 'desc'];

        $query = self::field($field)->where($where)->order($order);
        $model = self::get($query);

        if (empty($model)) {
            $model = new stdClass();
        } else {
            $model->eagerlyResult($model, ['provinceRegion', 'cityRegion', 'districtRegion']);
        }

        return $model;
    }

    /**
     * 地址数量
     * @param $member_id
     * @return int
     * @throws ThinkException
     */
    public static function address_count($member_id)
    {
        return self::where(['member_id' => $member_id])->count();
    }

    /**
     * 添加地址
     * @param $member_id
     * @param $consignee
     * @param $mobile
     * @param $address
     * @param $region
     * @param $tolerant
     * @return bool
     * @throws ThinkException
     */
    public static function address_add($member_id, $consignee, $mobile, $address, $region, $tolerant)
    {
        $tolerant AND self::address_default_remove($member_id);

        $data['consignee']   = $consignee;
        $data['mobile']      = $mobile;
        $data['address']     = $address;
        $data['province_id'] = $region['province_id'];
        $data['city_id']     = $region['city_id'];
        $data['district_id'] = $region['district_id'];
        $data['tolerant']    = self::address_count($member_id) == 0 ? true : $tolerant;
        $data['member_id']   = $member_id;

        $model = self::create($data);

        return !empty($model);
    }

    /**
     * 编辑地址
     * @param     $member_id
     * @param int $address_id 地址id
     * @param     $consignee
     * @param     $mobile
     * @param     $address
     * @param     $region
     * @param     $tolerant
     * @return bool
     * @throws DbException
     * @throws PDOException
     * @throws ThinkException
     */
    public static function address_edit($member_id, $address_id, $consignee, $mobile, $address, $region, $tolerant)
    {
        $tolerant AND self::address_default_remove($member_id);

        $data = [];
        empty($consignee) OR $data['consignee'] = $consignee;
        empty($mobile) OR $data['mobile'] = $mobile;
        empty($address) OR $data['address'] = $address;
        $data['tolerant'] = $tolerant;

        if (!empty($region)) {
            $data['province_id'] = $region['province_id'];
            $data['city_id']     = $region['city_id'];
            $data['district_id'] = $region['district_id'];
        }
        if (empty($data)) {
            return false;
        }

        $where = ['address_id' => $address_id];

        $model = self::get($where);

        if (empty($model)) {
            return false;
        }

        return $model->save($data) != 0;
    }

    /**
     * 地址默认状态变更
     * @param $member_id
     * @param $address_id
     * @param $tolerant
     * @return bool
     * @throws PDOException
     * @throws ThinkException
     */
    public static function address_default_change($member_id, $address_id, $tolerant)
    {
        $tolerant AND self::address_default_remove($member_id);

        $where['address_id'] = $address_id;
        $where['member_id']  = $member_id;

        $data['tolerant'] = $tolerant;

        return self::where($where)->update($data);
    }

    /**
     * 验证地址是否存在
     * @param $member_id
     * @param $address_id
     * @return bool
     * @throws ThinkException
     */
    public static function address_check($member_id, $address_id)
    {
        $where['address_id'] = $address_id;
        $where['member_id']  = $member_id;

        return self::check($where);
    }

    /**
     * 删除用户地址
     * @param $member_id
     * @param $address_id
     * @return bool
     * @throws PDOException
     * @throws ThinkException
     */
    public static function address_delete($member_id, $address_id)
    {
        $where['address_id'] = $address_id;
        $where['member_id']  = $member_id;

        return self::where($where)->delete() != 0;
    }

    /**
     * 将所有的默认地址更改为非默认地址
     * @param $member_id
     * @return int
     * @throws PDOException
     * @throws ThinkException
     */
    private static function address_default_remove($member_id)
    {
        $where['tolerant']  = true;
        $where['member_id'] = $member_id;

        $data['tolerant'] = false;

        return self::where($where)->update($data);
    }

    //-------------------------------------------------- 实例方法

    /**
     * 地址信息
     * @return array
     */
    public function address_info()
    {
        return [
            'consignee'   => $this->getAttr('consignee'),
            'mobile'      => $this->getAttr('mobile'),
            'address'     => $this->getAttr('address'),
            'province'    => $this->getRelation('province')->getAttr('name'),
            'province_id' => $this->getRelation('province')->getAttr('id'),
            'city'        => $this->getRelation('city')->getAttr('name'),
            'city_id'     => $this->getRelation('city')->getAttr('id'),
            'district'    => $this->getRelation('district')->getAttr('name'),
            'district_id' => $this->getRelation('district')->getAttr('id'),
        ];
    }

    //-------------------------------------------------- 读取器方法

    //-------------------------------------------------- 追加属性读取器方法

    //-------------------------------------------------- 修改器方法

    /**
     * 关联区域信息 省 修改器
     * @param Region $model
     * @return Region
     */
    public function setProvinceRegionRelation($model)
    {
        if (is_null($model)) {
            $model = new Region(['id' => 0, 'name' => '']);
        }

        $this->hidden(['province_id']);
        return $model;
    }

    /**
     * 关联区域信息 市 修改器
     * @param Region $model
     * @return Region
     */
    public function setCityRegionRelation($model)
    {
        if (is_null($model)) {
            $model = new Region(['id' => 0, 'name' => '']);
        }

        $this->hidden(['city_id']);
        return $model;
    }

    /**
     * 关联区域信息 区 修改器
     * @param Region $model
     * @return Region
     */
    public function setDistrictRegionRelation($model)
    {
        if (is_null($model)) {
            $model = new Region(['id' => 0, 'name' => '']);
        }

        $this->hidden(['district_id']);
        return $model;
    }

    //-------------------------------------------------- 关联加载方法

    /**
     * 关联区域信息 省 一对一 属于
     * @return BelongsTo
     */
    public function provinceRegion()
    {
        $relation = $this->belongsTo(Region::class, 'province_id');
        $relation->cache(true, null, Region::getCacheTag());
        $relation->field(['id', 'name']);
        $relation->where('type', Region::PROVINCE);
        return $relation;
    }

    /**
     * 关联区域信息 市 一对一 属于
     * @return BelongsTo
     */
    public function cityRegion()
    {
        $relation = $this->belongsTo(Region::class, 'city_id');
        $relation->cache(true, null, Region::getCacheTag());
        $relation->field(['id', 'name']);
        $relation->where('type', Region::CITY);
        return $relation;
    }

    /**
     * 关联区域信息 区 一对一 属于
     * @return BelongsTo
     */
    public function districtRegion()
    {
        $relation = $this->belongsTo(Region::class, 'district_id');
        $relation->cache(true, null, Region::getCacheTag());
        $relation->field(['id', 'name']);
        $relation->where('type', Region::DISTRICT);
        return $relation;
    }
}
