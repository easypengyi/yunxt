<?php

namespace app\common\controller;

use Exception;
use think\Cache;
use think\Config;
use think\Session;
use tool\ExcelTool;
use helper\HttpHelper;
use app\common\core\Common;
use app\common\model\Admin as AdminModel;
use app\common\model\LogWeb as LogWebModel;
use app\common\model\AdminRule as AdminRuleModel;

/**
 * 总后台基础类
 */
abstract class AdminController extends Common
{
    protected $module = 'admin';
    // 会员存放标签
    private $user_tag = 'admin_user';
    // 不进行登录验证
    protected $no_need_login = false;
    // 不进行登录验证
    protected $no_check = false;
    // 权限信息
    protected $url_info = [];
    // 登录会员信息
    protected $user = [];
    // 显示处理
    protected $show = ['navigation_bar' => true, 'left_nav' => true, 'head_nav' => true];
    // 公共参数
    protected $param = [];
    // 搜索参数
    protected $search = [];
    // 界面标题
    protected $title = '';

    /**
     * 初始化方法
     * @return void
     * @throws Exception
     */
    protected function _initialize()
    {
        parent::_initialize();

        $this->user = $this->get_user();

        $this->update_user_info();

        // 来源地址 处理
        if (!$this->check_referer()) {
            $this->http_referer = '';
        }

        if ($this->no_need_login) {
            return;
        }

        //未登录
        if (!$this->check_login()) {
            if ($this->is_ajax) {
                $this->error('登录失效请重新登录！', folder_url('Login/index'));
            } else {
                $this->redirect(folder_url('Login/index'));
            }
        }

        $this->url_info = AdminRuleModel::url_info();

        //权限检查
        if (!$this->no_check && !AdminRuleModel::check_auth($this->url_info['id'], $this->user['admin_id'])) {
            $this->error('没有权限', folder_url());
        }
    }

    /**
     * 空操作
     * @return void
     */
    public function _empty()
    {
        $this->is_ajax AND $this->error('请求方法不存在！');
        $this->redirect(folder_url());
    }

    /**
     * 操作者信息
     * @return array
     */
    public function operator_info()
    {
        return empty($this->user) ?
            ['operator_id' => 0, 'type' => LogWebModel::TYPE_NO] :
            ['operator_id' => $this->user['admin_id'], 'type' => LogWebModel::TYPE_ADMIN];
    }

    /**
     * 验证是否登录
     * @return int
     * @throws Exception
     */
    protected function check_login()
    {
        if (empty($this->user)) {
            return 0;
        }
        if (isset($this->user['admin_id'])) {
            return $this->user['admin_id'];
        }
        $this->set_user(null);
        return 0;
    }

    /**
     * 存放会员信息
     * @param $user
     * @return void
     */
    protected function set_user($user)
    {
        $this->user = is_null($user) ? [] : $user;
        Session::set($this->user_tag, $user);
    }

    /**
     * 获取会员信息
     * @return array|mixed
     */
    protected function get_user()
    {
        $value = Session::get($this->user_tag);
        return $value ?: [];
    }

    /**
     * 更新会员信息
     * @return void
     * @throws Exception
     */
    protected function update_user_info()
    {
        $user = [];
        if (isset($this->user['admin_id'])) {
            $user = AdminModel::get($this->user['admin_id']);
            $user = (!empty($user) && $user['enable']) ? $user->toArray() : [];
        }
        $this->set_user($user);
    }

    /**
     * 导出数据表
     * @param $name
     * @param $cell_name
     * @param $data
     * @return void
     * @throws Exception
     */
    protected function export_excel($name, $cell_name, $data)
    {
        ExcelTool::instance()->exportExcel($name, $cell_name, $data);
    }

    /**
     * 列表排序处理
     * @param string $field
     * @param string $sort
     * @param array  $sort_field 允许排序的字段
     * @return array
     */
    protected function sort_order($sort_field = [], $field = '', $sort = 'desc')
    {
        $order = input('order', '');
        $dir   = boolval(input('dir', 0));

        if (!empty($order) && in_array($order, $sort_field)) {
            $field = $order;
            $sort  = $dir ? 'asc' : 'desc';
        }

        $this->assign('order', ['field' => $order, 'dir' => $dir]);

        return empty($field) ? [] : [$field => $sort];
    }

    /**
     * 搜索处理
     * @param string $field
     * @param string $description
     * @param bool   $vague
     * @return array
     */
    protected function search($field = '', $description = '', $vague = true)
    {
        if (empty($field)) {
            return [];
        }

        $keyword = input('keyword', '');

        $this->search['keyword']     = $keyword;
        $this->search['description'] = $description;

        $where = [];
        if ($keyword !== '') {
            if ($vague) {
                $where[$field] = ['like', '%' . $keyword . '%'];
            } else {
                $where[$field] = $keyword;
            }
        }
        return $where;
    }

    /**
     * 模板界面输出
     * @param string $template   模板
     * @param array  $base_param 基础参数
     * @return mixed
     */
    protected function fetch_view($template = '', $base_param = [])
    {
        // 当前页面基础地址,仅包含必要参数，去除排序、搜索关键词等
        $current_url = HttpHelper::get_url_query($this->request->baseUrl(), $this->request->only($base_param));
        $this->assign('current_url', $current_url);

        return $this->fetch($template);
    }

    /**
     * 输出前数据处理
     * @return void
     * @throws Exception
     */
    protected function before_assign()
    {
        parent::before_assign();
        // 来源地址
        $this->assign('http_referer', $this->http_referer);
        // 参数数组
        $this->assign('param_array', $this->request->param());

        // 标题
        empty($this->title) AND $this->title = Config::get('public_title');
        $this->assign('title', $this->title);
        $this->assign($this->show);
        $this->assign($this->param);
        empty($this->search) OR $this->assign('search', $this->search);

        if (!$this->no_need_login) {
            $this->assign('user', $this->user);

            if ($this->show['head_nav']) {
                $this->assign('page_title', $this->url_info['title']);
            }

            if ($this->show['left_nav']) {
                //获取有权限的菜单tree
                $menus = AdminRuleModel::auth_menus($this->user['admin_id']);
                //当前方法倒推到顶级菜单ids数组
                $menus_curr = AdminRuleModel::menus_parent_id($this->url_info['id']);

                $key = md5(serialize($menus)) . md5(serialize($menus_curr));

                $menu_list_html = Cache::get($key);
                if (empty($menu_list_html)) {
                    // 生成左侧菜单
                    $menu_list_html = $this->menu_list($menus, $menus_curr);
                    Cache::tag(AdminRuleModel::getCacheTag())->set($key, $menu_list_html);
                }
                $this->assign('menu_list_html', $menu_list_html);
            }
        }
    }

    /**
     * 默认返回链接
     * @return string
     */
    protected function return_url()
    {
        return controller_url();
    }

    /**
     * 递归输出菜单
     * @param array $list
     * @param array $current_menu
     * @param int   $level
     * @return string
     * @throws Exception
     */
    private function menu_list(array $list, array $current_menu, $level = 0)
    {
        if (empty($list)) {
            return '';
        }

        $html = '';
        foreach ($list as $k => $v) {
            $data = [
                'level'      => $level,
                'check_id'   => isset($current_menu[$level]) ? $current_menu[$level] : 0,
                'child_html' => isset($v['_child']) ? $this->menu_list($v['_child'], $current_menu, $level + 1) : '',
                'data_info'  => $v,
            ];
            $html .= $this->view->fetch('piece/menu_list_piece', $data);
        }
        return $html;
    }
}
