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
                    <label class="col-sm-2 control-label no-padding-right" for="form-field-1"> 姓名 </label>
                    <div class="col-sm-10">
                        <input type="text" name="name" id="name" value="{$data_info.name|default=''}" placeholder="姓名" class="col-xs-10 col-sm-5"/>
                    </div>
                </div>

                <div class="space-4"></div>

                <div class="form-group">
                    <label class="col-sm-2 control-label no-padding-right" for="form-field-1"> 称号 </label>
                    <div class="col-sm-10">
                        <input type="text" name="title" id="title" value="{$data_info.title|default=''}" placeholder="称号" class="col-xs-10 col-sm-5"/>
                    </div>
                </div>

                <div class="space-4"></div>

                <?php $field_name = 'image'; ?>
                <?php $thumb = $data_info[$field_name] ?? []; ?>
                {include file="piece/thumb_upload_piece" field_desc="头像图片" image_size=""/}


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
<script>

</script>
{/block}
