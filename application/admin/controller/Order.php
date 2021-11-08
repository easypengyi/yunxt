<?php

namespace app\admin\controller;

use app\common\model\Member as MemberModel;
use app\common\model\MemberGroupRelation;
use app\common\model\Product as ProductModel;
use app\common\model\MemberGroupRelation as MemberGroupRelationModel;
use app\common\model\MemberGroup as MemberGroupModel;

use app\common\model\Reword;
use helper\ValidateHelper;
use think\Db;
use app\common\model\Member;
use app\common\model\MemberCommission;
use Exception;
use helper\StrHelper;
use helper\TimeHelper;
use app\common\controller\AdminController;
use app\common\model\OrderShop as OrderShopModel;

/**
 * 中心授权订单 模块
 */
class Order extends AdminController
{
    /**
     * 初始化方法
     * @return void
     * @throws Exception
     */
    public function _initialize()
    {
        parent::_initialize();
        $this->param['payment']       = [0 => '未支付', 2 => '微信', 4 => '余额'];
        $this->param['status']        = OrderShopModel::order_status_array();
        $this->param['refund_status'] = OrderShopModel::order_refund_status_array();
    }

    /**
     * 列表
     * @return mixed
     * @throws Exception
     */
    public function index()
    {
        $where = $this->search('order_sn', '输入需查询的订单号');

        $this->search['date']   = input('date', '');
        $this->search['status'] = input('status', '');

        $range_time = TimeHelper::range_time($this->search['date']);
        empty($range_time) OR $where['order_time'] = ['between', $range_time];

        $where['status'] = ['not in', [OrderShopModel::STATUS_INVALID]];
        empty($this->search['status']) OR $where['status'] = $this->search['status'];

        $where['del']        = false;

        $order = $this->sort_order(OrderShopModel::getTableFields(), 'order_time', 'desc');

//        $this->export($where, $order);

        $list = OrderShopModel::page_list($where, $order);
        if (!$list->isEmpty()) {
            $list->load(['Member']);
            $list->append(['top_name']);
        }

        $this->assign('total_money', OrderShopModel::where($where)->sum('amount'));
        $this->assign($list->toArray());
        return $this->fetch_view();
    }

    /**
     * 待发货订单列表
     * @return mixed
     * @throws Exception
     */
    public function delivery_index()
    {
        $where = $this->search('order_sn', '输入需查询的订单号');

        $where['del']           = false;
        $where['status']        = OrderShopModel::STATUS_ALREADY_PAY;
        $where['refund_status'] = ['not in', [OrderShopModel::REFUND_STATUS_APPLY]];

        $order = $this->sort_order(OrderShopModel::getTableFields(), 'order_time', 'desc');

//        $this->export($where, $order);

        $list = OrderShopModel::page_list($where, $order);
        if (!$list->isEmpty()) {
            $list->load(['Member']);
        }

        $this->assign($list->toArray());
        return $this->fetch_view();
    }

    /**
     * 发货操作
     * @param $id
     * @throws Exception
     */
    public function delivery_examine($id)
    {
        $this->is_ajax OR $this->error('请求错误！');

        $where['order_id']      = $id;
        $where['del']           = false;
        $where['status']        = OrderShopModel::STATUS_ALREADY_PAY;
        $where['refund_status'] = ['not in', [OrderShopModel::REFUND_STATUS_APPLY]];

        $data_info = OrderShopModel::get($where);
        empty($data_info) AND $this->error('数据不存在！');

        $data   = [
            'refund_status'   => OrderShopModel::REFUND_STATUS_NO,
            'status'          => OrderShopModel::STATUS_FINISH,
            'courier_sn'      => input('courier_sn', ''),
            'distribution_id' => input('distribution_id', 0),
            'finish_time'   => time(),
        ];

        try {
            Db::startTrans();
            $data_info->save($data);
            self::add($data_info,$data_info['product_id'],$data_info['product_num']);
            Db::commit();
        } catch (Exception $e) {
            Db::rollback();
            $this->error('确认完成失败！');
        }

        try {
            Db::startTrans();
            self::reward($id);
            Db::commit();
        } catch (Exception $e) {
            Db::rollback();
            $this->error('确认完成失败！');
        }

        $this->success('确认完成！', $this->http_referer ?: $this->return_url());
    }


    /**
     * 添加
     * @return mixed
     * @throws Exception
     */
    public function add($data_info,$product_id,$product_num)
    {
        if ($this->is_ajax) {
            $data  = $this->edit_data($data_info);
            $group_id = $this->edit_group_data($product_id);
            $member = MemberModel::create($data);
            $this->edit_group($member['member_id'],$data_info, $group_id);
            MemberModel::balance_inc($member->getAttr('member_id'),$product_num);
            $this->cache_clear();
        }
    }

    /**
     * 数据处理
     * @param MemberModel $data_info
     * @return mixed
     * @throws Exception
     */
    private function edit_data($data_info = null)
    {


        $data['uid']    =  $data_info['uid'];
        $data['member_tel'] =  $data_info['mobile'];
        ValidateHelper::is_mobile($data['member_tel']) OR $this->error('手机号格式错误！');

        $data['member_realname'] =  $data_info['nick_name'];
        empty($data['member_realname']) AND $this->error('请设置用户名称！');
        $data['member_nickname'] = $data_info['nick_name'];



        $data['member_pwd'] = trim(input('member_pwd', '123456'));
        if (!empty($data['member_pwd'])) {
            $data['member_pwd'] = md5($data['member_pwd']);
        }

        if (is_null($data_info)) {
            $result = MemberModel::check_phone($data['member_tel']);
            $result AND $this->error('手机号码已经注册！');

            $data['enable'] = boolval(input('enable', true));
        } else {
            if ($data_info->getAttr('mobile') == $data['member_tel']) {
                $result = MemberModel::check_phone($data['member_tel']);
                $result AND $this->error('手机号码已经注册！');
            }
            if (empty($data['member_pwd'])) {
                unset($data['member_pwd']);
            }
        }

        return $data;
    }

    /**
     * 用户组数据处理
     * @return array
     */
    private function edit_group_data($product_id)
    {
        $group_id = input('group_id/a', [$product_id]);
        $group_id = array_filter($group_id);
        empty($group_id) AND $this->error('请选择用户组！');

        return $group_id;
    }


    /**
     * 用户组编辑
     * @param MemberModel $data_info
     * @param array       $group_id
     * @return bool
     * @throws Exception
     */
    private function edit_group($member_id,$data_info, $group_id)
    {
        MemberGroupRelationModel::bind_group($group_id, $member_id);

        if ($data_info->getAttr('two_id')){
            $top_id = $data_info->getAttr('two_id');
        }else{
            $top_id = $data_info->getAttr('top_id');
        }
        MemberGroupRelationModel::where(['member_id'=>$member_id])->setField('top_id',$top_id);
        return true;
    }



    /**
     * 缓存清理
     * @return void
     */
    private function cache_clear()
    {
    }

    /**
     * 创客奖金
     * @param $id
     * @return bool
     * @throws \think\Exception
     * @throws \think\exception\DbException
     */
    public static  function reward($id){

        $order = OrderShopModel::get($id);

        $money = $order['product_num']*ProductModel::PRODUCT_PRICE;//成本价

        $top_group_id = MemberGroupRelationModel::get_group_id($order['top_id']);

        $two_group_id = MemberGroupRelationModel::get_group_id($order['two_id']);


        switch ($order['product_name']){
            case '代理人':
                $member_group_id = MemberGroupRelation::four;
                $member_price = 1580;
                break;
            case '执行董事':
                $member_group_id = MemberGroupRelation::three;
                $member_price = 1380;
                break;
            case '全球合伙人':
                $member_group_id = MemberGroupRelation::second;
                $member_price = 1180;
                break;
            case '联合创始人':
                $member_price = 980;
                $member_group_id =  MemberGroupRelation::first;
                break;
            default:
                $member_price = 1580;
                $member_group_id =  MemberGroupRelation::four;
                break;
        }

        switch ($top_group_id){
            case MemberGroupRelation::first:
                $current_price = 980;
                break;
            case MemberGroupRelation::second:
                $current_price = 1180;
                break;
            case MemberGroupRelation::three:
                $current_price = 1380;
                break;
            case MemberGroupRelation::four:
                $current_price = 1580;
                break;
            default:
                $current_price = 1580;
                break;
        }

        if ($current_price == 0){
            return false;
        }

        if ($order['is_admin'] == 1){//云库存结算

                $commission =  StrHelper::ceil_decimal($money, 2);
                Member::commission_inc($order['top_id'], $commission);
                MemberCommission::insert_log($order['top_id'], MemberCommission::maker3, $commission, $commission, '来自'.$order['nick_name'].'的分销奖￥'.$commission,0);

                $commission = ($member_price - $current_price)*$order['product_num'];
                Member::commission_inc($order['top_id'], $commission);
                MemberCommission::insert_log($order['top_id'], MemberCommission::maker4, $commission, $commission, '来自'.$order['nick_name'].'的批发奖￥'.$commission,0);

        }else{//平台结算

            $center_commission = $order['product_num']*20;

            $reward1 = StrHelper::ceil_decimal($money, 2)*0.03;//联合创始人奖金池

            $reward2 = StrHelper::ceil_decimal($money, 2)*0.02;//全球合伙人奖金池

            $reward3 = StrHelper::ceil_decimal($money, 2)*0.01;//执行董事奖金池

            $data_log = [];

            $is_center = MemberModel::find_member_uid($order['member_id'])['is_center'];

            if ($is_center == true){
                Member::commission_inc($order['member_id'], $center_commission);
                $data_log[0]['member_id']   = $order['member_id'];
                $data_log[0]['type']        = MemberCommission::maker8;
                $data_log[0]['amount']      = 0;
                $data_log[0]['value']       = $center_commission;
                $data_log[0]['description'] = '来自'.$order['nick_name'].'的报单中心奖';
                $data_log[0]['relation_id'] = 0;
                $data_log[0]['create_time'] = time();
            }
            Reword::commission_inc('first_commission', $reward1);
            $data_log[1]['member_id']   = 0;
            $data_log[1]['type']        = MemberCommission::first;
            $data_log[1]['amount']      = 0;
            $data_log[1]['value']       = $reward1;
            $data_log[1]['description'] = '来自'.$order['nick_name'].'的业绩分红';
            $data_log[1]['relation_id'] = 0;
            $data_log[1]['create_time'] = time();

            Reword::commission_inc('second_commission', $reward2);
            $data_log[2]['member_id']   = 0;
            $data_log[2]['type']        = MemberCommission::second;
            $data_log[2]['amount']      = 0;
            $data_log[2]['value']       = $reward2;
            $data_log[2]['description'] = '来自'.$order['nick_name'].'的业绩分红';
            $data_log[2]['relation_id'] = 0;
            $data_log[2]['create_time'] = time();

            Reword::commission_inc('three_commission', $reward3);
            $data_log[3]['member_id']   = 0;
            $data_log[3]['type']        = MemberCommission::three;
            $data_log[3]['amount']      = 0;
            $data_log[3]['value']       = $reward3;
            $data_log[3]['description'] = '来自'.$order['nick_name'].'的业绩分红';
            $data_log[3]['relation_id'] = 0;
            $data_log[3]['create_time'] = time();

            MemberCommission::insert_log_all($data_log);

            $array = self::sort($order['top_id']);

            if ($array){
                self::RankAward($array,$order['product_num'],$order['nick_name']);//职级奖
            }

            if ($order['two_id']){//有接点人
                    $commission = StrHelper::ceil_decimal($money, 2)*0.2;//市场开发奖
                    Member::commission_inc($order['top_id'], $commission);
                    MemberCommission::insert_log($order['top_id'], MemberCommission::maker14, $commission, $commission, '来自'.$order['nick_name'].'的开发奖',0);


               //推荐人等级大于用户等级拥有批发奖管理奖

                $array = self::sort($order['two_id']);
                if ($array){
                    self::WholesaleAward($array,$member_price,$order['product_num'],$order['nick_name'],$member_group_id);
                }



                //接点人等级大于等于用户等级拥有维护奖
                if ($member_group_id >= $two_group_id){
                    $commission = StrHelper::ceil_decimal($money, 2)*0.01;//市场维护奖
                    Member::commission_inc($order['two_id'], $commission);
                    MemberCommission::insert_log($order['two_id'], MemberCommission::maker6, $commission, $commission, '来自'.$order['nick_name'].'的维护奖',0);
                }


            }else{//无接点人

                $array = self::sort($order['top_id']);

                //平级推或下推上
                if ($member_group_id >= $top_group_id){
                    $commission = StrHelper::ceil_decimal($money, 2)*0.2;//市场开发奖
                    Member::commission_inc($order['top_id'], $commission);
                    MemberCommission::insert_log($order['top_id'], MemberCommission::maker14, $commission, $commission, '来自'.$order['nick_name'].'的开发奖',0);

                    $commission = StrHelper::ceil_decimal($money, 2)*0.01;//市场维护奖
                    Member::commission_inc($order['top_id'], $commission);
                    MemberCommission::insert_log($order['top_id'], MemberCommission::maker6, $commission, $commission, '来自'.$order['nick_name'].'的维护奖',0);
                    if ($array){
                        self::WholesaleAward($array,$member_price,$order['product_num'],$order['nick_name'],$member_group_id);
                    }
                }else{//上对下 管理奖  批发奖
                    if ($array){
                        self::WholesaleAward($array,$member_price,$order['product_num'],$order['nick_name'],$member_group_id);
                    }
                }

            }

        }

//        if ($order['two_id']){
//            self::level_up($order['two_id'],$two_group_id);
//        }
//        self::level_up($order['top_id'],$top_group_id);

    }


     //递归批发奖/管理奖
    public static function  WholesaleAward($array,$money,$num,$member_name,$member_group_id){

        foreach ($array as $key => $row) {
            $edition[$key] = $key;
        }
        array_multisort($edition, SORT_DESC,$array);//从大到小排序

        $group_id = 0;
        $new_array = [];
        foreach ($array as $k => &$val){
            if ($val['group_id'] > $group_id){
                $val['price'] = MemberGroupModel::where(['group_id'=>$val['group_id']])->find()['group_price'];
                array_push($new_array,$val);
                $group_id = $val['group_id'];
            }
        }

        $bottom_id = 0;
        foreach ($new_array as $k=>&$val){
            $val['bottom_id'] = $bottom_id;
            $bottom_id =  $val['member_id'];
        }

        $new_array = array_column($new_array,null,'member_id');

        foreach ($new_array as $k=>$val){
            $cj = $val['price']*$num;
            if ($val['bottom_id'] == 0){
                $commission = $money*$num -  $cj ; //批发奖
                if ($commission > 0){
                    Member::commission_inc($val['member_id'], $commission);
                    MemberCommission::insert_log($val['member_id'], MemberCommission::maker4, $commission, $commission, '来自'.$member_name.'的批发奖', '');
                }

            if ($member_group_id < $val['group_id']){
                $commission = ProductModel::PRODUCT_PRICE*$num*0.01;//百分之一管理奖
                Member::commission_inc($val['member_id'], $commission);
                MemberCommission::insert_log($val['member_id'], MemberCommission::maker5, $commission, $commission, '来自'.$member_name.'的管理奖', '');
            }

            }else{
                $commission =   ($new_array[$val['bottom_id']]['price']*$num - $cj);
                Member::commission_inc($val['member_id'], $commission);
                MemberCommission::insert_log($val['member_id'], MemberCommission::maker4, $commission, $commission, '来自'.$member_name.'的批发奖', '');

                if ($member_group_id < $val['group_id']){
                $commission = ProductModel::PRODUCT_PRICE*$num*0.01;//百分之一管理奖
                Member::commission_inc($val['member_id'], $commission);
                MemberCommission::insert_log($val['member_id'], MemberCommission::maker5, $commission, $commission, '来自'.$member_name.'的管理奖', '');
                }
            }

        }

    }


    //递归职级奖
    public static function RankAward($array,$num,$member_name){

        foreach ($array as $key => $row) {
            $edition[$key] = $key;
        }
        array_multisort($edition, SORT_DESC,$array);//从大到小排序

        $group_id = 0;
        $new_array = [];
        foreach ($array as $k => &$val){
            if ($val['group_id'] > $group_id){
                $val['group_ratio'] = MemberGroupModel::where(['group_id'=>$val['group_id']])->find()['group_ratio'];
                array_push($new_array,$val);
                $group_id = $val['group_id'];
            }
        }

        $bottom_id = 0;
        foreach ($new_array as $k=>&$val){
            $val['bottom_id'] = $bottom_id;
            $bottom_id =  $val['member_id'];
        }

        $new_array = array_column($new_array,null,'member_id');

        foreach ($new_array as $k=>$val){
            $cj = ($val['group_ratio']/1000)*ProductModel::PRODUCT_PRICE*$num;
            if ($val['bottom_id'] == 0){
                $commission = $cj ; //职级奖
                Member::commission_inc($val['member_id'], $commission);
                MemberCommission::insert_log($val['member_id'], MemberCommission::maker7, $commission, $commission, '来自'.$member_name.'的职级奖', '');

            }else{
                $group_ratio = $val['group_ratio'] - $new_array[$val['bottom_id']]['group_ratio'];
                $commission = ($group_ratio/1000)*$num*ProductModel::PRODUCT_PRICE;
                Member::commission_inc($val['member_id'], $commission);
                MemberCommission::insert_log($val['member_id'], MemberCommission::maker7, $commission, $commission, '来自'.$member_name.'的职级奖', '');

            }

        }

    }



    public static function sort1($id,$data)
    {
        $arr = [];
        foreach($data as $k => $v){
            //从小到大 排列
            if($v['member_id'] == $id){
                $arr[$k]['member_id'] = $v['member_id'];
                $arr[$k]['group_id'] =  $v['group_id'];
                if($v['top_id'] > 0){
                    $arr = array_merge(self::sort1($v['top_id'],$data), $arr);
                }
            }
        }
        return $arr;
    }
    public static function sort($top_id)
    {
        $where['group_id'] = ['neq',MemberGroupRelationModel::seven];
        $data = $a = MemberGroupRelationModel::all_list([],$where);
        $arr = self::sort1($top_id,$data);
        return $arr;
    }





    /**
     * @param $member_id
     * @param $group_id
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public static function level_up($member_id,$group_id)
    {
        $where['group_id'] = ['eq', $group_id];
        $where['top_id'] = ['eq',$member_id];
        $count = MemberGroupRelation::where($where)->count();
        unset( $where['top_id']);
        $list = MemberGroupRelation::where($where)->field('member_id')->select();
        switch ($group_id){
            case 1: //创客升级增长官
                if ($count >= 3){
                    MemberGroupRelation::where(['member_id'=>$member_id])->setInc('group_id');
                }
                break;
            case 2: //增长官升级董事
                foreach ($list as $k => $v) {
                    $a = MemberGroupRelation::all_list(['member_id', 'top_id']);
                    $new_data = [];
                    foreach ($a as $b) {
                        if ($b['top_id'] == $v['member_id']) {
                            $new_data[] = $b['member_id'];
                        }
                        if (in_array($b['top_id'], $new_data)) {
                            $new_data[] = $b['member_id'];
                        }
                    }

                    $list[$k]['count'] =  count($new_data);
                    if ($list[$k]['count'] >= 50 && $count >=2){
                        MemberGroupRelation::where(['member_id'=>$list[$k]['member_id']])->setInc('group_id');
                    }else{
                        $count =  MemberGroupRelation::where(['top_id'=>$list[$k]['member_id'],'group_id'=>$group_id])->count();
                        if ($count >=3){
                            MemberGroupRelation::where(['member_id'=>$list[$k]['member_id']])->setInc('group_id');
                        }
                    }
                }
                break;
            case 3://董事升级全球合伙人
                foreach ($list as $k => $v) {
                    $a = MemberGroupRelation::all_list(['member_id', 'top_id']);
                    $new_data = [];
                    foreach ($a as $b) {
                        if ($b['top_id'] == $v['member_id']) {
                            $new_data[] = $b['member_id'];
                        }
                        if (in_array($b['top_id'], $new_data)) {
                            $new_data[] = $b['member_id'];
                        }
                    }
                    $list[$k]['count'] =  count($new_data);
                    if ($list[$k]['count'] >= 100 && $count >=2){
                        MemberGroupRelation::where(['member_id'=>$list[$k]['member_id']])->setInc('group_id');
                    }else{
                        $count =  MemberGroupRelation::where(['top_id'=>$list[$k]['member_id'],'group_id'=>$group_id])->count();
                        if ($count >=3){
                            MemberGroupRelation::where(['member_id'=>$list[$k]['member_id']])->setInc('group_id');
                        }
                    }
                }
                break;
            case 4: //全球合伙人升级联合创始人
                foreach ($list as $k => $v) {
                    $a = MemberGroupRelation::all_list(['member_id', 'top_id']);
                    $new_data = [];
                    foreach ($a as $b) {
                        if ($b['top_id'] == $v['member_id']) {
                            $new_data[] = $b['member_id'];
                        }
                        if (in_array($b['top_id'], $new_data)) {
                            $new_data[] = $b['member_id'];
                        }
                    }
                    $list[$k]['count'] =  count($new_data);
                    if ($list[$k]['count'] >= 300 && $count >=2){
                        MemberGroupRelation::where(['member_id'=>$list[$k]['member_id']])->setInc('group_id');
                    }else{
                        $count =  MemberGroupRelation::where(['top_id'=>$list[$k]['member_id'],'group_id'=>$group_id])->count();
                        if ($count >=3){
                            MemberGroupRelation::where(['member_id'=>$list[$k]['member_id']])->setInc('group_id');
                        }
                    }
                }

                break;
        }

    }






    /**
     * 已配送订单列表
     * @return mixed
     * @throws Exception
     */
    public function distribution_index()
    {
        $where = $this->search('order_sn', '输入需查询的订单号');

        $where['del']    = false;
        $where['status'] = OrderShopModel::STATUS_ALREADY_DISTRIBUTION;

        $order = $this->sort_order(OrderShopModel::getTableFields(), 'order_time', 'desc');

        $this->export($where, $order);

        $list = OrderShopModel::page_list($where, $order);
        if (!$list->isEmpty()) {
            $list->load(['Member', 'Distribution']);
        }

        $this->assign($list->toArray());
        return $this->fetch_view();
    }

    /**
     * 收货操作
     * @param $id
     * @throws Exception
     */
    public function distribution_examine($id)
    {
        $this->is_ajax OR $this->error('请求错误！');

        $where['order_id']      = $id;
        $where['del']           = false;
        $where['status']        = OrderShopModel::STATUS_ALREADY_DISTRIBUTION;
        $where['refund_status'] = ['not in', [OrderShopModel::REFUND_STATUS_APPLY]];

        $data_info = OrderShopModel::get($where);
        empty($data_info) AND $this->error('数据不存在！');

        $data   = [
            'refund_status' => OrderShopModel::REFUND_STATUS_NO,
            'status'        => OrderShopModel::STATUS_CHECKING,
        ];
        $result = $data_info->save($data);
        $result OR $this->error('收货失败！');

        $this->success('收货完成！', $this->http_referer ?: $this->return_url());
    }

    /**
     * 检测中订单列表
     * @return mixed
     * @throws Exception
     */
    public function check_index()
    {
        $where = $this->search('order_sn', '输入需查询的订单号');

        $where['del']    = false;
        $where['status'] = OrderShopModel::STATUS_ALREADY_PAY;

        $order = $this->sort_order(OrderShopModel::getTableFields(), 'order_time', 'desc');

//        $this->export($where, $order);

        $list = OrderShopModel::page_list($where, $order);
        if (!$list->isEmpty()) {
            $list->load(['Member']);
            $list->append(['top_name','top_uid']);
        }

        $this->assign($list->toArray());
        return $this->fetch_view();
    }

    /**
     * 已完成订单列表
     * @return mixed
     * @throws Exception
     */
    public function finish_index()
    {
        $where = $this->search('order_sn', '输入需查询的订单号');

        $where['del']    = false;
        $where['status'] = ['in', [OrderShopModel::STATUS_FINISH, OrderShopModel::STATUS_EVALUATE]];

        $order = $this->sort_order(OrderShopModel::getTableFields(), 'order_time', 'desc');

//        $this->export($where, $order);

        $list = OrderShopModel::page_list($where, $order);
        if (!$list->isEmpty()) {
            $list->load(['Member']);
            $list->append(['top_name','top_uid']);
        }

        $this->assign($list->toArray());
        return $this->fetch_view();
    }

    /**
     * 退款订单列表
     * @return mixed
     * @throws Exception
     */
    public function refund_index()
    {
        $where = $this->search('order_sn', '输入需查询的订单号');

        $where['del']           = false;
        $where['refund_status'] = OrderShopModel::REFUND_STATUS_APPLY;

        $order = $this->sort_order(OrderShopModel::getTableFields(), 'order_time', 'desc');

        $this->export($where, $order);

        $list = OrderShopModel::page_list($where, $order);
        if (!$list->isEmpty()) {
            $list->load(['Member']);
        }

        $this->assign($list->toArray());
        return $this->fetch_view();
    }

    /**
     * 退款成功订单列表
     * @return mixed
     * @throws Exception
     */
    public function refund_success_index()
    {
        $where = $this->search('order_sn', '输入需查询的订单号');

        $where['del']           = false;
        $where['refund_status'] = ['in', [OrderShopModel::REFUND_STATUS_SUCCESS, OrderShopModel::REFUND_STATUS_FINISH]];

        $order = $this->sort_order(OrderShopModel::getTableFields(), 'order_time', 'desc');

        $this->export($where, $order);

        $list = OrderShopModel::page_list($where, $order);
        if (!$list->isEmpty()) {
            $list->load(['Member']);
        }

        $this->assign($list->toArray());
        return $this->fetch_view();
    }

    /**
     * 退款审核
     * @param $id
     * @return void
     * @throws Exception
     */
    public function refund_examine($id)
    {
        $this->is_ajax OR $this->error('请求错误！');

        $where['order_id']      = $id;
        $where['del']           = false;
        $where['refund_status'] = OrderShopModel::REFUND_STATUS_APPLY;

        $data_info = OrderShopModel::get($where);
        empty($data_info) AND $this->error('数据已删除！');

        $data_info->order_refund(boolval(input('status', '')));

        $this->success('审核完成！', $this->http_referer ?: $this->return_url());
    }

    /**
     * 数据导出
     * @param $where
     * @param $order
     * @throws Exception
     */
    private function export($where, $order)
    {
        $export = input('export', false);

        if (!$export) {
            $this->assign('export', true);
            return;
        }

        $list = OrderShopModel::all_list([], $where, $order);
        if (!$list->isEmpty()) {
            $list->load([]);
            $list->each(
                function ($item) {
                    /** @var OrderShopModel $item */
                    $item->setAttr('payment', $this->param['payment'][$item->getAttr('payment_id')] ?? '未支付');
                    $item->setAttr('status', $this->param['status'][$item->getAttr('status')] ?? '');
                    $item->setAttr('order_time', date('Y-m-d H:i:s', $item->getAttr('order_time')));
                    $item->setAttr('mobile', $item->getAttr('mobile'));
                    $item->setAttr('shop_name', $item->getAttr('shop_name'));
                    $item->setAttr('nick_name', $item->getAttr('nick_name'));
                    $item->setAttr('amount', $item->getAttr('amount'));
                }
            );
        }

        $title = [
            'order_sn'        => '订单号',
            'transaction_sn'  => '支付单号',
            'shop_name'       => '公司名称',
            'mobile'          => '用户手机号',
            'nick_name'       => '用户姓名',
            'amount'          => '订单价格',
            'payment'         => '支付方式',
            'status'          => '状态',
            'order_time'      => '下单时间',
        ];

        $this->export_excel('中心授权订单', $title, $list->toArray());
    }
}
