<?php

namespace app\common\model;

use app\common\core\BaseModel;
use think\exception\DbException;
use think\exception\PDOException;
use think\Exception as ThinkException;

/**
 * 退款信息 模型
 */
class PaymentRefund extends BaseModel
{
    protected $type = ['del' => 'boolean', 'solved' => 'boolean'];

    protected $insert = ['del' => false, 'solved' => false];

    //-------------------------------------------------- 静态方法

    /**
     * 读取退款信息
     * @param $detail_id
     * @param $order_id
     * @return static
     * @throws DbException
     */
    public static function load_refund($detail_id, $order_id)
    {
        $where['order_id']  = $order_id;
        $where['detail_id'] = $detail_id;
        $where['del']       = false;

        return self::get($where);
    }

    /**
     * 创建退款订单
     * @param $detail_id
     * @param $order_id
     * @param $refund_sn
     * @param $money
     * @param $pay_type
     * @param $type
     * @return static
     */
    public static function create_refund($detail_id, $order_id, $refund_sn, $money, $pay_type, $type)
    {
        $data['detail_id']       = $detail_id;
        $data['order_id']        = $order_id;
        $data['pay_type']        = $pay_type;
        $data['type']            = $type;
        $data['refund_sn']       = $refund_sn;
        $data['money']           = $money;
        $data['third_refund_sn'] = '';
        $data['status']          = 0;

        $model = self::create($data);
        return $model;
    }

    //-------------------------------------------------- 实例方法

    /**
     * 处理完成提交
     * @param $third_refund_sn
     * @return false|int
     * @throws PDOException
     * @throws ThinkException
     */
    public function commit_solved($third_refund_sn)
    {
        $data = ['solved' => true, 'third_refund_sn' => $third_refund_sn, 'refund_time' => time()];

        return $this->save($data) != 0;
    }

    /**
     * 判断是否已处理
     */
    public function check_solved()
    {
        return $this->getAttr('solved');
    }

    //-------------------------------------------------- 读取器方法

    //-------------------------------------------------- 追加属性读取器方法

    //-------------------------------------------------- 修改器方法

    //-------------------------------------------------- 关联加载方法
}
