{extend name="public/base" /}
{block name="main-content"}
<?php isset($edit) OR $edit = false; ?>
<?php isset($data_info) OR $data_info = []; ?>

<div class="page-content">
    <div class="row top20">
        <div class="col-xs-12">
            <form class="form-horizontal ajax-form" method="post" action="{$full_url}">
                <input type="hidden" name="return_url" value="{$return_url}"/>

                <div class="form-group">
                    <label class="col-sm-2 control-label no-padding-right" for="form-field-1"> 优惠券名称 </label>
                    <div class="col-sm-10">
                        <input type="text" name="coupon_name" id="coupon_name" value="{$data_info.coupon_name|default=''}" placeholder="输入优惠券名称" class="col-xs-10 col-sm-5" required/>
                        <span class="lbl col-xs-12 col-sm-7"></span>
                    </div>
                </div>

                <div class="space-4"></div>

                <div class="form-group none">
                    <label class="col-sm-2 control-label no-padding-right" for="form-field-1"> 优惠券描述 </label>
                    <div class="col-sm-10">
                        <input type="text" name="coupon_desc" id="coupon_desc" value="{$data_info.coupon_desc|default=''}" placeholder="输入优惠券描述" class="col-xs-10 col-sm-5"/>
                        <span class="lbl col-xs-12 col-sm-7"></span>
                    </div>
                </div>

                <div class="space-4"></div>

                <div class="form-group">
                    <label class="col-sm-2 control-label no-padding-right" for="form-field-1"> 满足使用金额 </label>
                    <div class="col-sm-10">
                        <input type="text" name="fill" id="fill" value="{$data_info.fill|default=''}" placeholder="输入满足使用金额" class="col-xs-10 col-sm-5" required/>
                        <span class="lbl col-xs-12 col-sm-7"></span>
                    </div>
                </div>

                <div class="space-4"></div>

                <div class="form-group">
                    <label class="col-sm-2 control-label no-padding-right" for="form-field-1"> 优惠金额 </label>
                    <div class="col-sm-10">
                        <input type="text" name="value" id="value" value="{$data_info.value|default=''}" placeholder="输入优惠金额" class="col-xs-10 col-sm-5" required/>
                        <span class="lbl col-xs-12 col-sm-7"></span>
                    </div>
                </div>

                <div class="space-4"></div>

                <div class="form-group">
                    <label class="col-sm-2 control-label no-padding-right" for="form-field-1"> 可领取数量 </label>
                    <div class="col-sm-10">
                        <input type="number" name="receive_number" id="receive_number" value="{$data_info.receive_number|default=''}" placeholder="输入可领取数量" class="col-xs-10 col-sm-5" required/>
                        <span class="lbl col-xs-12 col-sm-7"></span>
                    </div>
                </div>

                <div class="space-4"></div>

                <div class="form-group">
                    <label class="col-sm-2 control-label no-padding-right" for="form-field-1"> 单会员领取数量 </label>
                    <div class="col-sm-10">
                        <input type="number" name="number_limit" id="number_limit" value="{$data_info.number_limit|default=''}" placeholder="输入单会员领取数量" class="col-xs-10 col-sm-5" required/>
                        <span class="lbl col-xs-12 col-sm-7"></span>
                    </div>
                </div>

                <div class="space-4"></div>

                <div class="form-group">
                    <label class="col-sm-2 control-label no-padding-right" for="form-field-1"> 领取限制 </label>
                    <div class="col-sm-10" style="padding-top:5px;">
                        <input type="checkbox" name="receive_limit" id="receive_limit" <?php echo ($data_info['receive_limit'] ?? false) ? 'checked' : ''; ?> value="1" placeholder="" class="ace ace-switch ace-switch-6"/>
                        <span class="lbl"></span>
                    </div>
                </div>

                <div class="space-4"></div>

                <div id="receive_div" class="{$data_info.receive_limit|default=false ? '' : 'none'}">
                    <div class="form-group">
                        <label class="col-sm-2 control-label no-padding-right" for="form-field-1"> 优惠券开始领取时间 </label>
                        <div class="col-sm-10">
                            <input type="text" name="start_receive_time" id="start_receive_time" value="{$edit && $data_info.start_receive_time ? date('Y-m-d H:i',$data_info.start_receive_time) : ''}" placeholder="优惠券开始领取时间" class="col-xs-10 col-sm-5 datetimepicker"/>
                            <span class="lbl col-xs-12 col-sm-7"></span>
                        </div>
                    </div>

                    <div class="space-4"></div>
                </div>

                <div class="form-group">
                    <label class="col-sm-2 control-label no-padding-right" for="form-field-1"> 使用时间限制 </label>
                    <div class="col-sm-10" style="padding-top:5px;">
                        <input type="checkbox" name="time_limit" id="time_limit" <?php echo ($data_info['time_limit'] ?? false) ? 'checked' : ''; ?> value="1" placeholder="" class="ace ace-switch ace-switch-6"/>
                        <span class="lbl"></span>
                    </div>
                </div>

                <div class="space-4"></div>

                <div id="time_div" class="{$data_info.time_limit|default=false ? '' : 'none'}">
                    <div class="form-group">
                        <label class="col-sm-2 control-label no-padding-right" for="form-field-1"> 优惠券开始时间 </label>
                        <div class="col-sm-10">
                            <input type="text" name="start_time" id="start_time" value="{$edit && $data_info.start_time ? date('Y-m-d H:i',$data_info.start_time) : ''}" placeholder="优惠券开始时间" class="col-xs-10 col-sm-5 datetimepicker"/>
                            <span class="lbl col-xs-12 col-sm-7"></span>
                        </div>
                    </div>

                    <div class="space-4"></div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label no-padding-right" for="form-field-1"> 优惠券结束时间 </label>
                        <div class="col-sm-10">
                            <input type="text" name="end_time" id="end_time" value="{$edit && $data_info.end_time ? date('Y-m-d H:i',$data_info.end_time) : ''}" placeholder="优惠券结束时间" class="col-xs-10 col-sm-5 datetimepicker"/>
                            <span class="lbl col-xs-12 col-sm-7"></span>
                        </div>
                    </div>

                    <div class="space-4"></div>
                </div>

                <div class="form-group">
                    <label class="col-sm-2 control-label no-padding-right" for="form-field-1"> 商品限制 </label>
                    <div class="col-sm-10" style="padding-top:5px;">
                        <input type="checkbox" name="product_limit" id="product_limit" <?php echo ($data_info['product_limit'] ?? false) ? 'checked' : ''; ?> value="1" placeholder="" class="ace ace-switch ace-switch-6"/>
                        <span class="lbl"></span>
                    </div>
                </div>

                <div class="space-4"></div>

                <div id="product_div" class="{$data_info.product_limit|default=false ? '' : 'none'}">
                    <div class="form-group">
                        <label class="col-sm-2 control-label no-padding-right" for="form-field-1"> 限制商品 </label>
                        <div class="col-sm-10">
                            <div class="col-xs-10 col-sm-5">
                                <table class="table table-striped table-bordered table-hover">
                                    <thead>
                                        <tr>
                                            <th class="center">
                                            <span data-param="product_id[]" data-callback="product_choose" data-href="{:folder_url('Common/choose_product',['choose_all'=>true])}" class="purple new-row choose" title="添加限制商品">
                                                <i class="ace-icon fa fa-plus-circle bigger-130"></i>
                                            </span>
                                            </th>
                                            <th>名称</th>
                                            <th class="th-operate">操作</th>
                                        </tr>
                                    </thead>
                                    <tbody id="fields-data">
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="space-4"></div>
                </div>

                <div class="space-4"></div>

                <div class="form-group">
                    <label class="col-sm-2 control-label no-padding-right" for="form-field-1"> 开通邀请赠送 </label>
                    <div class="col-sm-10" style="padding-top:5px;">
                        <input type="checkbox" name="activity_send" id="activity_send" <?php echo ($data_info['activity_send'] ?? false) ? 'checked' : ''; ?> value="1" placeholder="" class="ace ace-switch ace-switch-6"/>
                        <span class="lbl"></span>
                    </div>
                </div>

                <div class="space-4"></div>

                <div class="form-group">
                    <label class="col-sm-2 control-label no-padding-right" for="form-field-1"> 开启 </label>
                    <div class="col-sm-10" style="padding-top:5px;">
                        <input type="checkbox" name="enable" id="enable" <?php echo ($data_info['enable'] ?? false) ? 'checked' : ''; ?> value="1" placeholder="" class="ace ace-switch ace-switch-6"/>
                        <span class="lbl"></span>
                    </div>
                </div>

                <div class="space-4"></div>

                <div class="clearfix form-actions">
                    <div class="col-md-offset-3 col-md-9">
                        <button class="btn btn-info" type="submit">
                            <i class="ace-icon fa fa-check bigger-110"></i> 保存
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
{/block}

{block name="hide-content"}
<table id="data_table">
    <tr>
        <td><input type="hidden" name="product_id[]" value=""/></td>
        <td class="product_name"></td>
        <td class="th-operate">
            <div class="action-buttons">
                <a class="red field-delete" data-toggle="tooltip" title="删除">
                    <i class="ace-icon fa fa-trash-o bigger-130"></i>
                </a>
            </div>
        </td>
    </tr>
</table>
{/block}

{block name="scripts"}
<script src="__STATIC__/laydate/dist-5.0.9/laydate.js"></script>
<!--suppress JSUnusedLocalSymbols -->
<script>
    $(function () {
        <?php isset($data_info['product_ids']) OR $data_info['product_ids'] = ''; ?>
        <?php if ($data_info['product_ids']):?>
        var url = "{:folder_url('Ajax/product_list',['id'=>$data_info.product_ids])}";
        $.get(url, function (data) {
            data.data.forEach(function (value) {
                product_choose(value);
            });
        }, 'json');
        <?php endif;?>

        $('.datetimepicker').each(function () {
            laydate.render({
                elem: this,
                type: 'datetime',
                format: 'yyyy-MM-dd HH:mm'
            });
        });

        $('#time_limit').change(function () {
            var time_div = $('#time_div');
            if ($(this).is(':checked')) {
                if (time_div.is(':hidden')) {
                    time_div.show(400);
                }
            }
            else {
                if (!time_div.is(':hidden')) {
                    time_div.hide(400);
                }
            }
        }).change();

        $('#product_limit').change(function () {
            var product_div = $('#product_div');
            if ($(this).is(':checked')) {
                if (product_div.is(':hidden')) {
                    product_div.show(400);
                }
            }
            else {
                if (!product_div.is(':hidden')) {
                    product_div.hide(400);
                }
            }
        }).change();

        $('#receive_limit').change(function () {
            var receive_div = $('#receive_div');
            if ($(this).is(':checked')) {
                if (receive_div.is(':hidden')) {
                    receive_div.show(400);
                }
            }
            else {
                if (!receive_div.is(':hidden')) {
                    receive_div.hide(400);
                }
            }
        }).change();

        $('body').on('click', '.field-delete', function () {
            var index = $('#fields-data tr').index($(this).closest('tr'));
            $('#fields-data').find('tr').eq(index).remove();
        });
    });

    // 商品选择回调
    function product_choose(product) {
        var data_table = $('#data_table');
        var html = data_table.find('tr').eq(0).clone();
        html.find('.product_name').html(product['name']);
        html.find('[name="product_id[]"]').val(product['product_id']);
        $("#fields-data").append(html);
    }
</script>
{/block}
