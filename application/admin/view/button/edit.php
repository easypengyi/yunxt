{extend name="public/base" /}
{block name="main-content"}
<?php isset($edit) OR $edit = false; ?>
<?php isset($data_info) OR $data_info = []; ?>
<?php isset($type) OR $type = 1; ?>
<?php isset($skip) OR $skip = []; ?>

<div class="page-content">
    <div class="row top20">
        <div class="col-xs-12">
            <form class="form-horizontal ajax-form" method="post" action="{$full_url}">
                <input type="hidden" name="return_url" value="{$return_url}"/>

                <div class="form-group">
                    <label class="col-sm-2 control-label no-padding-right" for="form-field-1"> 按钮名称 </label>
                    <div class="col-sm-10">
                        <input type="text" name="name" id="name" value="{$data_info.name|default=''}" placeholder="按钮名称" class="col-xs-10 col-sm-5"/>
                        <span class="lbl col-xs-12 col-sm-7"></span>
                    </div>
                </div>

                <div class="space-4"></div>

                <div class="form-group" id="type_div">
                    <label class="col-sm-2 control-label no-padding-right" for="skip"> 类型 </label>
                    <div class="col-sm-10">
                        <select name="skip" id="skip" class="col-xs-10 col-sm-5 selectpicker" title="请选择" required>
                            <option value="">请选择</option>
                            <?php isset($type_skip) OR $type_skip = []; ?>
                            <?php isset($data_info['skip']) OR $data_info['skip'] = ''; ?>
                            <?php foreach ($type_skip[$type] as $k => $v): ?>
                                <option value="<?php echo $v; ?>" <?php echo $data_info['skip'] == $v ? 'selected' : ''; ?>><?php echo $skip[$v] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="space-4"></div>

                <div id="category_div" class="none">
                    <div class="form-group">
                        <label class="col-sm-2 control-label no-padding-right" for="form-field-1"> 商品分类 </label>
                        <div class="col-sm-10">
                            <div class="input-group col-xs-10 col-sm-5">
                                <span data-href="{:folder_url('Common/choose_product_category')}" data-param="" data-callback="choose_product_category" class="input-group-addon choose">选择分类</span>
                                <input type="text" id="product_category_name" name="product_category_name" value="" placeholder="" class="form-control" readonly/>
                                <input type="hidden" id="product_category_id" name="product_category_id" value=""/>
                                <span class="input-group-addon choose-clear" data-input="product_category_name,product_category_id">清空</span>
                            </div>
                        </div>
                    </div>

                    <div class="space-4"></div>
                </div>

                <?php $field_name = 'image'; ?>
                <?php $thumb = $data_info[$field_name] ?? []; ?>
                {include file="piece/thumb_upload_piece" field_desc="按钮图片" image_size="86*86"/}

                <div class="space-4"></div>

                <div class="form-group">
                    <label class="col-sm-2 control-label no-padding-right" for="form-field-1"> 排序 </label>
                    <div class="col-sm-10">
                        <input type="text" name="sort" id="sort" value="{$data_info.sort|default='0'}" placeholder="输入排序" class="col-xs-10 col-sm-5"/>
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
<script>
    $(function () {
        $('#skip').change(function () {
            var skip = parseInt($(this).val());
            var category_div_show = false;

            switch (skip) {
                case parseInt('<?php echo app\common\model\Button::SKIP_CATEGORY_PRODUCT; ?>'):
                    category_div_show = true;
                    choose_product_category({
                        category_id: '{$data_info.content_info|default=""}',
                        name: '{$data_info.content|default=""}'
                    });
                    break;
                default:
                    break;
            }

            var category_div = $('#category_div');

            if (category_div_show && category_div.is(':hidden')) {
                category_div.show(400);
            } else if (!category_div_show && !category_div.is(':hidden')) {
                category_div.hide(400);
            }
        }).change();
    });

    // 分类选择回调
    function choose_product_category(category) {
        $('#product_category_id').val(category.category_id);
        $('#product_category_name').val(category.name);
    }
</script>
{/block}
