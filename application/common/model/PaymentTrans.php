<?php

namespace app\common\model;

use app\common\core\BaseModel;
use think\exception\DbException;
use think\exception\PDOException;
use think\Exception as ThinkException;

/**
 * 转账信息 模型
 */
class PaymentTrans extends BaseModel
{
    protected $type = ['del' => 'boolean', 'solved' => 'boolean'];

    protected $insert = ['del' => false, 'solved' => false];

    protected $autoWriteTimestamp = true;

    //-------------------------------------------------- 静态方法

    /**
     * 读取信息
     * @param $order_id
     * @return static
     * @throws DbException
     */
    public static function load_trans($order_id)
    {
        $where['order_id'] = $order_id;
        $where['del']      = false;

        return self::get($where);
    }

    /**
     * 创建订单
     * @param $order_id
     * @param $trans_sn
     * @param $money
     * @param $pay_type
     * @param $type
     * @return static
     */
    public static function create_trans($order_id, $trans_sn, $money, $pay_type, $type)
    {
        $data['order_id'] = $order_id;
        $data['pay_type'] = $pay_type;
        $data['type']     = $type;
        $data['trans_sn'] = $trans_sn;
        $data['money']    = $money;
        $data['third_sn'] = '';
        $data['status']   = 0;

        $model = self::create($data);
        return $model;
    }

    //-------------------------------------------------- 实例方法

    /**
     * 处理完成提交
     * @param $third_sn
     * @return false
     * @throws PDOException
     * @throws ThinkException
     */
    public function commit_solved($third_sn)
    {
        $data = ['solved' => true, 'third_sn' => $third_sn];

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
