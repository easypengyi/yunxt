<?php

namespace app\admin\controller;

use app\common\model\MemberGroupRelation;
use app\common\model\Member as MemberModel;
use app\common\model\OrderShop;
use app\common\model\OrdersShop;
use Exception;
use think\Lang;
use think\Config;
use think\captcha\Captcha;
use app\common\model\Admin as AdminModel;
use app\common\controller\AdminController;
use think\Db;
use app\common\model\OrderShop as OrderShopModel;
use app\admin\controller\OrderMiniApp;
/**
 * 登录 模块
 */
class Login extends AdminController
{
    // 不进行登录验证
    protected $no_need_login = true;
    // 验证标识
    private $verify_id = 'aid';

    /**
     * 初始化方法
     * @return void
     * @throws Exception
     */
    public function _initialize()
    {
        parent::_initialize();
        $this->show = ['navigation_bar' => false, 'left_nav' => false, 'head_nav' => false];
        // 是否需要验证码
        $this->param['verify'] = Config::get('login_verify');
    }

    /**
     * 登录显示
     * @return mixed
     * @throws Exception
     */
    public function index()
    {
        if ($this->is_ajax) {
            if ($this->param['verify']) {
                $this->verify_check($this->verify_id);
            }
            $username = trim(input('username', ''));
            $password = trim(input('pwd', ''));

            $user = AdminModel::login($username, $password);
            empty($user) AND $this->error('用户名或密码错误！');
            $user->getAttr('enable') OR $this->error('已被禁用！');
            $this->set_user($user->toArray());
            $this->success('登录成功！', folder_url());
        }

        $this->check_login() AND $this->redirect(folder_url());

        $this->title = Config::get('login_tile');
        $this->assign('name', Config::get('name'));
        return $this->fetch_view();
    }

    /**
     * 验证码
     * @return mixed
     * @throws Exception
     */
    public function verify()
    {
        if ($this->check_login()) {
            return '';
        }
        return $this->verify_build($this->verify_id);
    }

    /**
     * 退出登录
     * @return void
     * @throws Exception
     */
    public function logout()
    {
        $this->set_user(null);
        $this->redirect($this->return_url());
    }

    /**
     * 默认返回链接
     * @return string
     */
    protected function return_url()
    {
        return controller_url('login');
    }

    /**
     * 验证码生成
     * @param $id
     * @return mixed
     * @throws Exception
     */
    private function verify_build($id)
    {
        ob_end_clean();
        $verify = new Captcha(Config::get('verify'));
        return $verify->entry($id);
    }

    /**
     * 验证码验证
     * @param $id
     * @return void
     * @throws Exception
     */
    private function verify_check($id)
    {
        $verify = new Captcha();
        if (!$verify->check(input('verify', ''), $id)) {
            $this->error(Lang::get('verifiy incorrect'), $this->return_url());
        }
    }


    /**
     * 代理升级执行董事
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function level_up()
    {
        $where['group_id'] = ['eq', MemberGroupRelation::four];

        $list = MemberGroupRelation::where($where)->select()->toArray();

        $where1['group_id']   = ['neq',MemberGroupRelation::seven];
        $array =  MemberGroupRelation::where($where1)->select()->toArray();


        foreach ($list as $k=>&$val){
            $val['count'] = 0;
            $val['ary'] = [];
            $val['group_count'] = 0;
            foreach ($array as $kk => $vv){
                if ($val['member_id'] == $vv['top_id']){
                    $val['count'] ++;
                    array_push($val['ary'],$vv);
                }
            }
            if ($val['count'] < 3){
                unset($list[$k]);
            }else{
                foreach ($val['ary'] as $kkk=>$vvv){
                    if ($val['group_id'] == $vvv['group_id']){
                        $val['group_count']++;
                    }else{
                        $newArray  =  self::get_downline($array,$vvv['member_id']);
                        foreach ($newArray as $kkkk =>$vvvv){
                            if ($val['group_id']  == $vvvv['group_id'] ){
                                $val['group_count']++;
                                break;
                            }
                        }
                    }
                }
            }

            if ($val['group_count'] < 3){
                unset($list[$k]);
            }

        }


        foreach ($list as $k=>&$val){
            $where_ary = [];
            $ary = self::get_downline($array,$val['member_id']);
            foreach ($ary as $kk=>$vv){
                array_push($where_ary,$vv['member_id']);
            }

            $wheres['member_id'] = ['in',$where_ary];
            $wheres['status'] = ['gt',OrdersShop::STATUS_WAIT_PAY];

            $num = OrdersShop::where($wheres)->sum('product_num');//体系消费
            $my_num = OrdersShop::where('member_id', $val['member_id'])
                ->where('status', OrdersShop::STATUS_WAIT_PAY)->sum('product_num');//个人消费
            $bd_num = OrderShop::where('member_id', $val['member_id'])->where('status', 6)->sum('product_num');//报单数量


            if ($num >= 100 || ($my_num + $bd_num) >= 140){
                MemberGroupRelation::where(['member_id'=>$val['member_id']])->setInc('group_id');
            }
        }
        echo 'success';die;
    }
    /**
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * 执行董事升级全球合伙人
     */
    public static function level_up1()
    {
        $where['group_id'] = ['eq', MemberGroupRelation::three];
        $list = MemberGroupRelation::where($where)->select()->toArray();

        $where1['group_id']   = ['neq',MemberGroupRelation::seven];
        $array =  MemberGroupRelation::where($where1)->select()->toArray();


        foreach ($list as $k=>&$val){
            $val['count'] = 0;
            $val['ary'] = [];
            $val['group_count'] = 0;
            foreach ($array as $kk => $vv){
                if ($val['member_id'] == $vv['top_id']){
                    $val['count'] ++;
                    array_push($val['ary'],$vv);
                }
            }
            if ($val['count'] < 3){
                unset($list[$k]);
            }else{
                foreach ($val['ary'] as $kkk=>$vvv){
                    if ($val['group_id'] == $vvv['group_id']){
                        $val['group_count']++;
                    }else{
                        $newArray  =  self::get_downline($array,$vvv['member_id']);
                        foreach ($newArray as $kkkk =>$vvvv){
                            if ($val['group_id']  == $vvvv['group_id'] ){
                                $val['group_count']++;
                                break;
                            }
                        }
                    }
                }

            }

            if ($val['group_count'] < 3){
                unset($list[$k]);
            }


        }



        foreach ($list as $k=>&$val){
            $where_ary = [];
            $ary = self::get_downline($array,$val['member_id']);
            foreach ($ary as $kk=>$vv){
                array_push($where_ary,$vv['member_id']);
            }

            $wheres['member_id'] = ['in',$where_ary];
            $wheres['status'] = ['gt',OrdersShop::STATUS_WAIT_PAY];

            $num = OrdersShop::where($wheres)->sum('product_num');
            $my_num = OrdersShop::where('member_id', $val['member_id'])
                ->where('status', OrdersShop::STATUS_WAIT_PAY)->sum('product_num');//个人消费
            $bd_num = OrderShop::where('member_id', $val['member_id'])->where('status', 6)->sum('product_num');//报单数量

            if ($num >= 300 || ($my_num + $bd_num) >= 300){
                MemberGroupRelation::where(['member_id'=>$val['member_id']])->setInc('group_id');
            }
        }

        echo 'success';die;
    }
    /**
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * 全球合伙人升级联合创始人
     */
    public static function level_up2()
    {
        $where['group_id'] = ['eq', MemberGroupRelation::second];

        $list = MemberGroupRelation::where($where)->select()->toArray();

        $where1['group_id']   = ['neq',MemberGroupRelation::seven];
        $array =  MemberGroupRelation::where($where1)->select()->toArray();

        foreach ($list as $k=>&$val){
            $val['count'] = 0;
            $val['ary'] = [];
            $val['group_count'] = 0;
            foreach ($array as $kk => $vv){
                if ($val['member_id'] == $vv['top_id']){
                    $val['count'] ++;
                    array_push($val['ary'],$vv);
                }
            }
            if ($val['count'] < 3){
                unset($list[$k]);
            }else{
                foreach ($val['ary'] as $kkk=>$vvv){
                    if ($val['group_id'] == $vvv['group_id']){
                        $val['group_count']++;
                    }else{
                        $newArray  =  self::get_downline($array,$vvv['member_id']);
                        foreach ($newArray as $kkkk =>$vvvv){
                            if ($val['group_id']  == $vvvv['group_id'] ){
                                $val['group_count']++;
                                break;
                            }
                        }
                    }
                }

            }

            if ($val['group_count'] < 3){
                unset($list[$k]);
            }
        }


        foreach ($list as $k=>&$val){
            $where_ary = [];
            $ary = self::get_downline($array,$val['member_id']);
            foreach ($ary as $kk=>$vv){
                array_push($where_ary,$vv['member_id']);
            }

            $wheres['member_id'] = ['in',$where_ary];
            $wheres['status'] = ['gt',OrdersShop::STATUS_WAIT_PAY];

            $num = OrdersShop::where($wheres)->sum('product_num');
            $my_num = OrdersShop::where('member_id', $val['member_id'])
                ->where('status', OrdersShop::STATUS_WAIT_PAY)->sum('product_num');//个人消费

            $bd_num = OrderShop::where('member_id', $val['member_id'])->where('status', 6)->sum('product_num');//报单数量

            if ($num >= 700 || ($my_num + $bd_num) >= 700){
                MemberGroupRelation::where(['member_id'=>$val['member_id']])->setInc('group_id');
            }
        }

        echo 'success';die;
    }


    public static function  get_downline($members,$mid,$level=0){
        $arr = array();
        foreach ($members as $key => $v) {
            if($v['top_id']==$mid){  //pid为0的是顶级分类
                $v['level'] = $level+1;
                $arr[]=$v;
                $arr = array_merge($arr, self::get_downline($members,$v['member_id'],$level+1));
            }
        }
        return $arr;
    }

    /**
     * @description: 获得花生小程序的 token
     * @param {*}
     * @return {*}
     */
    public static function getMiniAppToken(){
        $currentFilePath=dirname(__FILE__);
        $tokenFilePath = $currentFilePath ."/../../../runtime/temp/hs97866.token.json";
        $flag_refreshToken = true;
        $token="";
        if(file_exists($tokenFilePath)){
            // $a=Config::get('hs97866.appId');
            $token_file = fopen($tokenFilePath, "r") or die("Unable to open file!");
            $data = json_decode(fgets($token_file));
            fclose($token_file);

            if(time()<$data->expires){
                $flag_refreshToken = false;
                $token = $data->token;
            }
        }

        if($flag_refreshToken){

            $appId = Config::get('hs97866.appId');
            $secret = Config::get('hs97866.secret');
            $wxId = Config::get('hs97866.wxId');

            $ch = curl_init(); //初始化一个CURL对象
            curl_setopt($ch, CURLOPT_URL, "http://$wxId.97866.com/api/token/grant.json?appid=$appId&secret=$secret");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            $data = json_decode(curl_exec($ch));
            if($data->access_token){
                $token_file = fopen($tokenFilePath,"w") or die("Unable to open file!");
                fwrite($token_file,json_encode([
                    'token'=>$data->access_token,
                    'expires'=>time()+$data->expires_in
                ]));
                fclose($token_file);
                $token = $data->access_token;
            }else{
                echo $data->errmsg;
            }
            curl_close($ch);

        }

        return $token;
    }

    /**
     * @description: 从花生小程序同步订单
     * @param {*}
     * @return {*}
     */
    public static function syncOrderFromMiniProgram()
    {

        $end_time = time();
        $start_time = $end_time - 7*24*3600;//单次最多是 7 天
        Login::syncOrderFromMiniProgramByTime($start_time,$end_time);

        $end_time = $start_time;
        $start_time = $end_time - 7*24*3600;
        Login::syncOrderFromMiniProgramByTime($start_time,$end_time);

        $end_time = $start_time;
        $start_time = $end_time - 7*24*3600;
        Login::syncOrderFromMiniProgramByTime($start_time,$end_time);

        $end_time = $start_time;
        $start_time = $end_time - 7*24*3600;
        Login::syncOrderFromMiniProgramByTime($start_time,$end_time);
    }

    public static function syncOrderFromMiniProgramByTime($start_time,$end_time)
    {


        $token = Login::getMiniAppToken();
        $wxId = Config::get('hs97866.wxId');
        $content = file_get_contents("http://$wxId.97866.com/api/mag.admin.order.list.json?access_token=$token&start_time=$start_time&end_time=$end_time");
        $data = json_decode($content);
        if($data->errcode==0){
            $orders = $data->orders;
            $start_time = $data->start_time;
            $end_time = $data->end_time;

            if(count($orders)>0){
                foreach($orders as $item){
                    $order_no = $item->order_no;
                    $row = Db::name('orders_mini_app')->where('order_sn', $order_no)->find();

                    //花生小程序订单状态：订单状态，1 - 待付款 2 - 待发货 3 - 已发货 4 - 交易完成 5 - 申请退款 6 - 退款成功 7 - 主动关闭 8 - 自动关闭 10 - 待成团
                    //本系统订单状态
                    /*
                    // 状态
                    // 状态-无
                    const STATUS_NO = 0;
                    // 状态-已失效
                    const STATUS_INVALID = 1;
                    // 状态-待支付
                    const STATUS_WAIT_PAY = 2;
                    // 状态-待配送 已付款
                    const STATUS_ALREADY_PAY = 3;
                    // 状态-配送中
                    const STATUS_ALREADY_DISTRIBUTION = 4;
                    // 状态-检测中
                    const STATUS_CHECKING = 5;
                    // 状态-已完成
                    const STATUS_FINISH = 6;
                    // 状态-已评价
                    const STATUS_EVALUATE = 7;
                    */

                    $status=OrderShopModel::STATUS_NO;
                    $refund_status = OrderShopModel::REFUND_STATUS_NO;
                    switch($item->status){
                        case 1:
                            $status=OrderShopModel::STATUS_WAIT_PAY;
                            break;
                        case 2:
                            $status=OrderShopModel::STATUS_ALREADY_PAY;
                            break;
                        case 3:
                            $status=OrderShopModel::STATUS_ALREADY_DISTRIBUTION;
                            break;
                        case 4:
                            $status=OrderShopModel::STATUS_FINISH;
                            break;
                        case 5:
                            $refund_status=OrderShopModel::REFUND_STATUS_APPLY;
                            break;
                        case 6:
                            $refund_status=OrderShopModel::REFUND_STATUS_SUCCESS;
                            break;
                        case 7:
                            $status=OrderShopModel::STATUS_INVALID;
                            break;
                        case 8:
                            $status=OrderShopModel::STATUS_INVALID;
                            break;
                    }

                    $rowData=[
                        'order_id'=>$item->id,
                        'order_sn'=>$item->order_no,
                        'transaction_sn'=>$item->wx_transaction_id,
                        'distribution_id'=>1,//发货方式，待确认
                        'courier_sn'=>isset($item->logistics[0])?$item->logistics[0]->no:'',
                        'order_type'=>1,
                        'payment_id'=>2,//微信支付
                        'member_id'=>0,//用户 id
                        'money'=>$item->total_fee,
                        'amount'=>$item->amount,
                        'status'=>$status,
                        'refund_status'=>$refund_status,
                        // a:9:{s:9:"consignee";s:9:"吴明确";s:6:"mobile";s:11:"13689589228";s:7:"address";s:106:"园山街道大康村陈屋路8号保成泰工业园'A栋401 明福鑫家具电器有限公司  吴国通8";s:8:"province";s:6:"广东";s:11:"province_id";i:6;s:4:"city";s:6:"深圳";s:7:"city_id";i:77;s:8:"district";s:9:"龙岗区";s:11:"district_id";i:709;}
                        'address'=>serialize([
                            'consignee'=>$item->receiver->receiver_name,
                            'mobile'=>$item->receiver->receiver_phone,
                            'province'=>$item->receiver->receiver_state,
                            'city'=>$item->receiver->receiver_city,
                            'district'=>$item->receiver->receiver_district,
                            'address'=>$item->receiver->receiver_address,
                            'buyer'=>$item->buyer,
                        ]),
                        'product_id'=>$item->items[0]->post_id,
                        'product_name'=>$item->items[0]->title,
                        'original_unit_price'=>$item->items[0]->original_price,
                        'order_time'=>$item->time,
                        'payment_time'=>$item->paytime,
                        'delivery_time'=>0,//发货时间
                        'finish_time'=>0,
                        'product_num'=>$item->items[0]->quantity,
                        'refund_finish_time'=>$item->refund->refund_time,
                    ];

                    $flag_pay_commission=false;
                    $flag_insert=false;

                    if($row){


                        if($status != $row['status'] || $refund_status != $row['refund_status']){
                            $flag_insert=true;
                            if($item->status==4 && $row->status!=OrderShopModel::STATUS_FINISH){
                                $flag_pay_commission=true;
                            }
                            Db::name('orders_mini_app')->where('order_sn',$order_no)->delete();
                        }
                    }
                    else{
                        if($item->status==4){
                            $flag_pay_commission=true;
                        }
                        $flag_insert=true;

                    }

                    if($flag_insert){
                        //需要支付佣金
                        if($flag_pay_commission){
                            $rowData['pay_commission']=1;
                            $rowData['pay_commission_time']=time();
                        }
                        Db::name('orders_mini_app')->insert($rowData);
                        //需要支付佣金
                        if($flag_pay_commission){

                            OrderMiniApp::reward($item->id);
                        }
                    }


                }
            }

        }
    }
}
