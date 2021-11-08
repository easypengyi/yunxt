<?php isset($data_list) OR $data_list = []; ?>
<?php isset($message_total) OR $message_total = 0; ?>

<a data-toggle="dropdown" class="dropdown-toggle" href="#">
    <i class="ace-icon fa fa-envelope icon-animated-vertical"></i>
    <?php if (!empty($message_total)): ?>
        <span class="badge badge-success">{$message_total}</span>
    <?php endif; ?>
</a>
<ul class="dropdown-menu-right dropdown-navbar dropdown-menu dropdown-caret dropdown-close">
    <li class="dropdown-header">
        <i class="ace-icon fa fa-envelope-o"></i>
        {$message_total}条新消息
    </li>
    <?php foreach ($data_list as $v) : ?>
        <li class="dropdown-content ace-scroll" style="position: relative;">
            <div class="scroll-track none">
                <div class="scroll-bar"></div>
            </div>
            <div class="scroll-content" style="max-height: 200px;">
                <ul class="dropdown-menu dropdown-navbar">
                    <li>
                        <a href="{:folder_url('Common/head_message_list')}" class="clearfix detail-btn">
                            <img src="{$v.send_member.member_headpic.full_url}" class="msg-photo" alt=""/>
                            <span class="msg-body">
                                <span class="msg-title">
                                    {$v.message}
                                </span>

                                <span class="msg-time">
                                    <i class="ace-icon fa fa-clock-o"></i>
                                    <span>{:date('Y-m-d H:i:s', $v.show_time)}</span>
                                </span>
                            </span>
                        </a>
                    </li>
                </ul>
            </div>
        </li>
    <?php endforeach; ?>
    <li class="dropdown-footer">
        <a href="{:folder_url('Common/head_message_list')}" class="detail-btn">
            查看更多
            <i class="ace-icon fa fa-arrow-right"></i>
        </a>
    </li>
</ul>
