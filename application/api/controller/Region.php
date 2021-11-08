<?php

namespace app\api\controller;

use app\common\model\MemberGroup;
use Exception;
use app\common\controller\ApiController;
use app\common\model\Region as RegionModel;

/**
 * 区域信息 API
 */
class Region extends ApiController
{
    /**
     * 获取全部省市区信息接口
     * @return void
     * @throws Exception
     */
    public function area()
    {
        $level        = $this->get_param('level', 3);
        $list['list'] = RegionModel::area_cache($level);
        output_success('', $list);
    }


}
