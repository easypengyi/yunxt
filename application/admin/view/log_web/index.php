{extend name="public/base" /}
{block name="main-content"}
<?php isset($order) OR $order = ['field' => '', 'dir' => false]; ?>
<?php isset($data_list) OR $data_list = []; ?>

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
                        <a href="{:controller_url('drop')}" class="input-group-addon" title="清空">
                            <span class="ace-icon fa fa-trash-o icon-on-right bigger-110"></span>
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
                                <th>详情</th>
                                <th>模块</th>
                                <th>请求类型</th>
                                <th>操作地址</th>
                                <th>操作时间</th>
                                <th class="th-operate">操作</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php foreach ($data_list as $v) : ?>
                                <tr>
                                    <td class="center">
                                        <div class="action-buttons">
                                            <a href="#" class="green bigger-140 show-details-btn" title="Show Details">
                                                <i class="ace-icon fa fa-angle-double-down"></i>
                                                <span class="sr-only">Details</span>
                                            </a>
                                        </div>
                                    </td>
                                    <td>{$v.module}</td>
                                    <td>{$v.method}</td>
                                    <td>{$v.url}</td>
                                    <td class="hidden-xs">{:date('Y-m-d H:i:s',$v.create_time)}</td>
                                    <td>
                                        <div class="hidden-sm hidden-xs action-buttons action-buttons-list">
                                            <a href="{:controller_url('del',['id'=>$v.id])}" class="confirm-rst-url-btn" data-toggle="tooltip" title="删除" data-info="你确定要删除吗？">
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
                                <tr class="detail-row">
                                    <td colspan="100">
                                        <div class="row">
                                            <label class="form-label col-xs-3 text-right">操作IP：</label>
                                            <div class="formControls col-xs-8">
                                                {$v.ip}
                                            </div>
                                        </div>
                                        <div class="row">
                                            <label class="form-label col-xs-3 text-right">操作地点：</label>
                                            <div class="formControls col-xs-8">
                                                {$v.location}
                                            </div>
                                        </div>
                                        <div class="row">
                                            <label class="form-label col-xs-3 text-right">操作系统：</label>
                                            <div class="formControls col-xs-8">
                                                {$v.os}
                                            </div>
                                        </div>
                                        <div class="row">
                                            <label class="form-label col-xs-3 text-right">操作浏览器：</label>
                                            <div class="formControls col-xs-8">
                                                {$v.browser}
                                            </div>
                                        </div>
                                        <div class="row">
                                            <label class="form-label col-xs-3 text-right">操作数据：</label>
                                            <div class="formControls col-xs-8">
                                                <textarea readonly="readonly" rows="3" class="col-xs-12" id="form-field-9" title="" placeholder="">{:var_export(unserialize($v.data))}</textarea>
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
