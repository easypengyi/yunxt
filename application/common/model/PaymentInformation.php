<?php

namespace app\common\model;

use think\Request;
use app\common\core\BaseModel;

/**
 * 支付记录 模型
 */
class PaymentInformation extends BaseModel
{
    protected $autoWriteTimestamp = true;

    //-------------------------------------------------- 静态方法

    /**
     * 添加记录
     * @param $status
     * @param $message
     * @param $payment_detail_id
     * @param $payment_id
     * @param $information
     * @return bool
     */
    public static function insert_information($status, $message, $payment_id, $payment_detail_id = 0, $information = [])
    {
        $parameter = [
            'get'   => Request::instance()->get(),
            'post'  => Request::instance()->post(),
            'input' => Request::instance()->getInput(),
        ];

        $data['payment_detail_id'] = $payment_detail_id;
        $data['payment_id']        = $payment_id;
        $data['status']            = $status;
        $data['message']           = $message;
        $data['information']       = serialize($information);
        $data['parameter']         = serialize($parameter);

        $model = self::create($data);

        return !empty($model);
    }

    //-------------------------------------------------- 实例方法

    //-------------------------------------------------- 读取器方法

    //-------------------------------------------------- 追加属性读取器方法

    //-------------------------------------------------- 修改器方法

    //-------------------------------------------------- 关联加载方法
}
