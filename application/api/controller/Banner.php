<?php

namespace app\api\controller;

use app\common\model\Announcement as AnnouncementModel;
use app\common\model\ArticleCenter;
use app\common\model\Video as VideoModel;
use Exception;
use app\common\controller\ApiController;
use app\common\model\Banner as BannerModel;

/**
 * 广告 API
 */
class Banner extends ApiController
{
    /**
     * banner接口
     * @return void
     * @throws Exception
     */
    public function banner()
    {
        $banner_type = $this->get_param('banner_type', BannerModel::TYPE_WAP_HOME);

        $list = BannerModel::banner_list($banner_type);
        output_success('', ['list' => $list]);
    }

    /**
     * @throws \think\exception\DbException
     */
    public function announcement()
    {
        $list = AnnouncementModel::data_list();
        output_success('', ['list' => $list]);
    }

    /**
     * @throws \think\exception\DbException
     */
    public function source()
    {
        $type = $this->get_param('type', ArticleCenter::TYPE_ONE);
        $list = ArticleCenter::article_list($type);
        output_success('', ['list' => $list]);
    }

    public function article()
    {
        $type = $this->get_param('type', ArticleCenter::TYPE_TWO);
        $list = ArticleCenter::article_list($type);
        output_success('', ['list' => $list]);
    }


    /**
     * banner点击接口
     * @return void
     * @throws Exception
     */
    public function banner_click()
    {
        $id = $this->get_param('id');

        BannerModel::banner_click($id);
        output_success('');
    }

    /**
     * 视频列表
     * @throws \think\exception\DbException
     */
    public function video_list()
    {
        $category = $this->get_param('category',1);
        $list = VideoModel::video_list($category);
        output_success('', ['list' => $list]);
    }
}
