<?php

namespace app\common\model;

use tool\PaymentTool;
use app\common\core\BaseModel;
use think\exception\DbException;
use think\exception\PDOException;
use think\Exception as ThinkException;

/**
 * 支付配置 模型
 */
class PaymentConfig extends BaseModel
{
    protected $type = ['enable' => 'boolean', 'tolerant' => 'boolean', 'config' => 'serialize'];

    protected $append = ['payment_name', 'pay_type_name'];

    //-------------------------------------------------- 静态方法

    /**
     * 获取配置信息
     * @param $payment_id
     * @param $pay_type
     * @return array
     * @throws DbException
     */
    public static function find_config($payment_id, $pay_type)
    {
        if (empty($pay_type)) {
            return [];
        }

        $where = ['payment_id' => $payment_id, 'pay_type' => $pay_type];

        $query = self::field(['config', 'payment_id'])->where($where)->order(['tolerant' => 'asc', 'id' => 'desc']);
        $model = self::get($query, [], [true, null, self::getCacheTag()]);

        return empty($model) ? [] : $model->getAttr('config_info');
    }

    /**
     * @param $id
     * @param $pay_type
     * @throws PDOException
     * @throws ThinkException
     */
    public static function default_remove($id, $pay_type)
    {
        $where = ['id' => ['NOT IN', $id], 'pay_type' => $pay_type, 'tolerant' => true];
        self::where($where)->update(['tolerant' => false]);
    }

    //-------------------------------------------------- 实例方法

    //-------------------------------------------------- 读取器方法

    //-------------------------------------------------- 追加属性读取器方法

    /**
     * 获取支付信息 读取器
     * @param $value
     * @param $data
     * @return mixed
     */
    public function getConfigInfoAttr($value, $data)
    {
        if (!is_null($value)) {
            return $value;
        }

        $value = $this->getAttr('config');
        switch ($data['payment_id']) {
            case PaymentTool::ALIPAY:
                $value['rsa_private_key'] = ROOT_PATH . $value['rsa_private_key'];
                $value['ali_public_key']  = ROOT_PATH . $value['ali_public_key'];
                break;
            case PaymentTool::WXPAY:
                $value['app_cert_pem'] = ROOT_PATH . $value['app_cert_pem'];
                $value['app_key_pem']  = ROOT_PATH . $value['app_key_pem'];
                break;
            case PaymentTool::UPACPAY:
                $value['sign_cert_path'] = ROOT_PATH . $value['sign_cert_path'];
                break;
            case PaymentTool::UPAY:
                break;
            default:
                break;
        }
        return $value;
    }

    /**
     * 支付类型名称
     * @param $value
     * @param $data
     * @return string
     */
    public function getPaymentNameAttr($value, $data)
    {
        if (!is_null($value)) {
            return $value;
        }

        $payment = PaymentTool::instance()->payment_info($data['payment_id']);

        return empty($payment) ? '' : $payment['short_name'];
    }

    /**
     * 支付种类名称
     * @param $value
     * @param $data
     * @return string
     */
    public function getPayTypeNameAttr($value, $data)
    {
        if (!is_null($value)) {
            return $value;
        }

        return PaymentTool::instance()->pay_type_name($data['pay_type']);
    }

    //-------------------------------------------------- 修改器方法

    //-------------------------------------------------- 关联加载方法
}
