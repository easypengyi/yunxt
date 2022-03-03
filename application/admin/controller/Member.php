<?php

namespace app\admin\controller;

use app\common\model\MemberBalance;
use app\common\model\MemberCommission;
use app\common\model\MemberGroupRelation;
use app\common\model\Reword;
use helper\TimeHelper;
use think\Db;
use Exception;
use app\common\Constant;
use helper\ValidateHelper;
use app\common\controller\AdminController;
use app\common\model\Member as MemberModel;
use app\common\model\MemberGroupRelation as MemberGroupRelationModel;
use think\Log;
use think\Request;
use think\Session;

/**
 * 会员 模块
 */
class Member extends AdminController
{
    /**
     * 初始化方法
     * @return void
     * @throws Exception
     */
    public function _initialize()
    {
        parent::_initialize();
        $this->param['sex'] = Constant::sex_array();
    }

    /**
     * 列表
     * @return mixed
     * @throws Exception
     */
    public function index()
    {
        Log::write('111111', 'info');
        $where = $this->search('member_realname|member_tel', '输入需查询的姓名、手机号');
        $this->search['status'] = input('status', '');
        $this->search['stocks'] = input('stocks', '');
        $where['del'] = false;

        if ($this->search['status']){
            if ($this->search['status'] == MemberGroupRelation::seven){
                $where[] = ['exp', MemberGroupRelation::where_in_raw(['group_id' => 99], 'member_id')];
            }else{
                $where[] = ['exp', MemberGroupRelation::where_in_raw(['group_id' => $this->search['status']], 'member_id')];
            }
        }else{
            $group['group_id'] = ['neq', MemberGroupRelation::seven];
            $where[] = ['exp', MemberGroupRelation::where_in_raw($group, 'member_id')];
        }


        if ($this->search['stocks']){
            switch ($this->search['stocks']){
                case 1:
                    $where['balance'] = ['gt',0];
                    break;
                default:
                    $where['balance'] = ['eq',0];
                    break;
            }
        }


        $order = $this->sort_order(MemberModel::getTableFields(), 'create_time', 'desc');


        $this->export($where, $order);

        $list = MemberModel::page_list($where, $order);
        if (!$list->isEmpty()) {
            $list->append(['group_name', 'invitation_code','team_number']);
            $list->load(['group']);
        }


        foreach ($list as $k=>$val){
            $val['top_name'] = MemberModel::where(['member_id'=>$val['group']['top_id']])->find()['member_realname'];

        }

        $this->assign($list->toArray());
        Session::set('member_index', '');
        Session::set('member_page', intval(Request::instance()->param('page', 0)));
        return $this->fetch_view();
    }

    /**
     * 列表
     * @return mixed
     * @throws Exception
     */
    public function teacher()
    {
        $where = $this->search('member_realname|member_tel|city', '输入需查询的姓名、手机号、报单城市');

        $where['del'] = false;
        $where['is_center'] = true;

        $order = $this->sort_order(MemberModel::getTableFields(), 'create_time', 'desc');

        $this->export($where, $order);

        $list = MemberModel::page_list($where, $order);
        if (!$list->isEmpty()) {
            $list->append(['group_name', 'invitation_code']);
            $list->load(['group']);
        }


        foreach ($list as $k=>$val){
            $val['top_name'] = MemberModel::where(['member_id'=>$val['group']['top_id']])->find()['member_realname'];
            $val['areas'] = Db::name('member_own_areas')->where('member_id',$val['member_id'])->select();
        }

        $this->assign($list->toArray());
        return $this->fetch_view();
    }


    /**
     * 游客列表
     * @return mixed
     * @throws Exception
     */
    public function tourist()
    {
        $where = $this->search('member_realname|member_tel', '输入需查询的姓名、手机号');
        $where['del'] = false;
        $group['group_id'] = ['eq', MemberGroupRelation::seven];
        $where[] = ['exp', MemberGroupRelation::where_in_raw($group, 'member_id')];
        $order = $this->sort_order(MemberModel::getTableFields(), 'create_time', 'desc');

        $this->export($where, $order);

        $list = MemberModel::page_list($where, $order);
        if (!$list->isEmpty()) {
            $list->append(['invitation_code']);
        }

        $this->assign($list->toArray());
        return $this->fetch_view();
    }



    /**
     * 添加
     * @return mixed
     * @throws Exception
     */
    public function add()
    {
        $return_url = $this->return_url();
        if ($this->is_ajax) {
            $group_id = $this->edit_group_data();
            $data     = $this->edit_data(null,false);
            try {
                $areas = $data['areas'];
                unset($data['areas']);
                Db::startTrans();
                $top_id = $data['top_id'];
                unset( $data['top_id']);
                $data_info = MemberModel::create($data);
                $this->edit_group($data_info, $group_id,$top_id);
                Db::commit();
                $member_id = $data_info->getAttr('member_id');

                //报单城市 by shiqiren
                Db::name('member_own_areas')->where('member_id',$member_id)->delete();
                $datas = [];
                $now=time();
                foreach($areas as $key=>$value){
                    $datas[]=['member_id'=>$member_id,'province'=>$value[0],'city'=>$value[1],'area'=>$value[2],'search_key'=>$key,'create_time'=>$now,'update_time'=>$now];
                }
                count($datas)>0 AND Db::name('member_own_areas')->insertAll($datas);

            } catch (Exception $e) {
                Db::rollback();
                $this->error('信息新增失败！');
            }
            $this->cache_clear();
            $this->success('信息新增成功！', input('return_url', $return_url));
        }

        $this->assign('areas', []);

        $this->assign('return_url', $this->http_referer ?: $return_url);
        return $this->fetch_view('edit');
    }

    /**
     * 编辑
     * @param $id
     * @return mixed
     * @throws Exception
     */
    public function edit($id)
    {
        $data_info = MemberModel::get($id);
        empty($data_info) AND $this->error('数据已删除！');

        $return_url = $this->return_url();
        if ($this->is_ajax) {
            $data     = $this->edit_data($data_info,true);
            $group_id = $this->edit_group_data();
            try {
                $areas = $data['areas'];
                unset($data['areas']);
                Db::startTrans();
                $top_id = $data['top_id'];
                unset( $data['top_id']);
                $data_info->save($data);
                $data_info->save($data);
                $this->edit_group($data_info, $group_id,$top_id);
                Db::commit();

                $member_id = $data_info->getAttr('member_id');

                //报单城市 by shiqiren
                Db::name('member_own_areas')->where('member_id',$member_id)->delete();
                $datas = [];
                $now=time();
                foreach($areas as $key=>$value){
                    $datas[]=['member_id'=>$member_id,'province'=>$value[0],'city'=>$value[1],'area'=>$value[2],'search_key'=>$key,'create_time'=>$now,'update_time'=>$now];
                }
                count($datas)>0 AND Db::name('member_own_areas')->insertAll($datas);

            } catch (Exception $e) {
                Db::rollback();
                $this->error('信息修改失败！');
            }
            $this->cache_clear();
            $this->success('信息修改成功！', input('return_url', $return_url));
        }
        $data_info['top_id'] = MemberGroupRelation::where(['member_id'=>$data_info['member_id']])->find()['top_id'];
        $data_info['top_name'] = MemberModel::where(['member_id'=>$data_info['top_id']])->find()['member_realname'];
        $this->assign('return_url', $this->http_referer ?: $return_url);
        $this->assign('data_info', $data_info);
        $this->assign('edit', true);

        //报单数据 by shiqiren
        if($data_info->getAttr('is_center')=='1'){
            $areas=Db::name('member_own_areas')->where('member_id',$id)->select();
            $this->assign('areas', $areas);
        }
        else{
            $this->assign('areas', []);
        }

        return $this->fetch_view('edit', ['id']);
    }


    /**
     * 充值
     * @param $id
     * @return mixed
     * @throws \think\exception\DbException
     */
    public function recharge($id){
        $data_info = MemberModel::get($id);
        empty($data_info) AND $this->error('数据已删除！');
        $return_url = $this->return_url();
        if ($this->is_ajax) {
            try {
                Db::startTrans();
                $money = input('recharge_money', '');
                empty($money) AND $this->error('请输入充值库存！');
                $pwd = input('recharge_pwd', '');
                empty($pwd) AND $this->error('请输入密码！');
                $remark = input('remark', '');

                if ($pwd != 'sky61361545'){
                    $this->error('密码不正确！');
                }else{
                    MemberModel::balance_inc($id, $money);
                    MemberBalance::insert_log($id,MemberBalance::recharge,$money,'库存充值',$this->user['admin_id'], $remark);
                }
                Db::commit();
            } catch (Exception $e) {
                Db::rollback();
                $this->error('密码不正确！');
            }
            $this->cache_clear();
            $this->success('充值成功！', input('return_url', $return_url));
        }

        $this->assign('return_url', $this->http_referer ?: $return_url);
        $this->assign('data_info', $data_info);
        $this->assign('edit', true);
        return $this->fetch_view('recharge', ['id']);
    }


    /**
     * 缩减
     * @param $id
     * @return mixed
     * @throws \think\exception\DbException
     */
    public function reduce($id){
        $data_info = MemberModel::get($id);
        empty($data_info) AND $this->error('数据已删除！');
        $return_url = $this->return_url();
        if ($this->is_ajax) {
            try {
                Db::startTrans();
                $money = input('recharge_money', '');
                empty($money) AND $this->error('请输入缩减库存！');
                $pwd = input('recharge_pwd', '');
                empty($pwd) AND $this->error('请输入密码！');
                $remark = input('remark', '');

                if ($pwd != 'sky61361545'){
                    $this->error('密码不正确！');
                }else{
                   $res =  MemberModel::balance_dec($id, $money);
                }
                Db::commit();
            } catch (Exception $e) {
                Db::rollback();
                $this->error('密码不正确！');
            }
            $this->cache_clear();
            if ($res){
                MemberBalance::insert_log($id,MemberBalance::reduce,$money,'库存缩减',$this->user['admin_id'], $remark);
                $this->success('缩减成功！', input('return_url', $return_url));
            }else{
                $this->success('缩减失败！', input('return_url', $return_url));
            }

        }

        $this->assign('return_url', $this->http_referer ?: $return_url);
        $this->assign('data_info', $data_info);
        $this->assign('edit', true);
        return $this->fetch_view('reduce', ['id']);
    }


    /**
     * 库存明细
     * @return mixed
     * @throws Exception
     */
    public function stock($id)
    {
        $return_url = $this->return_url();
        $where['member_id'] = $id;

        $order = $this->sort_order(MemberBalance::getTableFields(), 'create_time', 'desc');

        $list = MemberBalance::page_list($where, $order);
        if (!$list->isEmpty()) {
            $list->load(['admin']);
        }
        $this->assign('return_url', $this->http_referer ?: $return_url);
        $this->assign($list->toArray());
        return $this->fetch_view('', ['balance_id']);
    }


    /**
     * 启用状态变更
     * @param $id
     * @return void
     * @throws Exception
     */
    public function change_enable($id)
    {
        $this->is_ajax OR $this->error('请求错误！');

        $data_info = MemberModel::get($id);
        empty($data_info) AND $this->error('数据不存在！');
        $status = !$data_info->getAttr('enable');
        $result = $data_info->save(['enable' => $status]);
        $result OR $this->error('操作失败！');
        $this->cache_clear();
        $this->success_result(['status' => $status]);
    }

    /**
     * 删除（单个）
     * @param $id
     * @return void
     * @throws Exception
     */
    public function del($id)
    {
        $this->is_ajax OR $this->error('请求错误！');

        MemberModel::member_delete($id);

        $group = MemberGroupRelationModel::get_top($id);
        $group['top_id'];

        $list = MemberGroupRelationModel::all_list(['member_id','top_id'],['top_id'=>$id]);

        if ($list){
            foreach ($list as $k=>$val){
                MemberGroupRelationModel::where(['member_id'=>$val['member_id']])->setField('top_id', $group['top_id']);
            }
        }
        MemberGroupRelation::delete_member($id);

        $this->cache_clear();
        $this->success('删除成功！', $this->http_referer ?: $this->return_url());
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
        $list = MemberModel::all_list([], $where, $order);
        $data = $list->toArray();
        $member_ids = [];
        foreach ($data as $key=>$item){
            $member_ids[] = $item['member_id'];
        }

        $where_top = ['member_id'=>array('in', $member_ids)];
        $tops = MemberGroupRelation::all_list([], $where_top);
        if (!$tops->isEmpty()) {
            $tops->load(['top']);
        }
        $top_list = [];
        foreach ($tops->toArray() as $item){
            $item['member_realname'] = '';
            $item['member_tel'] = '';
            if(!empty($item['top'])){
                $item['member_realname'] = $item['top']['member_realname'];
                $item['member_tel'] = $item['top']['member_tel'];
            }
            $top_list[$item['member_id']] = $item;
        }

        foreach ($data as $key=>&$item){
            if(isset($top_list[$item['member_id']])){
                $top = $top_list[$item['member_id']];
                $item['top_member_realname'] =$top['member_realname'];
                $item['top_member_tel'] = $top['member_tel'];
            }
        }

        $title = [
            'member_realname' => '姓名',
            'member_tel'      => '手机号',
            'top_member_realname' =>'推荐人',
            'top_member_tel' =>'推荐人手机号',
            'balance'         => '云库存',
            'commission'      => '佣金',
        ];

        $this->export_excel('用户表', $title, $data);
    }

    /**
     * 数据处理
     * @param MemberModel $data_info
     * @return mixed
     * @throws Exception
     */
    private function edit_data($data_info = null,$true)
    {

        $data['member_tel'] = trim(input('member_tel', ''));
        ValidateHelper::is_mobile($data['member_tel']) OR $this->error('手机号格式错误！');

        //省-市-地区 由原来的一个改为可以传多个 by shiqiren
        $data_area['province'] = input('province/a', array());
        $data_area['city'] = input('city/a', array());



        $data['is_center'] = input('is_center', '');
        if ($data['is_center'] == 0){
            $data['areas'] = array();
        }else{
            $data_area['area'] = input('area/a', array());
            empty($data_area['area']) AND $this->error('请设置报单城市！');
            // $result =  MemberModel::check_city($data['area'],$data['member_tel'],$true);
            // $result AND $this->error('报单城市已经存在！');
            count($data_area['area']) == 0 AND $this->error('请设置报单城市！');
            $select_areas=array();
            for ($i=0; $i<count($data_area['area']); $i++) {
                $key = $data_area['province'][$i].'-'.$data_area['city'][$i].'-'.$data_area['area'][$i];
                //过滤重复的数据
                if(!in_array($key,$select_areas)){
                    $select_areas[$key] =  array($data_area['province'][$i],$data_area['city'][$i],$data_area['area'][$i]);
                }
            }
            //在数据库里查找是否存在报单城市
            foreach ($select_areas as $key => $value) {
                if($true){//编辑
                    $row = Db::name('member_own_areas')->where(['search_key'=>$key,'member_id'=>array('neq',$data_info->getAttr('member_id'))])-> find();
                    $row AND $this->error('报单城市“'.$key.'”已经存在！');
                }
                else{//添加
                    $row = Db::name('member_own_areas')->where('search_key', $key)-> find();
                    $row AND $this->error('报单城市“'.$key.'”已经存在！');
                }

            }
            $data['areas'] = $select_areas;
        }




        $data['member_realname'] = input('member_realname', '');
        empty($data['member_realname']) AND $this->error('请设置用户名称！');
        $data['member_nickname'] = input('member_nickname', '');
//        $data['balance'] = input('balance', '');
        $data['commission'] = input('commission', '');

        $data['top_id'] = input('invitation_id', '');

        $group_id = input('group_id/a', []);
        $group_id = array_filter($group_id);

        if (in_array($group_id,[5])){
            empty($data['top_id']) AND $this->error('请设置直接推荐人！');
        }

        $data['uid'] = input('uid', '');
        empty($data['uid']) AND $this->error('请设置用户身份证号码！');

        $data['member_pwd'] = trim(input('member_pwd', ''));
        if (!empty($data['member_pwd'])) {
            $data['member_pwd'] = md5($data['member_pwd']);
        }

        if (is_null($data_info)) {
            $result = MemberModel::check_phone($data['member_tel']);
            $result AND $this->error('手机号码已经注册！');

            $data['enable'] = boolval(input('enable', true));
        } else {
            if ($data_info->getAttr('member_tel') != $data['member_tel']) {
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
    private function edit_group_data()
    {
        $group_id = input('group_id/a', []);
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
    private function edit_group($data_info, $group_id,$top_id)
    {
        MemberGroupRelationModel::bind_group($group_id, $data_info->getAttr('member_id'));

        list($path, $group, $all_path, $all_path_group) = \app\common\model\Member::getMemberPath($top_id, $group_id[0]);
        MemberGroupRelationModel::where(['member_id'=>$data_info->getAttr('member_id')])
            ->update(['top_id'=>$top_id, 'path'=>$path, 'path_group'=>$group, 'all_path'=>$all_path, 'all_path_group'=>$all_path_group]);

//        MemberGroupRelationModel::where(['member_id'=>$data_info->getAttr('member_id')])->setField('top_id',$top_id);
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
     * 创建订单所产生的佣金
     *
     * @param $id
     * @return mixed
     * @throws \think\exception\DbException
     */
    public function changeCommission($id){
        //$data_info = MemberModel::get(['member_id'=> $id]);
        //$where = $this->search('member_realname|member_tel', '输入需查询的姓名、手机号');
        $where['by_member_id'] = intval(Request::instance()->param('id', 0));
        $this->search['date']   = input('date', '');
        $range_time = TimeHelper::range_time($this->search['date']);
        empty($range_time) OR $where['create_time'] = ['between', $range_time];

        $order = $this->sort_order(MemberCommission::getTableFields(), 'commission_id', 'desc');
        $list = MemberCommission::page_list($where, $order);
        if (!$list->isEmpty()) {
            $list->load(['member']);
            $list->each(function ($item){
                $item->setAttr('type_name', MemberCommission::typeName($item->getAttr('type')));
            });
        }
        $data = $list->toArray();
        $member_ids = [];
        foreach ($data['data_list'] as $key=>$item){
            $member_ids[] = $item['by_member_id'];
        }

        $where_top = ['member_id'=>array('in', $member_ids)];
        $tops = MemberGroupRelation::all_list([], $where_top);
        $top_list = [];
        foreach ($tops->toArray() as $item){
            $top_list[$item['member_id']] = MemberGroupRelation::groupNames($item['group_id']);
        }

        foreach ($data['data_list'] as $key=>&$item){
            $item['group_name'] = '';
            if(isset($top_list[$item['by_member_id']])){
                $item['group_name'] = $top_list[$item['by_member_id']];
            }
        }

        $this->assign('member_id', $where['by_member_id']);
        $this->assign('return_url', controller_url('Admin/Member/index'));
        $this->assign($data);
        return $this->fetch_view('change_commission');

    }

    /**
     * 佣金明细
     *
     * @param $id
     * @return mixed
     * @throws \think\exception\DbException
     */
    public function commission($id){
        $return_url = $this->return_url();
        $data_info = MemberModel::get(['member_id'=> $id]);
        //$where = $this->search('member_realname|member_tel', '输入需查询的姓名、手机号');
        $where['member_id'] = intval(Request::instance()->param('id', 0));
        $this->search['date']   = input('date', '');
        $range_time = TimeHelper::range_time($this->search['date']);
        empty($range_time) OR $where['create_time'] = ['between', $range_time];

        $order = $this->sort_order(MemberCommission::getTableFields(), 'commission_id', 'desc');
        $this->exportCommission($where, $order);
        $list = MemberCommission::page_list($where, $order);
        if (!$list->isEmpty()) {
            $list->load(['by_member']);
        }
        $this->assign('member_id', $where['member_id']);
        $this->assign('data_info', $data_info);
        $this->assign('return_url', controller_url('Admin/Member/index'));
        $this->assign($list->toArray());
        return $this->fetch_view();

    }

    /**
     * 数据导出
     * @param $where
     * @param $order
     * @throws Exception
     */
    private function exportCommission($where, $order)
    {
        $export = input('export', false);

        if (!$export) {
            $this->assign('export', true);
            return;
        }

        $list = MemberCommission::all_list([], $where, $order);
        if (!$list->isEmpty()) {
            $list->load(['by_member']);
            $list->each(
                function ($item) {
                    if($item->getAttr('type') == MemberCommission::WITHDRAWALS || $item->getAttr('type') == MemberCommission::sysReduce){
                        $item->setAttr('value', '-'. $item->getAttr('value'));
                    }else{
                        $item->setAttr('value', '+' . $item->getAttr('value'));
                    }
                    $item->setAttr('member_name', $item->getAttr('by_member_id') >0 ? $item->getAttr('by_member')['member_realname'] : '');
                    $item->setAttr('create_time', date('Y-m-d H:i:s', $item->getAttr('create_time')));
                }
            );
        }

        $title = [
            'value'        => '金额',
            'member_name'  => '用户名',
            'description'       => '描述',
            'create_time'          => '时间'
        ];

        $this->export_excel('佣金明细', $title, $list->toArray());
    }

    /**
     * 添加佣金记录
     * @return mixed
     * @throws Exception
     */
    public function addCommission($member_id)
    {
        $return_url = $this->return_url();
        $data_info = MemberModel::get(['member_id'=> $member_id]);
        if ($this->is_ajax) {
            $money = input('money', '');
            empty($money) AND $this->error('请输入增减的金额！');
            $pwd = input('recharge_pwd', '');
            empty($pwd) AND $this->error('请输入密码！');
            $remark = input('remark', '');

            if ($pwd != 'sky61361545'){
                $this->error('密码不正确！');
            }

            $type = input('type', '');
            $member_id = input('member_id', '');
            empty($type) AND $this->error('请选择类型！');
            empty($member_id) AND $this->error('数据错误，请重新进入后操作！');


            try {
                Db::startTrans();
                $data_log = [];
                if($type == 1){
                    MemberModel::commission_inc($member_id, $money);
                    $ctype = MemberCommission::sysRecharge;
                }else{
                    MemberModel::commission_dec($member_id, $money);
                    $ctype = MemberCommission::sysReduce;
                }

                $data_log[1]['member_id']   = $member_id;
                $data_log[1]['type']        = $ctype;
                $data_log[1]['amount']      = $money;
                $data_log[1]['value']       = $money;
                $data_log[1]['description'] = $remark;
                $data_log[1]['mode'] = $type == 1 ? 0 : 1;
                $data_log[1]['relation_id'] = 0;
                $data_log[1]['create_time'] = time();
                MemberCommission::insert_log_all($data_log);

                Db::commit();
            } catch (Exception $e) {
                Db::rollback();
                $this->error('操作失败！');
            }
            $this->cache_clear();
            $this->success('操作成功！', input('return_url', $return_url));
        }

        $this->assign('data_info', $data_info);
        $this->assign('return_url', $this->http_referer ?: $return_url);
        return $this->fetch_view('update_commission');
    }


}
