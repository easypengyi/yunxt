{extend name="public/base" /}
{block name="main-content"}
<?php isset($order) OR $order = ['field' => '', 'dir' => false]; ?>
<?php isset($data_list) OR $data_list = []; ?>
<?php isset($payment) OR $payment = []; ?>

<div class="page-content">
    <div class="row maintop">
        <div class="col-xs-12 col-sm-2 col-md-1 margintop5"></div>
        <?php if (isset($search) && $search): ?>
            <form class="form-search" method="get" action="{$current_url}">
                <div class="col-xs-12 col-sm-12 col-md-6 margintop5">
                </div>
                <div class="col-xs-12 col-sm-10 col-md-5 margintop5">
                    <div class="input-group">
                        <span class="input-group-addon"><i class="ace-icon fa fa-check"></i></span>
                        <input type="text" name="keyword" id="keyword" class="form-control" value="{$search.keyword}" placeholder="{$search.description}"/>
                        <a class="input-group-addon list-search" title="搜索">
                            <span class="ace-icon fa fa-search icon-on-right bigger-110"></span>
                        </a>
                        <a href="{$current_url}" class="input-group-addon" title="显示全部">
                            <span class="ace-icon fa fa-globe icon-on-right bigger-110"></span>
                        </a>
                        <?php if (isset($export) && $export): ?>
                            <a class="input-group-addon list-export" title="导出">
                                <span class="ace-icon fa fa-download icon-on-right bigger-110"></span>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </form>
        <?php endif; ?>
    </div>

    <div class="row">
        <div class="col-xs-12">
            <form class="ajax-form" method="post" action="{:controller_url('del')}">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered table-hover" data-href="{$full_url}" data-field="{$order.field}" data-dir="{$order.dir? 1 : 0}">
                        <thead>
                        <tr>
                            <th>订单号</th>
                            <th>用户名称</th>
                            <th>用户手机</th>
                            <th>银行账号</th>
                            <th>开户行</th>
                            <th>真实姓名</th>
                            <th>金额</th>
                            <th>手续费</th>
                            <th>提现金额</th>
                            <th>状态</th>
                            <th>时间</th>
                            <th>备注</th>
                            <!-- <th class="th-operate">操作</th> -->
                        </tr>
                        </thead>

                        <tbody>
                        <?php foreach ($data_list as $v) : ?>
                            <tr>
                                <td>{$v.order_sn}</td>
                                <td>{$v.member.member_realname}</td>
                                <td>{$v.member.member_tel}</td>
                                <td>{$v.account}</td>
                                <td>{$v.blank}</td>
                                <td>{$v.real_name}</td>
                                <td>{$v.amount}</td>
                                <td>{$v.service_money}</td>
                                <td>{$v.money}</td>
                                <td>{$status[$v.status]}</td>
                                <td>{:date('Y-m-d H:i:s',$v.order_time)}</td>
                                <td>{$v.remark}</td>
                                <!-- <td>
                                    <div class="hidden-sm hidden-xs action-buttons action-buttons-list">
                                        <?php if ($v['status'] == 2): ?>
                                            <a href="{:controller_url('examine',['id'=>$v.withdrawals_id])}" class="confirm-rst-url-btn" data-info="你确定完成吗？" data-toggle="tooltip" title="确认完成">
                                                <span class="green"><i class="ace-icon fa fa-pencil bigger-130"></i></span>
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                    <div class="hidden-md hidden-lg">
                                        <div class="inline position-relative">
                                            <button class="btn btn-minier btn-primary dropdown-toggle" data-toggle="dropdown" data-position="auto">
                                                <i class="ace-icon fa fa-cog icon-only bigger-110"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-only-icon dropdown-yellow dropdown-menu-right dropdown-caret dropdown-close"></ul>
                                        </div>
                                    </div>
                                </td> -->
                            </tr>
                        <?php endforeach; ?>
                        <tr>
                            <td colspan="100" align="left">{$page}</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </form>
        </div>
    </div>
</div>
{/block}

{block name="scripts"}
{/block}
