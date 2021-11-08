<?php

namespace app\common\model;

use helper\StrHelper;
use app\common\core\BaseModel;
use think\exception\DbException;

/**
 * 后台用户 模型
 */
class Admin extends BaseModel
{
    protected $type = ['del' => 'boolean', 'enable' => 'boolean'];

    protected $hidden = ['admin_pwd', 'admin_pwd_salt'];

    protected $append = ['group_name'];

    protected $file = ['admin_headpic_id' => 'admin_headpic'];

    //-------------------------------------------------- 静态方法

    /**
     * 用户登录
     * @param string $username 用户名
     * @param string $password 密码
     * @return bool|static
     * @throws DbException
     */
    public static function login($username = '', $password = '')
    {
        $where = ['admin_username' => $username, 'del' => false];

        $model = self::get($where);

        if (empty($model)) {
            return false;
        }

        if (!$model->password_check($password)) {
            return false;
        }

        return $model;
    }

    //-------------------------------------------------- 实例方法

    /**
     * 验证密码是否正确
     * @param $password
     * @return bool
     */
    public function password_check($password)
    {
        $data     = $this->getData();
        $password = $this->create_password($password, $data['admin_pwd_salt']);

        return $password == $data['admin_pwd'];
    }

    /**
     * 生成密码
     * @param $password
     * @param $salt
     * @return string
     */
    private function create_password($password, $salt)
    {
        return md5(md5($password) . md5($salt));
    }

    //-------------------------------------------------- 读取器方法

    //-------------------------------------------------- 追加属性读取器方法

    /**
     * 管理员组名称 读取器
     * @param $value
     * @param $data
     * @return string
     * @throws DbException
     */
    public function getGroupNameAttr($value, $data)
    {
        if (!is_null($value)) {
            return $value;
        }

        $list = AdminGroup::user_group_array($data['admin_id'], false);

        return implode('、', array_column($list, 'group_name'));
    }

    //-------------------------------------------------- 修改器方法

    /**
     * 密码设置 修改器
     * @param $admin_pwd
     * @return string
     */
    public function setAdminPwdAttr($admin_pwd)
    {
        $salt = StrHelper::random_string('alpha', 10);

        $this->setAttr('admin_pwd_salt', $salt);
        $this->setAttr('admin_changepwd_time', time());
        return $this->create_password($admin_pwd, $salt);
    }

    //-------------------------------------------------- 关联加载方法
}
