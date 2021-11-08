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
                    <label class="col-sm-2 control-label no-padding-right" for="form-field-1"> 名称 </label>
                    <div class="col-sm-10">
                        <input type="text" name="name" id="name" value="{$data_info.name|default=''}" placeholder="名称" class="col-xs-10 col-sm-5"/>
                        <span class="lbl col-xs-12 col-sm-7"></span>
                    </div>
                </div>

                <div class="space-4"></div>

                <div class="form-group">
                    <label class="col-sm-2 control-label no-padding-right" for="form-field-1"> 报告文件 </label>
                    <div class="col-sm-10">
                        <a href="javascript:" class="file" style="overflow:unset;">
                            <input type="file" name="file" id="file" class="file-upload"/>
                            选择上传文件
                        </a>
                        <span class="file-name">{$data_info.file.url|default=''}</span>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-2 control-label no-padding-right" for="form-field-1"> 备注 </label>
                    <div class="col-sm-10">
                        <textarea name="remark" id="remark" cols="20" rows="5" placeholder="" class="col-xs-10 col-sm-5">{$data_info.remark|default=''}</textarea>
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
<!--suppress JSUnusedLocalSymbols, JSCheckFunctionSignatures -->
<script>
    $(function () {
        $('.file-upload').change(function () {
            var view = $(this);
            var name = view.val().split("\\")[2];
            $('.file-name').text(name);
        });
    })
</script>
{/block}
