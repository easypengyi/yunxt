<?php

namespace app\common\controller;


use Exception;
use think\Cache;
use think\Log;
use Jssdk;
use think\Request;
use think\Session;
use app\common\Constant;
use app\common\ResultCode;
use app\common\model\LogWeb as LogWebModel;

/**
 * wap前端基础类
 */
abstract class MobileController extends WebController
{
    protected $app_type = Constant::CLIENT_WAP;

    protected $login_url = 'Login/index';

    protected $is_weixin = false;


    /**
     * 初始化方法
     * @return void
     * @throws Exception
     */
    protected function _initialize()
    {
        parent::_initialize();
        // 来源地址 处理
        if (!$this->check_referer()) {
            $this->http_referer = '';
        }
        $controller = Request::instance()->controller();
        $action = Request::instance()->action();
        switch ($controller == 'Product' && $action == 'detail'){
            case true:
                $this->login_url = 'Login/oauth_wx';
                break;
            default:
                $this->login_url = 'Login/index';
        }
        $this->wxshare();
        $this->referer_self AND $this->http_referer = '';
    }


    protected function wxshare()
    {
        $isMobile = Request::instance()->isMobile();
        $ip = $_SERVER["REMOTE_ADDR"];
        if ($isMobile && $ip!='127.0.0.1'){
            $jssdk = new Jssdk();
            $res = $jssdk->getSignPackage();
            $appId = $res['appId'];
            $timestamp = $res['timestamp'];
            $nonceStr = $res['nonceStr'];
            $signature = $res['signature'];
            $url =  $this->request->domain().$this->request->url();
            return $this->assign(
                array(
                    'appId' => $appId,
                    'timestamp' => $timestamp,
                    'nonceStr'  => $nonceStr,
                    'signature' => $signature,
                    'url'       =>$url,
                    'imgUrl'    =>'https://ydn-product.oss-cn-hangzhou.aliyuncs.com/upload/image/wxshare.jpg',
                    'title'     =>'赋活NMN',
                    'desc'      =>'携手雅典娜，人生更精彩!'
                )
            );
        }
    }





    /**
     * 操作者信息
     * @return array
     */
    public function operator_info()
    {
        if (empty($this->member)) {
            return ['operator_id' => 0, 'type' => LogWebModel::TYPE_NO];
        }

        return ['operator_id' => $this->member['member_id'], 'type' => LogWebModel::TYPE_MEMBER];
    }

    /**
     * 获取token
     * @return mixed
     */
    protected function get_token()
    {
        $token = input('token', null);
        if (is_null($token)) {
            $token = Session::get($this->token_tag);
        } else {
            $this->set_token($token);
        }
        return $this->token = empty($token) ? '' : $token;
    }

    protected function set_wx_login($url){
         Session::set($this->wx_login,$url);
    }

    protected function get_wx_login(){
      return  Session::get($this->wx_login);
    }



    /**
     * 更新会员信息
     * @return void
     * @throws Exception
     */
    protected function update_member()
    {
        if (empty($this->token)) {
            return;
        }

        $time = Session::get('member_info_update_time');
        if (time() - $time < 5) {
            return;
        }
        $result = $this->api('member', 'info', [], false);
        if (ResultCode::RES_SUCCESS === $result['code']) {
            $this->set_member($result['data']['member']);
            Session::set('member_info_update_time', time());
            return;
        }

        $this->logout();
    }

    /**
     * 清理更新缓存时间
     * @return void
     */
    protected function clear_update_member_time()
    {
        Session::set('member_info_update_time', 0);
    }

    /**
     * 输出前数据处理
     * @return void
     */
    protected function before_assign()
    {
        parent::before_assign();
        $this->assign('is_weixin', $this->is_weixin);
    }

    /**
     * 接口返回数据处理
     * @param $result
     * @return void
     */
    protected function api_handler($result)
    {
        switch (intval($result['code'])) {
            case ResultCode::RES_PARAMETER_ERR:
                if ($this->is_ajax) {
                    $this->error($result['msg']);
                } else {
                    Log::error($result);
                    $this->redirect(folder_url('Index/index'));
                }
                break;
            default:
                parent::api_handler($result);
                break;
        }
    }
}
