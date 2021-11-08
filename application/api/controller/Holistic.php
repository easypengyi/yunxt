<?php

namespace app\api\controller;

use app\common\model\ArticleCenter;
use Exception;
use helper\HttpHelper;
use app\common\controller\ApiController;
use app\common\model\Article as ArticleModel;
use app\common\model\Feedback as FeedbackModel;
use app\common\model\Configure as ConfigureModel;

/**
 * 整体 API
 */
class Holistic extends ApiController
{
    //-------------------------------------------------- H5页面链接

    /**
     * 报单手册接口
     * @return void
     * @throws Exception
     */
    public function register_agreement()
    {
        $list['url'] = HttpHelper::article_url(ConfigureModel::getValue('register_agreement'));
        output_success('', $list);
    }

    /**
     * 使用帮助接口
     * @return void
     * @throws Exception
     */
    public function use_help()
    {
        $list['url'] = HttpHelper::article_url(ConfigureModel::getValue('use_help'));
        output_success('', $list);
    }

    /**
     * 隐私保护接口
     * @return void
     * @throws Exception
     */
    public function private_help()
    {
        $list['url'] = HttpHelper::article_url(ConfigureModel::getValue('private_help'));
        output_success('', $list);
    }

    /**
     * 加入我们接口
     * @return void
     * @throws Exception
     */
    public function join_us()
    {
        $list['url'] = HttpHelper::article_url(ConfigureModel::getValue('join_us'));
        output_success('', $list);
    }


    /**
     * 联系我们接口
     * @return void
     * @throws Exception
     */
    public function contact_us()
    {
        $list['url'] = HttpHelper::article_url(ConfigureModel::getValue('contact_us'));
        output_success('', $list);
    }
    /**
     * 联系我们接口
     * @return void
     * @throws Exception
     */
    public function contact_us1()
    {
        $list['url'] = HttpHelper::article_url(ConfigureModel::getValue('contact_us1'));
        output_success('', $list);
    }
    /**
     * 联系我们接口
     * @return void
     * @throws Exception
     */
    public function contact_us2()
    {
        $list['url'] = HttpHelper::article_url(ConfigureModel::getValue('contact_us2'));
        output_success('', $list);
    }
    /**
     * 联系我们接口
     * @return void
     * @throws Exception
     */
    public function contact_us3()
    {
        $list['url'] = HttpHelper::article_url(ConfigureModel::getValue('contact_us3'));
        output_success('', $list);
    }

    /**
     * 联系我们接口
     * @return void
     * @throws Exception
     */
    public function contact_us4()
    {
        $list['url'] = HttpHelper::article_url(ConfigureModel::getValue('contact_us4'));
        output_success('', $list);
    }



    /**
     * 会员权益接口
     * @throws Exception
     */
    public function leaguer()
    {
        $list['leaguer_intro'] = HttpHelper::article_url(ConfigureModel::getValue('leaguer_intro'));
        $list['leaguer_price'] = ConfigureModel::getValue('leaguer_price');
        output_success('', $list);
    }

    //-------------------------------------------------- 其他

    /**
     * 全站统一客服电话接口
     * @return void
     * @throws Exception
     */
    public function service_phone()
    {
        $list['phone'] = ConfigureModel::getValue('service_phone');
        output_success('', $list);
    }

    /**
     * 发布会员反馈接口
     * @return void
     * @throws Exception
     */
    public function feedback_place()
    {
        $content = $this->get_param('content');
        $image   = $this->get_param('image', '');
        $this->check_login();

        FeedbackModel::insert_feedback($this->member_id, $content, $this->app_type, explode(',', $image));
        output_success();
    }

    /**
     * 文章内容
     * @return void
     * @throws Exception
     */
    public function article()
    {
        $article_id = $this->get_param('article_id');
        $replace    = $this->get_param('replace', false);

        $list = ArticleModel::info($article_id, $replace);
        output_success('', $list);
    }

    /**
     * 文章内容
     * @return void
     * @throws Exception
     */
    public function article_info()
    {
        $article_id = $this->get_param('article_id');

        $list = ArticleCenter::info($article_id);
        output_success('', $list);
    }
}
