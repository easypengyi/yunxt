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
                    <label class="col-sm-2 control-label no-padding-right" for="payment_id"> 支付类型 </label>
                    <div class="col-sm-10">
                        <?php isset($data_info['payment_id']) OR $data_info['payment_id'] = 0; ?>
                        <select name="payment_id" id="payment_id" data-child="pay_type" data-href="{:folder_url('Ajax/select_payment',['id'=>$data_info.payment_id])}" class="col-xs-10 col-sm-5 selectpicker select-ajax" title="请选择" required></select>
                    </div>
                </div>

                <div class="space-4"></div>

                <div class="form-group">
                    <label class="col-sm-2 control-label no-padding-right" for="pay_type"> 支付种类 </label>
                    <div class="col-sm-10">
                        <?php isset($data_info['pay_type']) OR $data_info['pay_type'] = ''; ?>
                        <select name="pay_type" id="pay_type" data-ischild="payment_id" data-href="{:folder_url('Ajax/select_pay_type',['id'=>$data_info.pay_type])}" class="col-xs-10 col-sm-5 selectpicker select-ajax" title="请选择" required></select>
                    </div>
                </div>

                <div class="space-4"></div>

                <div class="form-group">
                    <label class="col-sm-2 control-label no-padding-right" for="form-field-1"> 默认 </label>
                    <div class="col-sm-10" style="padding-top:5px;">
                        <input type="checkbox" name="tolerant" id="tolerant" <?php echo ($data_info['tolerant'] ?? false) ? 'checked' : ''; ?> value="1" placeholder="" class="ace ace-switch ace-switch-6"/>
                        <span class="lbl">&nbsp;&nbsp;默认关闭</span>
                    </div>
                </div>

                <div class="space-4"></div>

                <div class="form-group">
                    <label class="col-sm-2 control-label no-padding-right" for="form-field-1"> 开启 </label>
                    <div class="col-sm-10" style="padding-top:5px;">
                        <input type="checkbox" name="enable" id="enable" <?php echo ($data_info['enable'] ?? false) ? 'checked' : ''; ?> value="1" placeholder="" class="ace ace-switch ace-switch-6"/>
                        <span class="lbl">&nbsp;&nbsp;默认关闭</span>
                    </div>
                </div>

                <div class="space-4"></div>

                <div class="clearfix form-actions">
                    <div class="col-md-offset-3 col-md-9">
                        <button class="btn btn-info ajax-submit" type="submit">
                            <i class="ace-icon fa fa-check bigger-110"></i> 保存
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
{/block}

{block name="scripts"}
{/block}
