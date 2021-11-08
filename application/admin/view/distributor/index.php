{extend name="public/base" /}
{block name="main-content"}
<?php isset($order) OR $order = ['field' => '', 'dir' => false]; ?>
<?php isset($data_list) OR $data_list = []; ?>
<?php isset($sex) OR $sex = []; ?>

<div class="page-content">
    <div class="row maintop">
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
                    </div>
                </div>
            </form>
        <?php endif; ?>
    </div>

    <div class="row">
        <div class="col-xs-12">
            <form class="ajax-form" method="post" action="">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered table-hover" data-href="{$full_url}" data-field="{$order.field}" data-dir="{$order.dir? 1 : 0}">
                        <thead>
                            <tr>
                                <th>会员ID</th>
                                <th>手机号</th>
                                <th>昵称</th>
                                <th>email</th>
                                <th>性别</th>
                                <th>余额</th>
                                <th>佣金</th>
                                <th>注册时间</th>
                                <th class="th-operate">操作</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php foreach ($data_list as $v) : ?>
                                <tr>
                                    <td>{$v.invitation_code}</td>
                                    <td>{$v.member_tel}</td>
                                    <td>{$v.member_nickname}</td>
                                    <td>{$v.member_email}</td>
                                    <td>{$sex[$v.member_sex]}</td>
                                    <td>{$v.balance}</td>
                                    <td>{$v.commission}</td>
                                    <td>{:date('Y-m-d H:i:s',$v.create_time)}</td>
                                    <td>
                                        <div class="hidden-sm hidden-xs action-buttons action-buttons-list">
                                            <a href="{:folder_url('Distributor/distributor_level1',['member_id'=>$v.member_id])}" data-toggle="tooltip" title="下级代理">
                                                <span class="blue"><i class="ace-icon fa fa-user bigger-130"></i></span>
                                            </a>
                                            <a href="{:folder_url('DistributorOrder/index',['member_id'=>$v.member_id])}" data-toggle="tooltip" title="消费订单">
                                                <span class="blue"><i class="ace-icon fa fa-money bigger-130"></i></span>
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
