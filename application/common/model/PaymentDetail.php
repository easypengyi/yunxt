<?php

namespace app\common\model;

use app\common\core\BaseModel;
use think\exception\DbException;
use think\exception\PDOException;
use think\Exception as ThinkException;

/**
 * 支付信息 模型
 */
class PaymentDetail extends BaseModel
{
    protected $type = [
        'del'         => 'boolean',
        'solved'      => 'boolean',
        'use_sandbox' => 'boolean',
        'order_ids'   => 'plode',
    ];

    protected $insert = ['del' => false, 'solved' => false];

    //-------------------------------------------------- 静态方法

    /**
     * 读取信息
     * @param $key
     * @param $payment_id
     * @param $type
     * @param $pay_type
     * @return static
     * @throws DbException
     */
    public static function load_payment_info($key, $type, $payment_id, $pay_type)
    {
        $where['payment_key'] = $key;
        $where['type']        = $type;
        $where['payment_id']  = $payment_id;
        $where['pay_type']    = $pay_type;
        $where['del']         = false;

        return self::get($where);
    }

    /**
     * 支付验证
     * @param $key
     * @param $pay_type
     * @return bool
     * @throws ThinkException
     */
    public static function check_payment($key, $pay_type)
    {
        $where['payment_key'] = $key;
        $where['pay_type']    = $pay_type;
        $where['solved']      = true;
        $where['del']         = false;

        return self::check($where);
    }

    /**
     * 创建支付订单
     * @param $key
     * @param $type
     * @param $payment_id
     * @param $pay_type
     * @param $info
     * @return static
     */
    public static function create_payment($key, $type, $payment_id, $pay_type, $info)
    {
        $data['payment_id']     = $payment_id;
        $data['type']           = $type;
        $data['payment_key']    = $key;
        $data['pay_type']       = $pay_type;
        $data['payment_sn']     = $info['payment_sn'];
        $data['pay_value']      = $info['money'];
        $data['order_ids']      = $info['id'];
        $data['timeout']        = $info['timeout_express'];
        $data['payment_time']   = time();
        $data['status']         = 0;
        $data['use_sandbox']    = false;
        $data['info']           = isset($info['info']) ? $info['info'] : '';
        $data['transaction_sn'] = '';

        $model = self::create($data);
        return $model;
    }

    //-------------------------------------------------- 实例方法

    /**
     * 处理完成提交
     * @param $transaction_sn
     * @return false|int
     * @throws PDOException
     * @throws ThinkException
     */
    public function commit_solved($transaction_sn)
    {
        return $this->save(['solved' => true, 'transaction_sn' => $transaction_sn]) != 0;
    }

    /**
     * 判断是否已处理
     */
    public function check_solved()
    {
        return $this->getAttr('solved');
    }

    /**
     * 判断是否超时
     * @return bool
     */
    public function check_timeout()
    {
        return $this->getAttr('timeout') < time();
    }

    //-------------------------------------------------- 读取器方法

    //-------------------------------------------------- 追加属性读取器方法

    //-------------------------------------------------- 修改器方法

    //-------------------------------------------------- 关联加载方法
}
