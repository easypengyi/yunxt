<?php

namespace app\mobile\controller;

use app\common\model\ArticleCenter;
use app\common\model\Banner as BannerModel;
use Exception;
use app\common\controller\MobileController;


/**
 * 关于我们
 */
class Help extends MobileController
{

    /**
     * @return mixed
     * @throws Exception
     */
    public function video()
    {
        $result = $this->api('Banner', 'banner', ['banner_type' => BannerModel::TYPE_WEB_HOME]);
        $banner = [];
        foreach ($result['data']['list'] as $v) {
            switch ($v['skip']) {
                case BannerModel::SKIP_IMAGE:
                    $url = '';
                    break;
                case BannerModel::SKIP_URL:
                    $url = $v['url'];
                    break;
                case BannerModel::SKIP_PRODUCT:
                    $url = folder_url('Product/detail', ['product_id' => $v['content']]);
                    break;
                default:
                    $url = '';
                    break;
            }
            $banner[$v['id']] = ['url' => $url, 'image' => $v['image']];
        }
        $this->assign('banner', $banner);
        $this->assign('return_url', folder_url('Index/index'));
        $this->assign('title', '视频中心');
        return $this->fetch('index');
    }


    public function source(){
        $this->assign('title', '素材中心');
        $this->assign('return_url', folder_url('Index/index'));
        return $this->fetch('');
    }

    public function article(){
        $this->assign('title', '商学院');
        $this->assign('return_url', folder_url('Index/index'));
        return $this->fetch('');
    }


    /**
     * 帮助中心
     * @return mixed
     * @throws Exception
     */
    public function help()
    {
        $result = $this->api('Holistic', 'use_help');
        return $this->detail('帮助中心', $result['data']['url'], 1);
    }

    /**
     * 报单手册
     * @return mixed
     * @throws Exception
     */
    public function agreement(){
        $result = $this->api('Holistic', 'register_agreement');
        return $this->detail('报单手册', $result['data']['url'], 1);
    }


    public function article_detail(){
        $result = $this->api('Holistic', 'article_info');
        $this->assign('title', '文章详情');
        $this->assign('return_url', folder_url('Help/source'));
        $this->assign('detail_url', $result['data']['detail_url']);
        return $this->fetch('');
    }



    /**
     * 服务协议
     * @return mixed
     * @throws Exception
     */
    public function services()
    {
        $result = $this->api('Holistic', 'register_agreement');
        return $this->detail('服务协议', $result['data']['url'], 3);
    }



    /**
     * 企业介绍
     * @return mixed
     * @throws Exception
     */
    public function contact()
    {
        $this->assign('title', '平台介绍');
        $this->assign('return_url', folder_url('Index/index'));
        return $this->fetch('');
    }



    /**
     * 默认页面
     * @param $title
     * @param $detail_url
     * @param $active
     * @return mixed
     */
    public function detail($title, $detail_url, $active)
    {
        $this->assign('title', $title);
        $this->assign('active', $active);
        $this->assign('detail_url', $detail_url);
        $this->assign('return_url', $this->http_referer ?:folder_url('Index/index'));
        return $this->fetch('detail');
    }
}
