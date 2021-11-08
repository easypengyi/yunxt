<?php

namespace app\common\model;

use think\db\Query;
use app\common\core\BaseModel;
use think\exception\DbException;
use think\model\relation\BelongsTo;

/**
 * 消息发送 模型
 */
class MessageSend extends BaseModel
{
    protected $type = [
        'content'      => 'base64',
        'del'          => 'boolean',
        'member_limit' => 'boolean',
        'expiry'       => 'boolean',
        'member_ids'   => 'plode',
    ];

    protected $insert = ['del' => false];

    protected $autoWriteTimestamp = true;

    //-------------------------------------------------- 静态方法

    /**
     * 发送消息数组
     * @param $member_id
     * @return array|static[]
     * @throws DbException
     */
    public static function send_list($member_id)
    {
        $where['del'] = false;

        $where[] = function (Query $query) {
            $query->where(['expiry' => true, 'expiry_time' => ['>', time()]]);
            $query->whereOr('expiry', false);
        };

        $where[] = function (Query $query) use ($member_id) {
            $query->where(['member_limit' => true, ['exp', self::find_in_set('member_ids', $member_id, 'OR')]]);
            $query->whereOr('member_limit', false);
        };

        $where[] = ['exp', MessageSendItem::where_not_in_raw(['member_id' => $member_id], 'send_id')];

        return self::all_list([], $where);
    }

    //-------------------------------------------------- 实例方法

    //-------------------------------------------------- 读取器方法

    //-------------------------------------------------- 追加属性读取器方法

    //-------------------------------------------------- 修改器方法

    /**
     * 关联管理员 修改器
     * @param Member $model
     * @return Member
     */
    public function setAdminRelation($model)
    {
        if (is_null($model)) {
            $model = new Admin(['admin_id' => 0, 'admin_username' => '']);
        }

        $this->hidden(['admin_id']);
        return $model;
    }

    //-------------------------------------------------- 关联加载方法

    /**
     * 关联管理员
     * @return BelongsTo
     */
    public function admin()
    {
        $relation = $this->belongsTo(Admin::class, 'admin_id');
        $relation->field(['admin_id', 'admin_username']);
        return $relation;
    }
}
