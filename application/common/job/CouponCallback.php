<?php

namespace app\common\job;

use think\Db;
use Exception;
use think\queue\Job;
use app\common\model\Member as MemberModel;
use app\common\model\CouponGive as CouponGiveModel;
use app\common\model\MemberCoupon as MemberCouponModel;
use app\common\model\CouponTemplate as CouponTemplateModel;

/**
 * 优惠券赠送处理
 */
class CouponCallback
{
    /**
     * 执行方法
     * @param Job $job
     * @param     $data
     * @return void
     * @throws Exception
     */
    public function fire(Job $job, $data)
    {
        if ($job->attempts() > 3) {
            $job->delete();
            return;
        }

        $give_id = $data['give_id'];

        $result = CouponGiveModel::give_handle($give_id);
        if (!$result) {
            $job->delete();
            return;
        }

        $template = CouponTemplateModel::get($data['template_id']);
        if (empty($template)) {
            CouponGiveModel::give_fail($give_id);
            $job->delete();
            return;
        }

        $number     = 0;
        $member_ids = [];

        try {
            Db::startTrans();

            $where['del'] = false;
            $data['member_all'] OR $where['member_id'] = ['in', $data['member_ids']];

            $template_data = $template->create_coupon_data();
            MemberModel::where($where)->field(['member_id'])->chunk(
                100,
                function ($data_list) use ($template_data, &$number, &$member_ids) {
                    // TODO 添加优化 批量添加
                    /** @var MemberModel[] $data_list */
                    foreach ($data_list as $v) {
                        if (MemberCouponModel::coupon_insert($template_data, $v['member_id'], true)) {
                            $number++;
                            $member_ids[] = $v['member_id'];
                        }
                    }
                    return true;
                },
                'member_id'
            );
            Db::commit();
        } catch (Exception $e) {
            Db::rollback();
            CouponGiveModel::give_fail($give_id);
            $job->delete();
            return;
        }

        CouponGiveModel::give_finish($give_id, $number, $member_ids);
        $job->delete();
    }
}