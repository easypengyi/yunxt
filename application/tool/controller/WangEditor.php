<?php

namespace app\tool\controller;

use Exception;
use tool\UploadTool;
use app\common\core\Common;
use app\common\model\Article as ArticleModel;

/**
 * wangEditor编辑器处理
 */
class WangEditor extends Common
{
    /**
     * 初始化方法
     */
    public function _initialize()
    {
        parent::_initialize();
        if (!$this->check_referer()) {
            $this->error('无访问权限！');
        }
    }

    /**
     * 上传信息
     * @return mixed
     * @throws Exception
     */
    public function upload()
    {
        UploadTool::instance()->setMaxNumber(input('uploadImgMaxLength', 0));
        UploadTool::instance()->setSize(input('uploadImgMaxSize', 0));
        $result = UploadTool::instance()->upload_thumb(false);
        return json_encode($result);
    }

    /**
     * 加载数据
     * @throws Exception
     */
    public function load()
    {
        $this->is_ajax OR $this->error('请求错误！');

        $article_id = input('article_id', 0);
        $article    = ArticleModel::get($article_id);
        empty($article) AND $this->error('数据不存在！');
        $this->success_result($article);
    }

    /**
     * 保存文章
     * @throws Exception
     */
    public function save()
    {
        $this->is_ajax OR $this->error('请求错误！');

        $article_id = input('article_id/a', []);
        $file_data  = input('file_data/a', []);

        $ids = [];
        foreach ($article_id as $k => $v) {
            $ids[$k] = $this->edit_article($v, input($k, ''), isset($file_data[$k]) ? $file_data[$k] : []);
        }

        $this->success_result($ids);
    }

    /**
     * 文章内容处理
     * @param $article_id
     * @param $content
     * @param $file
     * @return mixed
     * @throws Exception
     */
    private function edit_article($article_id, $content, $file)
    {
        $data['file_ids'] = [];
        if (!empty($file)) {
            foreach ($file as $v) {
                if (!in_array($v['file_id'], $data['file_ids'])) {
                    if (strpos($content, $v['url']) !== false) {
                        $data['file_ids'][] = $v['file_id'];
                    }
                }
            }
        }

        $data['content'] = $content;

        $article = ArticleModel::get($article_id);
        if (empty($article)) {
            $article = ArticleModel::create($data);
        } else {
            $article->save($data);
        }

        return $article->getAttr('article_id');
    }
}
