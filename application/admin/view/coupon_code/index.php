{extend name="public/base" /}
{block name="main-content"}
<?php isset($order) OR $order = ['field' => '', 'dir' => false]; ?>
<?php isset($data_list) OR $data_list = []; ?>

<div class="page-content">
    <div class="row maintop">
        <div class="col-xs-12 col-sm-2 col-md-1 margintop5">
            <a href="{:controller_url('add',['template_id'=>$param_array.template_id])}" class="btn btn-sm btn-danger">礼券码</a>
        </div>
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
            <form class="ajax-form" method="post" action="">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered table-hover" data-href="{$full_url}" data-field="{$order.field}" data-dir="{$order.dir? 1 : 0}">
                        <thead>
                        <tr>
                            <th>礼券码</th>
                            <th>会员昵称</th>
                            <th>会员手机</th>
                            <th>状态</th>
                            <th>领取时间</th>
                        </tr>
                        </thead>

                        <tbody>
                        <?php foreach ($data_list as $v) : ?>
                            <tr>
                                <td>{$v.activation_code}</td>
                                <td>{$v.member.member_nickname}</td>
                                <td>{$v.member.member_tel}</td>
                                <td>{$status[$v.status]}</td>
                                <td>{$v.receive_time ? date('Y-m-d H:i:s',$v.receive_time) : ''}</td>
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
