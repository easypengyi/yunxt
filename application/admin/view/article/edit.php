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
                    <label class="col-sm-2 control-label no-padding-right" for="form-field-1"> 标题 </label>
                    <div class="col-sm-10">
                        <input type="text" name="title" id="title" value="{$data_info.title|default=''}" placeholder="标题" class="col-xs-10 col-sm-5"/>
                        <span class="lbl col-xs-12 col-sm-7"></span>
                    </div>
                </div>
                <?php if($type == 1): ?>
                <div class="space-4"></div>

                <div class="form-group">
                    <label class="col-sm-2 control-label no-padding-right" for="form-field-1"> 内容 </label>
                    <div class="col-sm-10">
                       <textarea  rows="3" cols="20" name="content" id="content"  class="col-xs-10 col-sm-5">{$data_info.content|default=''}</textarea>
                    </div>
                </div>
                <?php else: ?>
                <div class="space-4"></div>

                <div class="form-group">
                    <label class="col-sm-2 control-label no-padding-right" for="form-field-1"> 文章详情 </label>
                    <div class="col-sm-10">
                        <div id="editor" class="col-xs-10 col-sm-8"></div>
                        <textarea id="detail_id" class="editor-content" placeholder="" hidden></textarea>
                    </div>
                </div>
                <?php endif; ?>

                <div class="space-4"></div>

                <?php $field_name = 'image'; ?>
                <?php $thumb = $data_info[$field_name] ?? []; ?>
                {include file="piece/thumb_upload_piece" field_desc="图片" image_size=""/}


                <div class="space-4"></div>

                <div class="form-group">
                    <label class="col-sm-2 control-label no-padding-right" for="form-field-1"> 排序 </label>
                    <div class="col-sm-10">
                        <input type="text" name="sort" id="sort" value="{$data_info.sort|default='50'}" placeholder="输入排序" class="col-xs-10 col-sm-5"/>
                        <span class="lbl col-xs-12 col-sm-7"><span class="red">*</span>从小到大排序</span>
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
<script src="__STATIC__/wangEditor/dist-3.1.0/wangEditor.min.js"></script>
<script>
    $(function () {
        init_editor('editor', 'detail_id', '{$data_info.detail_id|default=0}');
    });


</script>
{/block}
