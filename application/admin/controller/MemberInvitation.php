<?php

namespace app\admin\controller;

use app\common\model\MemberGroupRelation;
use Exception;
use app\common\Constant;
use app\common\controller\AdminController;
use app\common\model\Member as MemberModel;
use app\common\model\MemberInvitation as MemberInvitationModel;
use think\Session;

/**
 * 会员邀请 模块
 */
class MemberInvitation extends AdminController
{
    protected $member_id = 0;

    /**
     * 初始化方法
     * @return void
     * @throws Exception
     */
    public function _initialize()
    {
        parent::_initialize();

        $this->member_id    = input('member_id', '');
        $this->param['sex'] = Constant::sex_array();
    }

    /**
     * 列表
     * @return mixed
     * @throws Exception
     */
    public function index()
    {
        $where = ['top_id'=>$this->member_id];
        $order = $this->sort_order(MemberModel::getTableFields());

        $list = MemberGroupRelation::page_list($where, $order);
        if (!$list->isEmpty()) {
            $list->load(['member']);
            $list->append(['group_name','team_number']);
        }

        foreach ($list as $k=>$v){
            $v['commission'] = number_format( $v['member']['commission'],2);
        }
        $this->assign($list->toArray());
        $top  = MemberGroupRelation::get_top($this->member_id);
        $session_member = Session::get('member_index');
        if(empty($top['top_id']) || ($session_member == $this->member_id || empty($session_member))){
            $return_url = folder_url('Member/index', ['page'=>Session::get('member_page')]);
        }else{
            $return_url = folder_url('MemberInvitation/index', ['member_id'=>$top['top_id']]);
        }

        //缓存
        $ret_url = $this->http_referer;
        if(strpos($ret_url,"member/index")){
            Session::set('member_index', $this->member_id);
        }


        $this->assign('return_url', $return_url);
        return $this->fetch_view('', ['member_id']);
    }

    /**
     * 列表
     * @return mixed
     * @throws Exception
     */
    public function index1()
    {
        $where = ['top_id'=>$this->member_id];
        $order = $this->sort_order(MemberModel::getTableFields());

        $list = MemberModel::page_list($where, $order);
        if (!$list->isEmpty()) {
            $list->append(['group_name']);
        }

        foreach ($list as $k=>$v){
            $v['commission'] = $v['commission']+$v['commission1'];
            $v['commission'] = number_format( $v['commission'],2);
        }
        $this->assign($list->toArray());
        $this->assign('return_url', folder_url('Member/index'));
        return $this->fetch_view('', ['member_id']);
    }
}
