<?php

namespace app\api\controller;

use Exception;
use app\common\model\News as NewsModel;
use app\common\controller\ApiController;

/**
 * 帖子 API
 */
class News extends ApiController
{
    /**
     * 帖子列表接口
     * 分页
     * @throws Exception
     */
    public function news_list()
    {
        $list = NewsModel::news_list();
        output_success('', $list);
    }

    /**
     * 帖子详情接口
     * @throws Exception
     */
    public function news_detail()
    {
        $news_id = $this->get_param('news_id');

        $detail = NewsModel::detail($news_id);
        empty($detail) AND output_error('数据不存在！');
        output_success('', $detail);
    }
}
