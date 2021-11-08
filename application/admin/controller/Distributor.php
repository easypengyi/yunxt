<?php


namespace app\admin\controller;


use app\common\Constant;
use app\common\controller\AdminController;
use app\common\model\Member as MemberModel;
use app\common\model\MemberInvitation as MemberInvitationModel;
use Exception;

class Distributor extends AdminController
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
        $where   = $this->search('member_nickname|member_tel', '输入需查询的昵称、手机号');
        $where[] = ['exp', MemberInvitationModel::where_in_raw(['level' => 1], 'invitation_id', 'member_id')];
        $order   = $this->sort_order(MemberModel::getTableFields(), 'create_time', 'desc');

        $list = MemberModel::page_list($where, $order);
        if (!$list->isEmpty()) {
            $list->append(['group_name', 'invitation_code']);
        }
        $this->assign($list->toArray());
        return $this->fetch_view();
    }


    /**
     * 分销商一级列表
     * @return mixed
     * @throws Exception
     */
    public function distributor_level1()
    {
        $where[] = ['exp', MemberInvitationModel::where_in_raw(['invitation_id' => $this->member_id], 'member_id')];

        $order = $this->sort_order(MemberModel::getTableFields());

        $list = MemberModel::page_list($where, $order);

        $this->assign($list->toArray());
        $this->assign('return_url', folder_url('distributor/index'));
        return $this->fetch_view('', ['member_id']);
    }

    /**
     * 分销商二级列表
     * @return mixed
     * @throws Exception
     */
    public function distributor_level2()
    {
        $where[] = ['exp', MemberInvitationModel::where_in_raw(['invitation_id' => $this->member_id], 'member_id')];

        $order = $this->sort_order(MemberModel::getTableFields());

        $list = MemberModel::page_list($where, $order);

        $this->assign($list->toArray());
        $this->assign('return_url', folder_url('distributor/index'));
        return $this->fetch_view('', ['member_id']);
    }
}