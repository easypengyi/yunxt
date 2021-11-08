<?php

namespace app\tool\controller;

use Exception;
use app\common\core\Common;
use app\common\model\Article as ArticleModel;

/**
 * 文章内容
 */
class Article extends Common
{
    public function _initialize()
    {
        parent::_initialize();
    }

    /**
     * 默认方法
     * @param int $id
     * @return mixed
     * @throws Exception
     */
    public function index($id = 0)
    {
        $model = ArticleModel::get($id);

        if (empty($model)) {
            return '';
        }

        $content = $model->getAttr('content');

        if (boolval(input('only', 0))) {
            return $this->display($content);
        }

        $this->assign('content', $content);
        $this->assign('title', '');
        return $this->fetch();
    }
}
