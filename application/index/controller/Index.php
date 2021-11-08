<?php

namespace app\index\controller;

use app\common\core\Common;
use app\common\model\AdminRule;
use app\common\model\AdminGroup;
use app\common\model\MemberGroup;

/**
 * 默认控制器
 */
class Index extends Common
{
    /**
     *
     */
    public function index()
    {
    }

    /**
     * 缓存清理
     * @return string
     */
    public function clear()
    {
        MemberGroup::cacheClear();
        AdminRule::cacheClear();
        AdminGroup::cacheClear();
        return '缓存清理完成！';
    }
}
