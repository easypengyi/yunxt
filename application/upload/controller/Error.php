<?php

namespace app\upload\controller;

use app\common\core\Common;

/**
 * 空控制器处理
 */
class Error extends Common
{
    /**
     * 默认方法
     * @return mixed
     */
    public function index()
    {
        return $this->_empty();
    }

    /**
     * 空操作
     * @return mixed
     */
    public function _empty()
    {
        return response(file_get_contents_no_ssl(PUBLIC_PATH . 'image/no_img.jpg'), 200, ['content-type' => 'image/jpg']);
    }
}
