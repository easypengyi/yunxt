<?php

namespace app\common\model;

use helper\TimeHelper;
use app\common\core\BaseModel;
use think\exception\DbException;
use think\Exception as ThinkException;

/**
 * 配置 模型
 */
class Configure extends BaseModel
{
    protected $type = ['configure_value' => 'serialize'];

    //-------------------------------------------------- 静态方法

    /**
     * 获取配置值
     * @param $key
     * @return mixed
     * @throws DbException
     */
    public static function getValue($key)
    {
        $configure = self::get(['configure_name' => $key], [], [true, null, self::getCacheTag($key)]);
        return self::default_value($key, empty($configure) ? null : $configure->getAttr('configure_value'));
    }

    /**
     * 设置配置值
     * @param        $key
     * @param mixed  $value
     * @param string $desc
     * @return bool
     * @throws DbException
     * @throws ThinkException
     */
    public static function setValue($key, $value, $desc = null)
    {
        $configure = self::get(['configure_name' => $key], [], [true, null, self::getCacheTag($key)]);
        empty($configure) AND $configure = new static(['configure_name' => $key, 'configure_desc' => '']);

        is_null($desc) OR $configure->setAttr('configure_desc', $desc);
        $configure->setAttr('configure_value', $value);
        $result = $configure->save() != 0;
        $result AND self::cacheClear($key);
        return $result;
    }

    /**
     * 默认值
     * @param $key
     * @param $value
     * @return mixed
     */
    private static function default_value($key, $value)
    {
        if (is_null($value)) {
            switch ($key) {
                case 'default_head_image':
                case 'system_massage_image':
                case 'first_agent_ratio':
                case 'second_agent_ratio':
                    $value = 0;
                    break;
                case 'leaguer_price':
                    $value = 0.01;
                    break;
                case 'sms_effective_time':
                    $value = TimeHelper::minutesToSecond(10);
                    break;
                case 'sms_interval_time':
                    $value = 1;
                    break;
                case 'default_order_timeout_time':
                    $value = 5;
                    break;
                case 'message_expiry_day':
                    $value = 7;
                    break;
                case 'withdrawal_service_ratio':
                    $value = '0';
                    break;
                case 'withdrawal_mini_amount':
                    $value = 0.1;
                    break;
                case 'use_help':
                case 'service_phone':
                case 'register_agreement':
                case 'private_help':
                case 'join_us':
                case 'contact_us':
                case 'leaguer_intro':
                default:
                    $value = '';
                    break;
            }
        }
        return $value;
    }

    //-------------------------------------------------- 实例方法

    //-------------------------------------------------- 读取器方法

    //-------------------------------------------------- 追加属性读取器方法

    //-------------------------------------------------- 修改器方法

    //-------------------------------------------------- 关联加载方法
}
