<?php

namespace app\mobile\controller;

use Exception;
use app\common\controller\MobileController;
use app\common\model\Banner as BannerModel;
use think\Log;

/**
 * 首页
 */
class Index extends MobileController
{
    /**
     * 首页
     * @return mixed
     * @throws Exception
     */
    public function index()
    {
        $result = $this->api('Message', 'message_no_read_number');
        $this->assign('no_read_number', $result['data']['no_read_number']);

        // banner
        $result = $this->api('Banner', 'banner', ['banner_type' => BannerModel::TYPE_WAP_HOME]);
        $banner = [];
        foreach ($result['data']['list'] as $v) {
            switch ($v['skip']) {
                case BannerModel::SKIP_IMAGE:
                    $url = '#';
                    break;
                case BannerModel::SKIP_URL:
                    $url = $v['url'];
                    break;
                case BannerModel::SKIP_PRODUCT:
                    $url = folder_url('Product/detail', ['product_id' => $v['content']]);
                    break;
                default:
                    $url = '#';
                    break;
            }
            $banner[$v['id']] = ['url' => $url, 'image' => $v['image']];
        }


        // banner
        $result = $this->api('Banner', 'banner', ['banner_type' => BannerModel::TYPE_WAP_PRODUCT]);
        $banner1 = [];
        foreach ($result['data']['list'] as $v) {
            switch ($v['skip']) {
                case BannerModel::SKIP_IMAGE:
                    $url = '#';
                    break;
                case BannerModel::SKIP_URL:
                    $url = $v['url'];
                    break;
                case BannerModel::SKIP_PRODUCT:
                    $url = folder_url('Product/detail', ['product_id' => $v['content']]);
                    break;
                default:
                    $url = '#';
                    break;
            }
            $banner1[$v['id']] = ['url' => $url, 'image' => $v['image']];
        }


        $announcement   =  $this->api('Banner', 'announcement');
        $this->assign('banner', $banner);
        $this->assign('banner1', $banner1);
        $this->assign('announcement', $announcement['data']['list']);
        $this->assign('return_url', $this->http_referer ?: folder_url());
        $this->assign('title', '赋活NMN');
        return $this->fetch();
    }
}
