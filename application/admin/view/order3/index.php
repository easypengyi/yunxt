{extend name="public/base" /}
{block name="main-content"}
<?php isset($order) OR $order = ['field' => '', 'dir' => false]; ?>
<?php isset($data_list) OR $data_list = []; ?>
<?php isset($payment) OR $payment = []; ?>
<?php isset($status) OR $status = []; ?>

<div class="page-content">
    <div class="row maintop">
        <div class="col-xs-12 col-sm-2 col-md-1 margintop5"></div>
        <?php if (isset($search) && $search): ?>
            <form class="form-search" method="get" action="{$current_url}">
                <div class="col-xs-12 col-sm-12 col-md-6 margintop5">
                    <div class="col-xs-12 col-sm-12 col-md-9">
                        <div class="input-group">
                            <span class="input-group-addon"><i class="ace-icon fa fa-list-alt"></i></span>
                            <input type="text" name="date" id="date" value="{$search.date}" placeholder="时间" class="form-control"/>
                            <a class="input-group-addon list-search" title="搜索">
                                <span class="ace-icon fa fa-search icon-on-right bigger-110"></span>
                            </a>
                        </div>
                    </div>
                    <div class="col-xs-12 col-sm-12 col-md-3">
                        <label for="status">
                            <select name="status" id="status" data-href="{:folder_url('Ajax/select_order3_status',['id'=>$search.status])}" class="col-xs-10 col-sm-5 selectpicker select-ajax" title="按状态"></select>
                        </label>
                    </div>
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

    <div class="row maintop">
        <div class="col-xs-12 col-sm-12 col-md-5 margintop5">
            <h3 class="blue">总计：{$total_money}元</h3>
        </div>
    </div>

    <div class="row">
        <div class="col-xs-12">
            <form class="ajax-form" method="post" action="{:controller_url('del')}">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered table-hover" data-href="{$full_url}" data-field="{$order.field}" data-dir="{$order.dir? 1 : 0}">
                        <thead>
                            <tr>
                                <th>订单号</th>
                                <th>订单名称</th>
                                <th>订单价格</th>
                                <th>支付方式</th>
                                <th>用户手机号</th>
                                <th>用户姓名</th>
                                <th>状态</th>
                                <th>下单时间</th>
                                <th class="th-operate">操作</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php foreach ($data_list as $v) : ?>
                                <tr>
                                    <td>{$v.order_sn}</td>
                                    <td>{$v.product_name}</td>
                                    <td>{$v.amount}</td>
                                    <td>{$v.payment_id ? $payment[$v.payment_id] : ''}</td>
                                    <td>{$v.user_phone}</td>
                                    <td>{$v.user_name}</td>
                                    <td>{$status[$v.status]}</td>
                                    <td>{:date('Y-m-d',$v.order_time)}</td>
                                    <td>
                                        <div class="hidden-sm hidden-xs action-buttons action-buttons-list">
                                            <a href="{:folder_url('Common/activity_detail',['id'=>$v.order_id])}" class="detail-btn" data-toggle="tooltip" title="详情">
                                                <span class="blue"><i class="ace-icon fa fa-sticky-note bigger-130"></i></span>
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
<script src="__STATIC__/laydate/dist-5.0.9/laydate.js"></script>
<script>
    $(function () {
        laydate.render({
            elem: '#date',
            range: true
        });

        $('.selectpicker').selectpicker({
            width: 120
        });
    });
</script>
{/block}
