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
                                <th>会员昵称</th>
                                <th>会员手机号</th>
                                <th>优惠券优惠</th>
                                <th>商品价格</th>
                                <th>订单价格</th>
                                <th>支付方式</th>
                                <th>状态</th>
                                <th>下单时间</th>
                                <th class="th-operate">操作</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php foreach ($data_list as $v) : ?>
                                <tr>
                                    <td>{$v.order_sn}</td>
                                    <td>{$v.member.member_nickname}</td>
                                    <td>{$v.member.member_tel}</td>
                                    <td>{$v.coupon_money}</td>
                                    <td>{$v.money}</td>
                                    <td>{$v.amount}</td>
                                    <td>{$v.payment_id ? $payment[$v.payment_id] : ''}</td>
                                    <td>{$status[$v.status]}</td>
                                    <td>{:date('Y-m-d H:i:s',$v.order_time)}</td>
                                    <td>
                                        <div class="hidden-sm hidden-xs action-buttons action-buttons-list">
                                            <a href="{:folder_url('Common/order_detail',['id'=>$v.order_id])}" class="detail-btn" data-toggle="tooltip" title="详情">
                                                <span class="blue"><i class="ace-icon fa fa-sticky-note bigger-130"></i></span>
                                            </a>
                                            <a class="delivery-change" data-id="{$v.order_id}" data-distribution="{$v.distribution_id}" data-toggle="modal" data-target="#delivery_modal" title="发货">
                                                <span class="green"><i class="ace-icon fa fa-pencil bigger-130"></i></span>
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

{block name="modal"}
<!-- 显示修改运费模态框（Modal） -->
<div class="modal fade in" id="delivery_modal" tabindex="-1" role="dialog" aria-labelledby="delivery_modal_title" aria-hidden="true">
    <form class="form-horizontal ajax-form" method="post" action="{:controller_url('delivery_examine')}">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <h4 class="modal-title" id="delivery_modal_title"> 发货 </h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-xs-12">
                            <input type="hidden" name="id" value=""/>
                            <input type="hidden" name="return_url" value="{$current_url}"/>

                            <div class="form-group">
                                <label class="col-sm-2 control-label no-padding-right" for="distribution_id"> 配送方式 </label>
                                <div class="col-sm-10">
                                    <select name="distribution_id" id="distribution_id" data-href="{:folder_url('Ajax/select_distribution')}" class="col-xs-10 col-sm-5 selectpicker select-ajax" title="请选择" required></select>
                                </div>
                            </div>

                            <div class="space-4"></div>

                            <div class="form-group">
                                <label class="col-sm-2 control-label no-padding-right" for="form-field-1"> 运单号 </label>
                                <div class="col-sm-10">
                                    <input type="text" name="courier_sn" id="courier_sn" value="" placeholder="输入运单号" class="col-xs-10 col-sm-5" required/>
                                    <span class="lbl col-xs-12 col-sm-7"></span>
                                </div>
                            </div>

                            <div class="space-4"></div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-primary ajax-submit">
                        提交保存
                    </button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">
                        关闭
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>
{/block}

{block name="scripts"}
<script>
    $(function () {
        $('body').on('click', '.delivery-change', function () {
            $('input[name=id]').val($(this).data('id'));
            $('#distribution_id').selectpicker('val', $(this).data('distribution'));
        });
    });
</script>
{/block}
