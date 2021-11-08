<?php

namespace app\common\job;

use think\Log;
use think\queue\Job;
use tool\YunhpayTool;
use app\common\model\Message as MessageModel;

/**
 * 退款回调处理
 */
class RefundCallback
{
    /**
     * 执行方法
     * @param Job $job
     * @param     $data
     * @return void
     */
    public function fire(Job $job, $data)
    {
        $result = YunhpayTool::instance([])->refund($data['type'], $data['order_id']);
        if ($result['status']) {
            $job->delete();
            return;
        }

        if ($job->attempts() > 3) {
            if ($result['msg'] == 'failure：Business Failed(卖家余额不足)') {
                MessageModel::insert_message(0, 0, '支付宝账户退款金额不足！', MessageModel::SYSTEM);
            } elseif ($result['msg'] == '交易未结算资金不足，请使用可用余额退款') {
                MessageModel::insert_message(0, 0, '微信账户退款金额不足！', MessageModel::SYSTEM);
            }
            // TODO 退款失败处理
            Log::error([$result, $data]);
            $job->delete();
            return;
        }
    }
}