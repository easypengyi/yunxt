<?php

namespace app\api\controller;

use think\Db;
use Exception;
use app\common\controller\ApiController;
use app\common\model\Message as MessageModel;
use app\common\model\CouponCode as CouponCodeModel;
use app\common\model\MemberCoupon as MemberCouponModel;
use app\common\model\CouponTemplate as CouponTemplateModel;

/**
 * 优惠券 API
 */
class Coupon extends ApiController
{
    /**
     * 会员优惠券列表接口
     * 分页
     * type 1-通用 2-专用
     * @return void
     * @throws Exception
     */
    public function member_coupon_list()
    {
        $coupon_type = intval($this->get_param('coupon_type'));
        $this->check_login();

        $list = MemberCouponModel::member_coupon_list($this->member_id, $coupon_type);
        empty($list) AND output_error('参数错误！');
        output_success('', $list);
    }

    /**
     * 会员可用优惠券列表接口
     * 分页
     * @return void
     * @throws Exception
     */
    public function coupon_use_list()
    {
        $product_id = $this->get_param('product_id');
        $money      = floatval($this->get_param('money'));
        $coupon_id  = $this->get_param('coupon_id', '');
        $this->check_login();

        empty($product_id) AND output_error('商品ID不能为空！');

        $list = MemberCouponModel::coupon_use_list($this->member_id, $money, $product_id, $coupon_id);
        output_success('', $list);
    }

    /**
     * 可领取优惠券列表接口
     * @return void
     * @throws Exception
     */
    public function receive_coupon_list()
    {
        $list = CouponTemplateModel::receive_coupon_list($this->member_id);
        output_success('', $list);
    }

    /**
     * 商铺优惠券列表接口
     * 分页
     * @return void
     * @throws Exception
     */
    public function store_coupon_list()
    {
        $product_id = $this->get_param('product_id', '');
        $money      = floatval($this->get_param('money', 0));

        $list = CouponTemplateModel::store_coupon_list($money, $product_id, $this->member_id);
        output_success('', $list);
    }

    /**
     * 领取优惠券接口
     * @return void
     * @throws Exception
     */
    public function receive_coupon()
    {
        $template_id = $this->get_param('template_id');
        $this->check_login();

        try {
            Db::startTrans();
            $result = CouponTemplateModel::receive_coupon($template_id, $this->member_id);
            $result OR output_error('优惠券已被领光或达到领取上限！');
            $template = CouponTemplateModel::get($template_id);
            empty($template) AND output_error('领取失败！');
            $result = MemberCouponModel::coupon_insert($template->create_coupon_data(), $this->member_id);
            empty($result) AND output_error('领取失败！');
            Db::commit();
        } catch (Exception $e) {
            Db::rollback();
            throw $e;
        }

        $template = CouponTemplateModel::get($template_id);
        $template->setAttr('current_member_id', $this->member_id);
        $receive_number = MemberCouponModel::receive_number($this->member_id, $template_id);
        $surplus_number = max(($template->getAttr('number_limit') - $receive_number), 0);
        output_success('领取成功！', ['receive' => $template->getAttr('receive'), 'surplus_number' => $surplus_number]);
    }

    /**
     * 优惠券提醒接口
     * @return void
     * @throws Exception
     */
    public function coupon_remind()
    {
        $template_id = $this->get_param('template_id');
        $this->check_login();

        $result = MessageModel::coupon_check_exists($this->member_id, $template_id);
        $result AND output_error('该优惠券已设置提醒！');

        $coupon = CouponTemplateModel::coupon_remind_info($template_id);
        empty($coupon) AND output_error('优惠券已经可以领取！');

        $remind_time = $coupon->getAttr('start_receive_time') - 60;
        $remind_time <= time() AND output_error('优惠券领取即将开始！');

        MessageModel::coupon_remind_message(
            $this->member_id,
            $coupon->getAttr('coupon_name'),
            $template_id,
            $remind_time
        );
        output_success();
    }

    /**
     * 优惠券激活接口
     * @return void
     * @throws Exception
     */
    public function coupon_activation()
    {
        $code = trim($this->get_param('code'));
        $this->check_login();

        $coupon = CouponCodeModel::get(['activation_code' => $code, 'status' => CouponCodeModel::STATUS_NO_HANDLE]);
        empty($coupon) AND output_error('激活码不可用！');

        $template_id = $coupon->getAttr('template_id');
        try {
            Db::startTrans();
            $result = CouponTemplateModel::receive_coupon($template_id, $this->member_id);
            $result OR output_error('优惠券已被领光或达到领取上限！');
            $template = CouponTemplateModel::get($template_id);
            empty($template) AND output_error('领取失败！');
            $result = MemberCouponModel::coupon_insert($template->create_coupon_data(), $this->member_id);
            empty($result) AND output_error('领取失败！');
            $result = CouponCodeModel::receive_finish($coupon->getAttr('code_id'), $this->member_id);
            empty($result) AND output_error('领取失败！');
            Db::commit();
        } catch (Exception $e) {
            Db::rollback();
            throw $e;
        }
        output_success('领取成功！');
    }
}