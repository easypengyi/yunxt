<?php

namespace app\api\controller;

use think\Db;
use Exception;
use app\common\controller\ApiController;
use app\common\model\Message as MessageModel;

/**
 * 消息 API
 */
class Message extends ApiController
{
    /**
     * 会员消息列表接口
     * 分页
     * @return void
     * @throws Exception
     */
    public function message_list()
    {
        $type = $this->get_param('type', 0);
        $this->check_login();

        $list = MessageModel::message_list($this->member_id, $type);
        output_success('', $list);
    }

    /**
     * 消息详情接口
     * @return void
     * @throws Exception
     */
    public function message_detail()
    {
        $message_id = $this->get_param('message_id');
        $this->check_login();

        $detail = MessageModel::message_detail($message_id, $this->member_id);
        empty($detail) AND output_error('消息已删除！');
        MessageModel::message_readed($message_id, $this->member_id);
        output_success('', $detail);
    }

    /**
     * 消息已读设置接口
     * @return void
     * @throws Exception
     */
    public function message_readed()
    {
        $message_id = $this->get_param('message_id');
        $this->check_login();

        MessageModel::message_readed($message_id, $this->member_id);
        output_success();
    }

    /**
     * 消息处理接口
     * @return void
     * @throws Exception
     */
    public function message_operation()
    {
        $message_id = $this->get_param('message_id');
        $this->check_login();

        $message = MessageModel::load_no_operated_message($message_id, $this->member_id);
        empty($message) AND output_error('消息已处理！');

        try {
            Db::startTrans();
            switch ($message->getAttr('message_type')) {
                default:
                    $message->save(['status' => MessageModel::STATUS_AGREE]);
                    break;
            }
            Db::commit();
        } catch (Exception $e) {
            Db::rollback();
            throw $e;
        }
        output_success();
    }

    /**
     * 消息删除接口
     * @return void
     * @throws Exception
     */
    public function message_delete()
    {
        $message_id = $this->get_param('message_id');
        $this->check_login();

        MessageModel::message_delete($message_id, $this->member_id);
        output_success();
    }

    /**
     * 未读消息数量接口
     * @return void
     * @throws Exception
     */
    public function message_no_read_number()
    {
        $is_login = !empty($this->member_id);

        $number = $is_login ? MessageModel::message_no_read_number($this->member_id) : 0;
        output_success('', ['no_read_number' => $number, 'is_login' => $is_login]);
    }
}
