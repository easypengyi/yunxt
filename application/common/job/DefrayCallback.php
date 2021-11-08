<?php

namespace app\common\job;

use think\queue\Job;
use tool\PaymentTool;

/**
 * 余额支付回调处理
 */
class DefrayCallback
{
    /**
     * 执行方法
     * @param Job $job
     * @param     $data
     * @return void
     */
    public function fire(Job $job, $data)
    {

        if ($job->attempts() > 3) {
            $job->delete();
            return;
        }

        $_POST['order_no'] = $data['param']['payment_sn'];
        $_POST['money']    = $data['param']['money'];

        $result = PaymentTool::instance([])->callback($data['pay_type']);
        $result AND $job->delete();
    }
}