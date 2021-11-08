<?php

namespace app\admin\controller;

use Exception;
use helper\StrHelper;
use app\common\controller\AdminController;
use app\common\model\Configure as ConfigureModel;
use app\common\model\UploadFile as UploadFileModel;

/**
 * 配置 模块
 */
class Configure extends AdminController
{
    private $configure_list = [];

    private $hidden = [];

    /**
     * 初始化方法
     * @return void
     * @throws Exception
     */
    public function _initialize()
    {
        parent::_initialize();

        $this->configure_list = [
            'sms_effective_time'         => '短信验证码有效期(秒)',
            'sms_interval_time'          => '短信验证码获取间隔(分钟)',
            'default_order_timeout_time' => '默认订单超时时间(分钟)',
            'message_expiry_day'         => '消息有效期(天)',
            'withdrawal_service_ratio'   => '提现手续费(%)',
            'withdrawal_mini_amount'     => '最低提现金额',
            'system_massage_image'       => '系统消息图片',
            'default_head_image'         => '默认头像图片',
            'register_agreement'         => '报单协议',
            'contact_us'                 => '企业介绍',
            'contact_us1'                 => '企业文化',
            'contact_us2'                 => '企业战略',
            'contact_us3'                 => '企业资质',
            'contact_us4'                 => '商业模式',
            'first_commission'            => '全球合伙人奖金',
            'second_commission'           => '董事奖金',
            'three_commission'            => '城市合伙人奖金',
        ];

        $this->hidden = ['default_order_timeout_time', 'system_massage_image'];
    }

    /**
     * 列表
     * @return mixed
     * @throws Exception
     */
    public function index()
    {
        $all = boolval(input('all', false));

        if (!$all) {
            foreach ($this->hidden as $v) {
                unset($this->configure_list[$v]);
            }
        }

        $this->assign('configure_list', $this->configure_list);
        return $this->fetch_view();
    }

    /**
     * 配置
     * @param $name
     * @return mixed
     * @throws Exception
     */
    public function config($name)
    {
        array_key_exists($name, $this->configure_list) OR $this->error('数据不存在！');

        $return_url = $this->return_url();
        if ($this->is_ajax) {
            $data = $this->edit_data($name);
            ConfigureModel::setValue($name, $data, $this->configure_list[$name]);
            $this->success('信息修改成功！', input('return_url', $return_url));
        }

        $this->assign('return_url', $this->http_referer ?: $return_url);
        $this->assign('configure_desc', $this->configure_list[$name]);
        $this->assign('configure_value', $this->output_data($name));
        return $this->fetch_view($this->template_name($name));
    }

    /**
     * 模板名称获取
     * @param $name
     * @return string
     */
    private function template_name($name)
    {
        switch ($name) {
            case 'service_phone':
            case 'sms_interval_time':
            case 'sms_effective_time':
            case 'message_expiry_day':
            case 'withdrawal_mini_amount':
            case 'withdrawal_service_ratio':
            case 'default_order_timeout_time':
            case 'first_agent_ratio':
            case 'second_agent_ratio':
            case 'three_agent_ratio' :
            case 'four_agent_ratio' :
            case 'teacher_agent_ratio':
            case 'myself_agent_ratio' :
            case 'boss1_agent_ratio' :
            case 'boss2_agent_ratio' :
            case 'first_commission' :
            case 'second_commission' :
            case 'three_commission' :
                $template = 'config';
                break;
            case 'register_agreement':
            case 'contact_us':
            case 'contact_us1':
            case 'contact_us2':
            case 'contact_us3':
            case 'contact_us4':
                $template = 'editor';
                break;
            case 'default_head_image':
            case 'system_massage_image':
                $template = 'image';
                break;
            default:
                $this->error('类型不存在！');
                $template = '';
                break;
        }
        return $template;
    }

    /**
     * 输出数据
     * @param $name
     * @return mixed
     * @throws Exception
     */
    private function output_data($name)
    {
        $data = ConfigureModel::getValue($name);
        switch ($name) {
            case 'default_head_image':
            case 'system_massage_image':
                $data = UploadFileModel::file_info($data);
                break;
            default:
                break;
        }
        return $data;
    }

    /**
     * 编辑数据
     * @param $name
     * @return mixed
     * @throws Exception
     */
    private function edit_data($name)
    {
        $value = input('configure_value', '');
        switch ($name) {
            case 'sms_interval_time':
            case 'sms_effective_time':
            case 'message_expiry_day':
            case 'default_order_timeout_time':
            case 'register_agreement':
            case 'contact_us':
            case 'contact_us1':
            case 'contact_us2':
            case 'contact_us3':
            case 'contact_us4':
            case 'withdrawal_mini_amount':
                $value = floatval($value);
                $value < 0.1 AND $this->error('最低提现金额小于0.1元！');
                break;
            case 'first_commission' :
            case 'second_commission' :
            case 'three_commission' :
            case 'first_agent_ratio':
            case 'second_agent_ratio':
            case 'three_agent_ratio' :
            case 'four_agent_ratio' :
            case 'teacher_agent_ratio':
            case 'myself_agent_ratio' :
            case 'boss1_agent_ratio' :
            case 'boss2_agent_ratio' :
            case 'withdrawal_service_ratio':
                $value = floatval($value);
                $value <= 0 AND $this->error('请勿设置小于等于0的数值！');
                $value = StrHelper::price_str($value);
                break;
            case 'default_head_image':
            case 'system_massage_image':
                if ($this->request->file()) {
                    $image = $this->upload_thumb(true, ['image']);
                    $image['status'] OR $this->error($image['message']);

                    $value = $image['data']['file_id'];

                    UploadFileModel::change_file($value, ConfigureModel::getValue($name));
                } else {
                    $this->error('请上传图片!');
                    $value = 0;
                }
                break;
            default:
                $value = '';
                break;
        }
        return $value;
    }
}
