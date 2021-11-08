{extend name="public/base" /}
{block name="main-content"}
<?php isset($order) OR $order = ['field' => '', 'dir' => false]; ?>
<?php isset($data_list) OR $data_list = []; ?>

<div class="page-content">
    <div class="row maintop">
        <div class="col-xs-12 col-sm-2 col-md-1 margintop5">
            <a href="{:controller_url('add')}" class="btn btn-sm btn-danger">添加</a>
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
                                <th>名称</th>
                                <th>满足金额</th>
                                <th>优惠金额</th>
                                <th>赠送数量</th>
                                <th>可领取数量</th>
                                <th>已领取数量</th>
                                <th>单会员领取数量</th>
                                <th>领取限制</th>
                                <th>商品限制</th>
                                <th>时间限制</th>
                                <th>活动赠送</th>
                                <th>启用</th>
                                <th class="th-operate">操作</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php foreach ($data_list as $v) : ?>
                                <tr>
                                    <td>{$v.coupon_name}</td>
                                    <td>{$v.fill}</td>
                                    <td>{$v.value}</td>
                                    <td>{$v.send_number}</td>
                                    <td>{$v.receive_number}</td>
                                    <td>{$v.already_receive_number}</td>
                                    <td>{$v.number_limit}</td>
                                    <td>{$v.receive_limit ? date('Y-m-d H:i',$v.start_receive_time) : '否'}</td>
                                    <td>{$v.product_limit ? '是' : '否'}</td>
                                    <td>{$v.time_limit ? date('Y-m-d H:i',$v.start_time).'~'.date('Y-m-d H:i',$v.end_time) : '否'}</td>
                                    <td>{$v.activity_send ? '是' : '否'}</td>
                                    <td>
                                        <a class="red open-btn" href="{:controller_url('change_enable')}" data-id="{$v.template_id}" data-status="{$v.enable}">
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
                                            <a href="{:folder_url('CouponCode/index',['template_id'=>$v.template_id])}" data-toggle="tooltip" title="添加礼券码">
                                                <span class="blue"><i class="ace-icon fa fa-ticket bigger-130"></i></span>
                                            </a>
                                            <a href="{:folder_url('CouponGive/index',['template_id'=>$v.template_id])}" data-toggle="tooltip" title="赠送优惠券">
                                                <span class="blue"><i class="ace-icon fa fa-send bigger-130"></i></span>
                                            </a>
                                            <a href="{:controller_url('edit',['id'=>$v.template_id])}" data-toggle="tooltip" title="修改">
                                                <span class="green"><i class="ace-icon fa fa-pencil bigger-130"></i></span>
                                            </a>
                                            <a href="{:controller_url('del',['id'=>$v.template_id])}" class="confirm-rst-url-btn" data-info="你确定要删除吗？" data-toggle="tooltip" title="删除">
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
