{extend name="public/base" /}
{block name="main-content"}
<?php isset($order) OR $order = ['field' => '', 'dir' => false]; ?>
<?php isset($data_list) OR $data_list = []; ?>
<?php isset($sex) OR $sex = []; ?>

<div class="page-content">
    <div class="row maintop">
        <div class="col-xs-12 col-sm-2 col-md-1 margintop5">
            <a href="{:controller_url('add')}" class="btn btn-sm btn-danger">添加</a>
        </div>
        <?php if (isset($search) && $search): ?>
            <form class="form-search" method="get" action="{$current_url}">
                <div class="col-xs-12 col-sm-12 col-md-6 ">
                    <label for="status">
                        <select name="status" id="status" data-href="{:folder_url('Ajax/select_group',['id'=>$search.status])}" class="col-xs-12 col-sm-12 selectpicker select-ajax " title="按用户组"></select>
                    </label>
                    <label for="stocks">
                        <select name="stocks" id="stocks" data-href="{:folder_url('Ajax/select_stock',['id'=>$search.stocks])}" class="col-xs-12 col-sm-12 selectpicker select-ajax " title="云库存"></select>
                    </label>
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
            <form class="ajax-form" method="post" action="">
                <div class="table-responsive">
                    <table  class="table table-striped table-bordered table-hover" data-href="{$full_url}" data-field="{$order.field}" data-dir="{$order.dir? 1 : 0}">
                        <thead>
                            <tr>
                                <th>姓名</th>
                                <th>职位</th>
                                <th>UID</th>
                                <th>头像</th>
                                <th>手机号</th>
                                <th>云库存</th>
                                <th>奖金币</th>
                                <th>直推人</th>
                                <th>团队人数</th>
                                <th>启用</th>
                                <th class="th-operate">操作</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php foreach ($data_list as $v) : ?>
                                <tr>
                                    <td>{$v.member_realname}</td>
                                    <td>{$v.group_name}</td>
                                    <td>{$v.invitation_code}</td>
                                    <td>
                                        <a href="{$v.member_headpic.full_url}" target="_blank"><img src="{$v.member_headpic.full_url}" width="50" height="50" alt=""/></a>
                                    </td>
                                    <td>{$v.member_tel}</td>
                                    <td>{$v.balance}</td>
                                    <td>{$v.commission}</td>
                                    <td>{$v.top_name}</td>
                                    <td>{$v.team_number}</td>
                                    <td>
                                        <a class="red open-btn" href="{:controller_url('change_enable')}" data-id="{$v.member_id}" data-status="{$v.enable}">
                                            <div>
                                                <button class="btn btn-minier btn-yellow none" title="已开启">
                                                    开启
                                                </button>
                                                <button class="btn btn-minier btn-danger none" title="已禁用">
                                                    禁用
                                                </button>
                                            </div>
                                        </a>
                                    </td>
                                    <td>
                                        <div class="hidden-sm hidden-xs action-buttons action-buttons-list">

                                            <a href="{:folder_url('MemberInvitation/index',['member_id'=>$v.member_id])}" data-toggle="tooltip" title="我的团队">
                                                <span class="blue"><i class="ace-icon fa fa-user bigger-130"></i></span>
                                            </a>
                                            <a href="{:controller_url('edit',['id'=>$v.member_id])}" data-toggle="tooltip" title="修改">
                                                <span class="green"><i class="ace-icon fa fa-pencil bigger-130"></i></span>
                                            </a>
                                            <a href="{:controller_url('recharge',['id'=>$v.member_id])}" data-toggle="tooltip" title="充值">
                                                <span class="red"><i class="ace-icon fa fa-plus-square bigger-130"></i></span>
                                            </a>
                                            <a href="{:controller_url('reduce',['id'=>$v.member_id])}" data-toggle="tooltip" title="缩减">
                                                <span class="red"><i class="ace-icon fa fa-minus-square bigger-130"></i></span>
                                            </a>
                                            <a href="{:controller_url('stock',['id'=>$v.member_id])}" data-toggle="tooltip" title="库存明细">
                                                <span class="dark"><i class="ace-icon fa fa-bars bigger-130"></i></span>
                                            </a>
                                            <a href="{:controller_url('commission',['id'=>$v.member_id])}" data-toggle="tooltip" title="佣金明细">
                                                <span class="dark"><i class="ace-icon fa fa-money bigger-130"></i></span>
                                            </a>
                                            <a href="{:controller_url('changeCommission',['id'=>$v.member_id])}" data-toggle="tooltip" title="产生佣金">
                                                <span class="dark"><i class="ace-icon fa fa-credit-card-alt"></i></span>
                                            </a>

                                            <a href="{:controller_url('del',['id'=>$v.member_id])}" class="confirm-rst-url-btn" data-info="你确定要删除吗？" data-toggle="tooltip" title="删除">
                                                <span class="red"><i class="ace-icon fa fa-trash-o bigger-130"></i></span>
                                            </a>
                                        </div>
                                        <div class="hidden-md hidden-lg">
                                            <div class="inline position-relative">
                                                <button class="btn btn-minier btn-primary dropdown-toggle" data-toggle="dropdown" data-position="auto">
                                                    <i class="ace-icon fa fa-cog icon-only bigger-110"></i>
                                                </button>
                                                <ul class="dropdown-menu dropdown-only-icon dropdown-yellow dropdown-menu-right dropdown-caret dropdown-close"></ul>
                                            </div>
                                        </div>
                                    </td>
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
