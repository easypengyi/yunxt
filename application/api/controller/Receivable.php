<?php

namespace app\api\controller;

use app\common\model\Configure as ConfigureModel;
use app\common\model\MemberBalance;
use app\common\model\MemberCommission;
use app\common\model\Message;
use helper\StrHelper;
use think\Db;
use Exception;
use helper\ValidateHelper;
use app\common\controller\ApiController;
use app\common\model\Member as MemberModel;
use app\common\model\OrderWithdrawals as OrderWithdrawalsModel;
use tool\AliyunSms;

/**
 * 收款 API
 */
class Receivable extends ApiController
{
    /**
 * 提现
 * @return void
 * @throws Exception
 */
    public function withdrawals()
    {
        $type     = intval($this->get_param('type', 1));
        $money     = floatval($this->get_param('money'));
        $account   = $this->get_param('account');
        $blank     = $this->get_param('blank');
        $bank_name     = $this->get_param('bank_name');
        $real_name = $this->get_param('real_name');
        $mobile    = $this->get_param('mobile');
        $this->check_login();

        if($money <= 0){
            output_error('提现金额必须大于0！');
        }
        $pay_image_id = 0;
        if($type == 1){
            empty($account) AND output_error('请填写银行卡号！');
            empty($bank_name) AND output_error('请填写银行名称！');
            empty($blank) AND output_error('请填写开户行！');
            empty($real_name) AND output_error('请填写持卡人姓名！');
            ValidateHelper::is_mobile($mobile) OR output_error('手机号码格式错误！');
        }else if($type == 2 || $type == 3){
            $file = null;
            if ($this->request->file()) {
                $result = $this->upload_thumb();
                $result['status'] OR output_error($result['message']);
                $file = $result['data'];
            }
            if(empty($file)){
                output_error('请先上传收款码图片！');
            }
            $pay_image_id = $file['file_id'];
        }

        $ratio = ConfigureModel::getValue('withdrawal_service_ratio');
        $service_amount = StrHelper::ceil_decimal(($money * $ratio/100), 2);

        try {
            Db::startTrans();
            $result = MemberModel::commission_dec($this->member_id, $money+$service_amount);
            if(!$result){
                Db::rollback();
                output_error('提现金额不足');
            }
            $order_id = OrderWithdrawalsModel::order_place($this->member_id, $account, $blank, $bank_name,$real_name, $money, $service_amount, $type, $pay_image_id);
            if(empty($order_id)){
                Db::rollback();
                output_error('提现失败！');
            }
            Db::commit();
        } catch (Exception $e) {
            Db::rollback();
            throw $e;
        }

        Message::commission($this->member_id, $money, $order_id);
        //发送短信
        $member = MemberModel::get($this->member_id);
        if(!empty($member['member_tel'])){
            $sms       = AliyunSms::instance();
            $time = date('Y-m-d H:i:s');
            $option = ['name'=> $member['member_realname'], 'datetime'=> $time, 'money'=> $money];
            $sms->send_message($member['member_tel'], $option, 'cash', $this->member_id);
        }
        output_success('您的提现申请已提交成功，公司会核对阁下的银行资料正确无误后，您的奖金将会在三个工作天到账');
    }




    /**
     * 云库存赠送
     * @return void
     * @throws Exception
     */
    public function amount_give()
    {
        $balance     = floatval($this->get_param('balance'));
        $mobile       = $this->get_param('mobile');


        $this->check_login();
        empty($balance) AND output_error('请填写云库存数量！');
        empty($mobile) AND output_error('请填写转赠人手机号！');
        ValidateHelper::is_mobile($mobile) OR output_error('手机号码格式错误！');


        $Member =  MemberModel::where(['member_tel'=>$mobile])->find();
        empty($Member) AND output_error('转赠人不存在！');

        if ($Member['member_id'] == $this->member_id){
            output_error('转赠人不可以是自己！');
        }

        $My =  MemberModel::get($this->member_id);

        try {
            Db::startTrans();
            $result = MemberModel::balance_dec($this->member_id, $balance);
            MemberBalance::insert_log($this->member_id,MemberBalance::give,$balance,'转赠给'.$Member['member_realname'],0);
            $result OR output_error('云库存数量不足');
            MemberModel::balance_inc($Member['member_id'], $balance);
            MemberBalance::insert_log($Member['member_id'],MemberBalance::collect,$balance,'来自'.$My['member_realname'].'的库存',0);
            Db::commit();
        } catch (Exception $e) {
            Db::rollback();
            throw $e;
        }
        output_success('转赠成功');
    }

}
