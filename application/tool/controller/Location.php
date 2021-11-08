<?php

namespace app\tool\controller;

use think\Config;
use app\common\core\Common;

/**
 * 定位
 */
class Location extends Common
{
    /**
     * 初始化方法
     */
    public function _initialize()
    {
        parent::_initialize();
        if (!$this->check_referer()) {
            $this->error('无访问权限！');
        }
    }

    /**
     * 地址展示
     * @return mixed
     */
    public function map()
    {
        $geomap_ak = Config::get('location.geomap_ak');
        $longitude = floatval(trim(input('longitude', 0)));
        $latitude  = floatval(trim(input('latitude', 0)));
        $address   = trim(input('address', ''));

        $this->assign('emoji_open', false);
        $this->assign('geomap_ak', $geomap_ak);
        $this->assign('longitude', $longitude);
        $this->assign('latitude', $latitude);
        $this->assign('address', $address);
        return $this->fetch();
    }

    /**
     * 选择地址定位
     * @return mixed
     */
    public function choose_location()
    {
        $geomap_ak = Config::get('location.geomap_ak');
        $longitude = floatval(trim(input('longitude', 0)));
        $latitude  = floatval(trim(input('latitude', 0)));
        $address   = trim(input('address', ''));
        $edit      = true;

        if (empty($longitude) || empty($latitude)) {
            $edit      = false;
            $longitude = Config::get('location.longitude');
            $latitude  = Config::get('location.latitude');
            $address   = '';
        }

        $this->assign('callback', input('callback', ''));
        $this->assign('emoji_open', false);
        $this->assign('edit', $edit);
        $this->assign('geomap_ak', $geomap_ak);
        $this->assign('longitude', $longitude);
        $this->assign('latitude', $latitude);
        $this->assign('address', $address);
        return $this->fetch();
    }
}
