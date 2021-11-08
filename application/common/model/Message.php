<?php

namespace app\common\model;

use stdClass;
use think\Config;
use app\common\core\BaseModel;
use think\exception\DbException;
use think\exception\PDOException;
use think\model\relation\BelongsTo;
use think\Exception as ThinkException;

/**
 * 会员消息 模型
 */
class Message extends BaseModel
{
    // 类型
    // 系统
    const SYSTEM = 1;
    // 充值
    const RECHARGE = 2;
    // 商城
    const SHOP = 4;
    // 后台新订单
    const ADMIN_ORDER = 5;
    // 优惠券提醒
    const COUPON_REMIND = 8;
    //会员开通
    const HOME_VIP = 7;
    //前台订单支付完成
    const HOME_ORDER = 9;
    //上传报告
    const HOME_REPORT = 10;
    //佣金提现申请
    const COMMISSION = 11;

    // 状态
    // 状态-未处理
    const STATUS_NO_OPERATED = 0;
    // 状态-同意
    const STATUS_AGREE = 1;
    // 状态-拒绝
    const STATUS_REFUSE = 2;

    protected $type = ['del' => 'boolean', 'readed' => 'boolean'];

    protected $insert = ['del' => false, 'readed' => false, 'status' => self::STATUS_NO_OPERATED];

    protected $autoWriteTimestamp = true;

    //-------------------------------------------------- 静态方法

    /**
     * 会员消息列表
     * 分页
     * @param      $member_id
     * @param bool $readed
     * @param      $type
     * @return array
     * @throws DbException
     */
    public static function message_list($member_id, $type = 0, $readed = false)
    {
        $field = [
            'message_id',
            'message_type',
            'send_id',
            'message',
            'readed',
            'show_time',
            'relation_id',
        ];

        $where['del']       = false;
        $where['show_time'] = ['<=', time()];
        $where['member_id'] = $member_id;
        $where['readed'] = false;

        if (empty($type)) {
            $where['message_type'] = ['<>', self::COMMISSION];
        } else {
            $where['message_type'] = $type;
        }

        $readed AND $order['readed'] = 'asc';
        $order['show_time'] = 'desc';

        $query = self::field($field);
        $list  = self::page_list($where, $order, $query);

        if (!$list->isEmpty()) {
            $list->load(['SendMember']);
            $list->append(['extras']);
        }

        return $list->toArray();
    }

    /**
     * 消息详情
     * @param $message_id
     * @param $member_id
     * @return Message|false
     * @throws DbException
     */
    public static function message_detail($message_id, $member_id)
    {
        $field = [
            'message_id',
            'message_type',
            'send_id',
            'message',
            'readed',
            'status',
            'show_time',
            'relation_id',
        ];

        $where['message_id'] = $message_id;
        $where['member_id']  = $member_id;
        $where['show_time']  = ['<=', time()];
        $where['del']        = false;

        $query = self::field($field)->where($where);
        $model = self::get($query);

        if (empty($model)) {
            return null;
        }

        $model->eagerlyResult($model, ['SendMember']);
        $model->append(['extras']);

        return $model;
    }

    /**
     * 读取未操作消息
     * @param $message_id
     * @param $member_id
     * @return static
     * @throws DbException
     */
    public static function load_no_operated_message($message_id, $member_id)
    {
        $where['message_id'] = $message_id;
        $where['member_id']  = $member_id;
        $where['show_time']  = ['<=', time()];
        $where['status']     = self::STATUS_NO_OPERATED;
        $where['del']        = false;

        return self::get($where);
    }

    /**
     * 未读消息数量
     * @param      $member_id
     * @param null $type
     * @return int
     * @throws ThinkException
     */
    public static function message_no_read_number($member_id, $type = null)
    {
        $where['message_type'] = ['<>', self::COMMISSION];
        $where['del']          = false;
        $where['readed']       = false;
        $where['member_id']    = $member_id;
        $where['show_time']    = ['<=', time()];
        is_null($type) OR $where['message_type'] = $type;

        return self::where($where)->count();
    }

    /**
     * 消息已读设置
     * @param $message_id
     * @param $member_id
     * @return bool
     * @throws PDOException
     * @throws ThinkException
     */
    public static function message_readed($message_id, $member_id)
    {
        $where['message_type'] = ['<>', self::COMMISSION];
        $where['message_id']   = ['in', $message_id];
        $where['member_id']    = $member_id;
        $where['show_time']    = ['<=', time()];
        $where['del']          = false;

        return self::where($where)->update(['readed' => true]) != 0;
    }

    public static function commission_message_readed($id)
    {
        $where['relation_id'] = $id;
        return self::where($where)->update(['readed' => true]) != 0;
    }

    /**
     * 消息删除
     * @param $message_id
     * @param $member_id
     * @return bool
     * @throws PDOException
     * @throws ThinkException
     */
    public static function message_delete($message_id, $member_id)
    {
        $where['message_id'] = ['in', $message_id];
        $where['member_id']  = $member_id;
        $where['show_time']  = ['<=', time()];
        $where['del']        = false;

        return self::where($where)->update(['del' => true]) != 0;
    }

    /**
     * 添加充值消息
     * @param $member_id
     * @param $content
     * @param $relation_id
     * @return static
     */
    public static function recharge_message($member_id, $content, $relation_id)
    {
        is_array($content) OR $content = [$content];
        $content = vsprintf(Config::get('message.recharge'), $content);
        return self::insert_message($member_id, 0, $content, self::RECHARGE, $relation_id);
    }

    /**
     * 添加佣金提现申请记录
     * @param $member_id
     * @param $content
     * @param $relation_id
     * @return Message
     */
    public static function commission($member_id, $content, $relation_id)
    {
        is_array($content) OR $content = [$content];
        $content = vsprintf(Config::get('message.commission'), $content);
        return self::insert_message($member_id, 0, $content, self::COMMISSION, $relation_id);
    }

    /**
     * 添加商城购物消息
     * @param $member_id
     * @param $content
     * @param $relation_id
     * @return static
     */
    public static function shop_message($member_id, $content, $relation_id)
    {
        is_array($content) OR $content = [$content];
        $content = vsprintf(Config::get('message.shop'), $content);
        return self::insert_message($member_id, 0, $content, self::SHOP, $relation_id);
    }

    /**
     * 添加后台新订单消息
     * @param $member_id
     * @param $content
     * @param $relation_id
     * @return static
     */
    public static function admin_order_message($member_id, $content, $relation_id)
    {
        is_array($content) OR $content = [$content];
        $content = vsprintf(Config::get('message.admin_order'), $content);
        return self::insert_message(0, $member_id, $content, self::ADMIN_ORDER, $relation_id);
    }
    /**
     * 添加后台新订单消息
     * @param $member_id
     * @param $content
     * @param $relation_id
     * @return static
     */
    public static function admin1_order_message($member_id, $content, $relation_id)
    {
        is_array($content) OR $content = [$content];
        $content = vsprintf(Config::get('message.admin1_order'), $content);
        return self::insert_message(0, $member_id, $content, self::ADMIN_ORDER, $relation_id);
    }

    /**
     * 添加后台新订单消息
     * @param $member_id
     * @param $content
     * @param $relation_id
     * @return static
     */
    public static function admin2_order_message($member_id, $content, $relation_id)
    {
        is_array($content) OR $content = [$content];
        $content = vsprintf(Config::get('message.admin2_order'), $content);
        return self::insert_message(0, $member_id, $content, self::ADMIN_ORDER, $relation_id);
    }

    /**
     * 添加优惠券提醒消息
     * @param $member_id
     * @param $content
     * @param $relation_id
     * @param $show_time
     * @return static
     */
    public static function coupon_remind_message($member_id, $content, $relation_id, $show_time)
    {
        is_array($content) OR $content = [$content];
        $content = vsprintf(Config::get('message.coupon_remind'), $content);
        return self::insert_message($member_id, 0, $content, self::COUPON_REMIND, $relation_id, $show_time);
    }


    /**
     * 添加赠送优惠券提醒消息
     * @param $member_id
     * @return static
     */
    public static function home_coupon($member_id)
    {
        $content = Config::get('message.home_coupon');
        return self::insert_message($member_id, 0, $content, self::COUPON_REMIND);
    }


    /**
     * 添加会员开通信息
     * @param $member_id
     * @return Message
     */
    public static function home_vip($member_id)
    {
        $content = Config::get('message.home_vip');
        return self::insert_message($member_id, 0, $content, self::HOME_VIP);
    }

    /**
     * 添加上传报告通知信息
     * @param $member_id
     * @return Message
     */
    public static function home_report($member_id)
    {
        $content = Config::get('message.home_report');
        return self::insert_message($member_id, 0, $content, self::HOME_REPORT);
    }

    /**
     * 添加订单支付完成通知信息
     * @param $member_id
     * @return Message
     */
    public static function home_order($member_id)
    {
        $content = Config::get('message.home_order');
        return self::insert_message($member_id, 0, $content, self::HOME_ORDER);
    }

    /**
     * 添加订单支付完成通知信息
     * @param $member_id
     * @return Message
     */
    public static function home1_order($member_id)
    {
        $content = Config::get('message.home1_order');
        return self::insert_message($member_id, 0, $content, self::HOME_ORDER);
    }

    /**
     * 添加订单支付完成通知信息
     * @param $member_id
     * @return Message
     */
    public static function home2_order($member_id)
    {
        $content = Config::get('message.home2_order');
        return self::insert_message($member_id, 0, $content, self::HOME_ORDER);
    }


    /**
     * 添加消息
     * @param     $member_id
     * @param     $send_id
     * @param     $content
     * @param     $type
     * @param int $relation_id
     * @param int $show_time
     * @return static
     */
    public static function insert_message($member_id, $send_id, $content, $type, $relation_id = 0, $show_time = 0)
    {
        $data['member_id']    = $member_id;
        $data['send_id']      = $send_id;
        $data['message_type'] = $type;
        $data['message']      = $content;
        $data['relation_id']  = $relation_id;
        $data['show_time']    = empty($show_time) ? time() : $show_time;

        return self::create($data);
    }

    /**
     * 检查优惠券是否提醒
     * @param $member_id
     * @param $template_id
     * @return bool
     * @throws ThinkException
     */
    public static function coupon_check_exists($member_id, $template_id)
    {
        $where['del']          = false;
        $where['message_type'] = self::COUPON_REMIND;
        $where['member_id']    = $member_id;
        $where['relation_id']  = $template_id;
        return self::check($where);
    }

    //-------------------------------------------------- 实例方法

    //-------------------------------------------------- 读取器方法

    //-------------------------------------------------- 追加属性读取器方法

    /**
     * 附加信息 读取器
     * @param $value
     * @param $data
     * @return object
     */
    public function getExtrasAttr($value, $data)
    {
        if (!is_null($value)) {
            return $value;
        }

        switch ($data['message_type']) {
            default:
                $value = new stdClass();
                break;
        }

        $this->hidden(['relation_id']);
        return $value;
    }

    //-------------------------------------------------- 修改器方法

    /**
     * 消息发送 修改器
     * @param Member $model
     * @return Member
     * @throws DbException
     */
    public function setSendMemberRelation($model)
    {
        if (is_null($model)) {
            $data  = [
                'member_id'         => 0,
                'member_nickname'   => '系统消息',
                'member_tel'        => '',
                'member_headpic_id' => Configure::getValue('system_massage_image'),
            ];
            $model = new Member($data);
        }

        $model->hidden(['member_tel']);

        $this->hidden(['send_id']);
        return $model;
    }

    //-------------------------------------------------- 关联加载方法

    /**
     * 关联发帖会员  一对一 属于
     * 需关联帖子
     * @return BelongsTo
     */
    public function sendMember()
    {
        $relation = $this->belongsTo(Member::class, 'send_id');
        $relation->field(['member_id', 'member_nickname', 'member_tel', 'member_headpic_id']);
        return $relation;
    }
}
