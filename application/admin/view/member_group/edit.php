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
                    <label class="col-sm-2 control-label no-padding-right" for="form-field-1"> 职位 </label>
                    <div class="col-sm-10">
                        <input type="text" name="group_name" id="group_name" value="{$data_info.group_name|default=''}" placeholder="输入职位" class="col-xs-10 col-sm-5" required/>
                        <span class="lbl col-xs-12 col-sm-7"></span>
                    </div>
                </div>

                <div class="space-4"></div>

                <div class="form-group">
                    <label class="col-sm-2 control-label no-padding-right" for="form-field-1"> 晋升数量 </label>
                    <div class="col-sm-10">
                        <input type="number" name="num" id="num" value="{$data_info.num|default=''}" placeholder=" 输入晋升数量" class="col-xs-10 col-sm-5" required/>
                        <span class="lbl col-xs-12 col-sm-7"></span>
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

{block name="scripts"}
{/block}
