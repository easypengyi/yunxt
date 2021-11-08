<?php

namespace app\api\controller;


use app\common\model\Card as CardModel;
use app\common\model\Member as MemberModel;
use app\common\model\MemberActivity;
use app\common\model\MemberActivity as MemberActivityModel;
use app\common\controller\ApiController;



/**
 * 活动 API
 */
class Card extends ApiController
{


    /**
     * 城市列表
     */
    public function city_list(){
        $where['del'] = 0;
        $where['enable'] = 1;
        $arr = CardModel::where($where)->column('city');
        $new_arr =  array_unique($arr);
        output_success('', $new_arr);
    }


    /**
     * 活动列表
     * @throws \think\exception\DbException
     */
    public function activity_list(){
        $category = $this->get_param('category');
        $cityName = $this->get_param('cityName');
        $this->check_login();
         switch ($category) {
             case 1:
                 $where['type'] = 1;
                 $where['city'] = $cityName;
                 break;
             case 2:
                 $id = MemberActivity::where(['member_id'=>$this->member_id,'is_use'=>false])->column('activity_id');
                 $where['id'] = ['in',$id];
                 break;
             case 3:
                 $id = MemberActivity::where(['member_id'=>$this->member_id,'is_use'=>true])->column('activity_id');
                 $where['id'] = ['in',$id];
                 break;
         }
        $where['del'] = false;
        $where['enable'] = true;
        $list = CardModel::activity_list($where);
        output_success('', $list);
    }

    /**
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function enroll(){
        $id = $this->get_param('id');
        $this->check_login();
        $detail = CardModel::detail($id);
        empty($detail) AND output_error('活动已结束！');
        $res = MemberActivityModel::where(['member_id'=>$this->member_id,'activity_id'=>$id])->find();
        if ($res){
            output_error('您已经报名该活动，请勿重复报名！');
        }else{
            $is = MemberModel::where(['member_id'=>$this->member_id])->find()['is_zige'];
            if ($is){
                $data['member_id']    = $this->member_id;
                $data['activity_id']    = $id;
                $data['create_time']    = time();
                MemberActivityModel::create($data);
                MemberModel::where(['member_id'=>$this->member_id])->setDec('is_zige',1);
                CardModel::where(['id'=>$id])->setInc('people_num',1);
                output_error('报名成功！');
            }else{
                output_error('没有报名资格！',1);
            }

        }

    }


    /**
     * @throws \think\exception\DbException
     */
    public function detail()
    {
        $id = $this->get_param('id');
        $detail = CardModel::detail($id);
        empty($detail) AND output_error('活动已结束！');
        output_success('', $detail);
    }






}
