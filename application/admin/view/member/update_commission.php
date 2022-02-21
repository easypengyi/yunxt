{extend name="public/base" /}
{block name="main-content"}
<?php isset($edit) OR $edit = false; ?>
<?php isset($data_info) OR $data_info = []; ?>

<div class="page-content">
    <div class="row top20">
        <div class="col-xs-12">
            <div class="row maintop">
                <div class="col-xs-12 col-sm-12 col-md-5 margintop5">
                    <h3 class="blue">{$data_info['member_realname']}佣金：{$data_info['commission']}元</h3>
                </div>
            </div>
            <form class="form-horizontal ajax-form" method="post" action="{$full_url}">
                <input type="hidden" name="return_url" value="{$return_url}"/>
                <input type="hidden" name="member_id" value="{$data_info['member_id']}"/>

                <div class="form-group">
                    <label class="col-sm-2 control-label no-padding-right" for="form-field-1"> 金额 </label>
                    <div class="col-sm-10">
                        <input type="tel" name="money"  maxlength="11" value="" placeholder="输入增减的金额" class="col-xs-10 col-sm-5" required/>
                        <span class="lbl col-xs-12 col-sm-7"><span class="red">*必填</span></span>
                    </div>
                </div>

                <div class="space-4"></div>

                <div class="form-group">
                    <label class="col-sm-2 control-label no-padding-right" for="group_id"> 类型 </label>
                    <div class="col-sm-10">
                        <select name="type" id="type"  class="col-xs-10 col-sm-5 selectpicker" title="请选择" required>
                            <option value="1">增加</option>
                            <option value="2">减少</option>
                        </select>
                    </div>
                </div>

                <div class="space-4"></div>

                <div class="form-group">
                    <label class="col-sm-2 control-label no-padding-right" for="form-field-1"> 备注 </label>
                    <div class="col-sm-10">
                        <input type="text" name="remark"  placeholder="输入备注" class="col-xs-10 col-sm-5" />
                        <span class="lbl col-xs-12 col-sm-7">
                        </span>
                    </div>
                </div>

                <div class="space-4"></div>

                <div class="form-group">
                    <label class="col-sm-2 control-label no-padding-right" for="form-field-1"> 密码 </label>
                    <div class="col-sm-10">
                        <input type="password" name="recharge_pwd"  placeholder="输入密码" class="col-xs-10 col-sm-5" maxlength="15" minlength="6"/>
                        <span class="lbl col-xs-12 col-sm-7">
                            <span class="red">*必填</span>
                        </span>
                    </div>
                </div>


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

{block name="scripts"}
<script src="__STATIC__/laydate/dist-5.0.9/laydate.js"></script>
<!--suppress JSUnusedLocalSymbols -->
<script>
    $(function () {

    });

</script>
{/block}
