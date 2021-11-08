{extend name="public/base" /}
{block name="main-content"}
<?php isset($order) OR $order = ['field' => '', 'dir' => false]; ?>
<?php isset($data_list) OR $data_list = []; ?>
<?php isset($skip) OR $skip = []; ?>

<div class="page-content">
    <div class="row maintop">
        <div class="col-xs-12 col-sm-2 col-md-1 margintop5">
            <a href="{:controller_url(['add'])}" class="btn btn-sm btn-danger">添加</a>
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
                                <th>ID</th>
                                <th>活动名称</th>
                                <th>活动名额</th>
                                <th>报名名额</th>
                                <th>地址</th>
                                <th>图片</th>
                                <th>活动开始时间</th>
                                <th>活动结束时间</th>
                                <th>排序</th>
                                <th>启用</th>
                                <th class="th-operate">操作</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php foreach ($data_list as $v) : ?>
                                <tr>
                                    <td>{$v.id}</td>
                                    <td>{$v.name}</td>
                                    <td>{$v.num}</td>
                                    <td>{$v.people_num}</td>
                                    <td>{$v.province}{$v.city}{$v.area}{$v.address}</td>
                                    <td>
                                        <a href="{$v.image.full_url}" target="_blank"><img src="{$v.image.full_url}" width="70" height="50" alt=""/></a>
                                    </td>
                                    <td>{:date('Y-m-d H:i',$v.start_time)}</td>
                                    <td>{:date('Y-m-d H:i',$v.end_time)}</td>
                                    <td>{$v.sort}</td>
                                    <td>
                                        <a class="red open-btn" href="{:controller_url('change_enable')}" data-id="{$v.id}" data-status="{$v.enable}">
                                            <div>
                                                <button class="btn btn-minier btn-yellow none" title="已开启">
                                                    是
                                                </button>
                                                <button class="btn btn-minier btn-danger none" title="已禁用">
                                                    否
                                                </button>
                                            </div>
                                        </a>
                                    </td>
                                    <td>
                                        <div class="hidden-sm hidden-xs action-buttons action-buttons-list">
                                            <a href="{:controller_url(['edit'],['id'=>$v.id])}" data-toggle="tooltip" title="修改">
                                                <span class="green"><i class="ace-icon fa fa-pencil bigger-130"></i></span>
                                            </a>
                                            <a href="{:controller_url(['del'],['id'=>$v.id])}" class="confirm-rst-url-btn" data-info="你确定要删除吗？" data-toggle="tooltip" title="删除">
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
