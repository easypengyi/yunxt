<?php

namespace app\common\model;

use Tree;
use Think\Cache;
use think\Config;
use think\Request;
use think\db\Query;
use helper\HttpHelper;
use app\common\core\BaseModel;
use think\exception\DbException;

/**
 * 权限菜单 模型
 */
class AdminRule extends BaseModel
{
    protected $type = [
        'enable'    => 'boolean',
        'display'   => 'boolean',
        'notcheck'  => 'boolean',
        'unassign'  => 'boolean',
        'parameter' => 'serialize',
    ];

    protected $hidden = ['parameter'];

    //-------------------------------------------------- 静态方法

    /**
     * 所有的权限菜单
     * @return array
     * @throws DbException
     */
    public static function all_auth_rule()
    {
        $list = self::all_list([], [], ['sort' => 'asc'], [true, null, self::getCacheTag()]);

        return $list->toArray();
    }

    /**
     * 权限菜单--已启用权限
     * @return array
     * @throws DbException
     */
    public static function rule_tree()
    {
        $where['enable']   = true;
        $where['unassign'] = true;

        $list = self::all_list([], $where, ['sort' => 'asc']);

        $tree = Tree::instance();
        $tree->init($list->toArray(), ['child' => 'sub', 'parentid' => 'pid']);
        return $tree->get_arraylist();
    }

    /**
     * 权限菜单--用户拥有权限
     * @param     $admin_id
     * @return array
     * @throws DbException
     */
    public static function auth_menus($admin_id)
    {
        $key = __CLASS__ . __FUNCTION__ . $admin_id;

        $menus = Cache::get($key);
        if ($menus) {
            return $menus;
        }

        $result = self::has_no_check_group($admin_id);
        if (!$result) {
            $auth_ids_list = self::auth_list($admin_id);
            if (empty($auth_ids_list)) {
                return [];
            }
            $where['id'] = ['in', $auth_ids_list];
        }
        $where['display'] = true;
        $where['enable']  = true;

        $list = self::all_list([], $where, ['sort' => 'asc', 'id' => 'asc']);

        $tree = Tree::instance();
        $tree->init($list->toArray(), ['child' => '_child', 'parentid' => 'pid']);
        $menus = $tree->get_arraylist();

        Cache::set($key, $menus, null);
        Cache::tag(self::getCacheTag(), $key);
        Cache::tag(AdminGroup::getCacheTag(), $key);
        return $menus;
    }

    /**
     * 获取所有父节点id(含自身)
     * @param int $id 节点id
     * @return array
     */
    public static function menus_parent_id($id)
    {
        if (empty($id)) {
            return [];
        }

        $key = __CLASS__ . __FUNCTION__ . $id;

        $ids = Cache::get($key);
        if ($ids) {
            return $ids;
        }

        $list = self::where(['enable' => true])->order(['level' => 'desc', 'sort' => 'asc'])->column('pid', 'id');
        $ids  = [];
        $key  = $id;
        do {
            $ids[] = $key;
            $key   = $list[$key];
            if (empty($key)) {
                break;
            }
        } while (isset($list[$key]));
        $ids = array_reverse($ids);

        Cache::tag(self::getCacheTag())->set($key, $ids);
        return $ids;
    }

    /**
     * 获取指定url的信息(可能为显示状态或非显示状态)
     * @param boolean $display true表示取显示状态,false则不限制
     * @return array
     * @throws DbException
     */
    public static function url_info($display = false)
    {
        $request = Request::instance();

        $base_url = $request->module() . '/' . $request->controller() . '/' . $request->action();

        $field = ['id', 'name', 'title', 'parameter'];

        $where['base_url'] = $base_url;
        $where['enable']   = true;
        $display AND $where['display'] = true;

        $order = ['level' => 'desc', 'sort' => 'asc', 'parameter' => 'desc'];

        $list = self::all_list($field, $where, $order);

        $info = ['id' => 0, 'name' => '', 'title' => ''];
        foreach ($list as $v) {
            $parameter = $v->getAttr('parameter');
            is_array($parameter) OR $parameter = [];
            $parameter = $request->only($parameter);
            $url       = HttpHelper::get_url_query($base_url, $parameter);

            if ($url === $v->getAttr('name')) {
                $info = $v->toArray();
                break;
            }
        }

        return $info;
    }

    /**
     * 权限检测
     * @param int $id
     * @param int $admin_id
     * @return bool
     * @throws DbException
     */
    public static function check_auth($id = 0, $admin_id = 0)
    {
        $result = self::has_no_check_group($admin_id);
        if ($result) {
            return true;
        }
        if (empty($id)) {
            return false;
        }
        $auth_ids_list = self::auth_list($admin_id);
        if (empty($auth_ids_list)) {
            return false;
        }
        if (in_array($id, $auth_ids_list)) {
            return true;
        }
        return false;
    }

    /**
     * 是否存在无需验证的用户组
     * @param $admin_id
     * @return bool
     * @throws DbException
     */
    private static function has_no_check_group($admin_id)
    {
        $no_check_group_id = Config::get('admin_rule.no_check_group_id');
        if (!empty($no_check_group_id)) {
            $group = AdminGroup::user_group_array($admin_id);

            foreach ($no_check_group_id as $v) {
                if (array_key_exists($v, $group)) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * 获得权限列表
     * @param integer $admin_id 用户ID
     * @return array
     * @throws DbException
     */
    private static function auth_list($admin_id)
    {
        $key = __CLASS__ . __FUNCTION__ . $admin_id;

        $authList = Cache::get($key);
        if (!empty($authList)) {
            return $authList;
        }

        // 读取用户所属用户组
        $groups = AdminGroup::user_group_array($admin_id);
        $ids    = [];//保存用户所属用户组设置的所有权限规则id
        foreach ($groups as $v) {
            $ids = array_merge($ids, $v['rules']);
        }

        $ids = array_unique($ids);

        // 读取用户组所有权限规则
        $where = function (Query $query) use ($ids) {
            $query->where(['id' => ['in', $ids], 'notcheck' => false, 'enable' => true]);
        };

        $authList = self::where(['notcheck' => true, 'enable' => true])->whereOr($where)->column('id');

        Cache::set($key, $authList);
        Cache::tag(self::getCacheTag(), $key);
        Cache::tag(AdminGroup::getCacheTag(), $key);
        return $authList;
    }

    //-------------------------------------------------- 实例方法

    //-------------------------------------------------- 读取器方法

    //-------------------------------------------------- 追加属性读取器方法

    //-------------------------------------------------- 修改器方法

    /**
     * 路径 修改器
     * @param string $value
     * @return string
     */
    public function setNameAttr($value)
    {
        $info = parse_url(strval($value));
        if (isset($info['query'])) {
            parse_str($info['query'], $parameter);
            ksort($parameter);
        } else {
            $parameter = [];
        }

        $this->setAttr('parameter', array_keys($parameter));
        $this->setAttr('base_url', $info['path']);
        return HttpHelper::get_url_query($info['path'], $parameter);
    }

    //-------------------------------------------------- 关联加载方法
}
