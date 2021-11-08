<?php

namespace app\common\model;

use app\common\core\BaseModel;
use think\model\relation\BelongsTo;

/**
 * 会员反馈 模型
 */
class Feedback extends BaseModel
{
    protected $type = ['content' => 'base64'];

    protected $file = ['image_ids' => ['image', true]];

    protected $autoWriteTimestamp = true;

    //-------------------------------------------------- 静态方法

    /**
     * 添加会员反馈
     * @param $member_id
     * @param $content
     * @param $app_type
     * @param $image_ids
     * @return static
     */
    public static function insert_feedback($member_id, $content, $app_type, $image_ids)
    {
        $data['member_id']   = $member_id;
        $data['content']     = $content;
        $data['app_type']    = $app_type;
        $data['image_ids']   = $image_ids;
        $data['create_time'] = time();

        return self::create($data);
    }

    //-------------------------------------------------- 实例方法

    //-------------------------------------------------- 读取器方法

    //-------------------------------------------------- 追加属性读取器方法

    //-------------------------------------------------- 修改器方法

    /**
     * 关联会员 修改器
     * @param $model
     * @return mixed
     */
    public function setMemberRelation($model)
    {
        $this->hidden(['member_id']);
        return $model;
    }

    //-------------------------------------------------- 关联加载方法

    /**
     * 关联会员
     * @return BelongsTo
     */
    public function member()
    {
        $relation = $this->belongsTo(Member::class, 'member_id');
        $relation->field(['member_id', 'member_nickname', 'member_tel', 'member_headpic_id']);
        return $relation;
    }
}
