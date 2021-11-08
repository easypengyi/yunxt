<?php

namespace app\callback\controller;

use tool\PaymentTool;
use app\common\core\Common;

/**
 * 支付回调
 */
class Defray extends Common
{
    /**
     * 支付回调方法
     * @param $pay_type
     */
    public function callback($pay_type)
    {
        switch ($pay_type) {
            default:
                $config = [];
                break;
        }

        PaymentTool::instance($config)->callback($pay_type);
    }
}
