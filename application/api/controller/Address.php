<?php

namespace app\api\controller;

use Exception;
use helper\ValidateHelper;
use app\common\controller\ApiController;
use app\common\model\Region as RegionModel;
use app\common\model\MemberAddress as MemberAddressModel;

/**
 * 地址 API
 */
class Address extends ApiController
{
    /**
     * 地址列表接口
     * 分页
     * @return void
     * @throws Exception
     */
    public function address_list()
    {
        $this->check_login();

        $list = MemberAddressModel::address_list($this->member_id);
        output_success('', $list);
    }

    /**
     * 地址详情接口
     * @return void
     * @throws Exception
     */
    public function address_detail()
    {
        $address_id = $this->get_param('address_id');
        $this->check_login();

        $detail = MemberAddressModel::address_detail($this->member_id, $address_id);
        output_success('', $detail);
    }

    /**
     * 默认地址接口
     * @return void
     * @throws Exception
     */
    public function default_address()
    {
        $this->check_login();

        $detail = MemberAddressModel::default_address($this->member_id);
        output_success('', $detail);
    }

    /**
     * 地址添加接口
     * @return void
     * @throws Exception
     */
    public function address_add()
    {
        $consignee = $this->get_param('consignee');
        $address   = $this->get_param('address');
        $mobile    = $this->get_param('mobile');
        $district  = intval($this->get_param('district'));
        $tolerant  = boolval($this->get_param('tolerant', 0));
        $this->check_login();

        empty($consignee) AND output_error('请填写收件人！');
        empty($address) AND output_error('请填写收货地址！');
        ValidateHelper::is_mobile($mobile) OR output_error('手机号码格式错误！');

        $region = RegionModel::fill_region($district, RegionModel::DISTRICT);
        empty($region) AND output_error('地址信息错误！');

        $result = MemberAddressModel::address_add($this->member_id, $consignee, $mobile, $address, $region, $tolerant);
        $result OR output_error('地址添加失败！');

        output_success('地址添加成功！');
    }

    /**
     * 地址修改接口
     * @return void
     * @throws Exception
     */
    public function address_edit()
    {
        $consignee  = $this->get_param('consignee', '');
        $address    = $this->get_param('address', '');
        $mobile     = $this->get_param('mobile', '');
        $district   = $this->get_param('district', 0);
        $address_id = $this->get_param('address_id');
        $tolerant   = boolval($this->get_param('tolerant', 0));
        $this->check_login();

        $result = MemberAddressModel::address_check($this->member_id, $address_id);
        $result OR output_error('地址不存在！');

        $region = RegionModel::fill_region($district, RegionModel::DISTRICT);
        if (empty($consignee) && empty($address) && empty($mobile) && empty($region)) {
            output_error('未进行任何修改！');
        }

        if (!empty($mobile)) {
            ValidateHelper::is_mobile($mobile) OR output_error('手机号码格式错误！');
        }

        $result = MemberAddressModel::address_edit($this->member_id, $address_id, $consignee, $mobile, $address, $region, $tolerant);
        $result OR output_error('地址未修改成功！');
        output_success();
    }

    /**
     * 地址默认状态修改接口
     * @return void
     * @throws Exception
     */
    public function address_default_change()
    {
        $address_id = $this->get_param('address_id');
        $tolerant   = boolval($this->get_param('tolerant', false));
        $this->check_login();

        $result = MemberAddressModel::address_check($this->member_id, $address_id);
        $result OR output_error('地址不存在！');

        $result = MemberAddressModel::address_default_change($this->member_id, $address_id, $tolerant);
        $result OR output_error('地址修改失败！');
        output_success();
    }

    /**
     * 地址删除接口
     * @return void
     * @throws Exception
     */
    public function address_delete()
    {
        $address_id = $this->get_param('address_id');
        $this->check_login();

        $result = MemberAddressModel::address_check($this->member_id, $address_id);
        $result OR output_error('地址已经删除！');

        $result = MemberAddressModel::address_delete($this->member_id, $address_id);
        $result OR output_error('地址删除失败！');
        output_success();
    }
}
