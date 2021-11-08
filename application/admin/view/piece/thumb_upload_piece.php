<div class="form-group">
    <label class="col-sm-2 control-label no-padding-right" for="form-field-1"> {$field_desc|default=''}[field_desc] </label>
    <input type="hidden" name="{$field_name}_id" value="{$thumb.file_id|default=0}"/>
    <div class="col-sm-10 image-div" data-src="{$thumb.full_url|default=''}" data-name="{$field_name}">
        <a href="javascript:void(0);" title="点击选择所要上传的图片" class="file">
            <input type="file" class="image-upload"/>
            选择上传文件
        </a>
        &nbsp;&nbsp;
        <a title="还原修改前的图片" class="file image-cancel">
            撤销上传
        </a>
        <div>
            <img src="{:base_url(NO_IMAGE_URL)}" height="70" alt=""/>
        </div>
        <div>
            <span style="font-size: 14px;color: #858585;">[image_size]</span>
        </div>
    </div>
</div>
