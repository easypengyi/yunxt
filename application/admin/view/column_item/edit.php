{extend name="public/base" /}
{block name="main-content"}
<?php isset($edit) OR $edit = false; ?>
<?php isset($data_info) OR $data_info = []; ?>
<?php isset($type) OR $type = 1; ?>
<?php isset($skip) OR $skip = []; ?>
<?php isset($button) OR $button = []; ?>

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

                <div class="form-group" id="type_div">
                    <label class="col-sm-2 control-label no-padding-right" for="skip"> 跳转模式 </label>
                    <div class="col-sm-10">
                        <select name="skip" id="skip" class="col-xs-10 col-sm-5 selectpicker" title="请选择" required>
                            <option value="">请选择</option>
                            <?php isset($data_info['skip']) OR $data_info['skip'] = ''; ?>
                            <?php foreach ($skip as $k => $v): ?>
                                <option value="<?php echo $k; ?>" <?php echo $data_info['skip'] == $k ? 'selected' : ''; ?>><?php echo $v ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="space-4"></div>

                <?php $field_name = 'image'; ?>
                <?php $thumb = $data_info[$field_name] ?? []; ?>
                {include file="piece/thumb_upload_piece" field_desc="图片" image_size="<?php echo $image_size[$column_type ?? 0] ?? ''; ?>"/}

                <div class="space-4"></div>

                <div id="url_div" class="none">
                    <div class="form-group">
                        <label class="col-sm-2 control-label no-padding-right" for="form-field-1"> 链接URL </label>
                        <div class="col-sm-10">
                            <input type="url" name="url" id="url" value="{$data_info.url|default=''}" placeholder="输入链接URL" class="col-xs-10 col-sm-5"/>
                            <span class="lbl col-xs-12 col-sm-7"><span class="red">*</span>必须是以http://开头</span>
                        </div>
                    </div>

                    <div class="space-4"></div>
                </div>

                <div id="product_div" class="none">
                    <div class="form-group">
                        <label class="col-sm-2 control-label no-padding-right" for="form-field-1"> 商品 </label>
                        <div class="col-sm-10">
                            <div class="input-group col-xs-10 col-sm-5">
                                <span data-href="{:folder_url('Common/choose_product')}" data-param="" data-callback="choose_product" class="input-group-addon choose">选择商品</span>
                                <input type="text" id="product_name" value="" placeholder="" class="form-control" readonly/>
                                <input type="hidden" id="product_id" name="product_id" value=""/>
                                <span class="input-group-addon choose-clear" data-input="product_name,product_id">清空</span>
                            </div>
                        </div>
                    </div>

                    <div class="space-4"></div>
                </div>

                <div id="series_div" class="none">
                    <div class="form-group">
                        <label class="col-sm-2 control-label no-padding-right" for="form-field-1"> 专题 </label>
                        <div class="col-sm-10">
                            <div class="input-group col-xs-10 col-sm-5">
                                <span data-href="{:folder_url('Common/choose_series')}" data-param="" data-callback="choose_series" class="input-group-addon choose">选择专题</span>
                                <input type="text" id="series_name" value="" placeholder="" class="form-control" readonly/>
                                <input type="hidden" id="series_id" name="series_id" value=""/>
                                <span class="input-group-addon choose-clear" data-input="series_name,series_id">清空</span>
                            </div>
                        </div>
                    </div>

                    <div class="space-4"></div>
                </div>

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
<script src="__STATIC__/laydate/dist-5.0.9/laydate.js"></script>
<script>
    var init = false;

    $(function () {
        $('#skip').change(function () {
            var skip = parseInt($(this).val());
            var url_div_show = false;
            var product_div_show = false;
            var series_div_show = false;

            switch (skip) {
                case parseInt('<?php echo app\common\model\ColumnItem::SKIP_URL; ?>'):
                    url_div_show = true;
                    break;
                case parseInt('<?php echo app\common\model\ColumnItem::SKIP_PRODUCT; ?>'):
                    product_div_show = true;
                    if (!init) {
                        choose_product({
                            product_id: "{$data_info.content|default=''}",
                            name: "{$data_info.content_info|default=''}"
                        });
                    }
                    break;
                case parseInt('<?php echo app\common\model\ColumnItem::SKIP_SERIES; ?>'):
                    series_div_show = true;
                    if (!init) {
                        choose_series({
                            series_id: "{$data_info.content|default=''}",
                            name: "{$data_info.content_info|default=''}"
                        });
                    }
                    break;
                default:
                    break;
            }

            init = true;

            var url_div = $('#url_div');
            var product_div = $('#product_div');
            var series_div = $('#series_div');

            if (url_div_show && url_div.is(':hidden')) {
                url_div.show(400);
            } else if (!url_div_show && !url_div.is(':hidden')) {
                url_div.hide(400);
            }

            if (product_div_show && product_div.is(':hidden')) {
                product_div.show(400);
            } else if (!product_div_show && !product_div.is(':hidden')) {
                product_div.hide(400);
            }

            if (series_div_show && series_div.is(':hidden')) {
                series_div.show(400);
            } else if (!series_div_show && !series_div.is(':hidden')) {
                series_div.hide(400);
            }
        }).change();

        $('.datetimepicker').each(function () {
            laydate.render({
                elem: this,
                type: 'datetime',
                format: 'yyyy-MM-dd HH:mm'
            });
        });
    });

    // 商品选择回调
    function choose_product(product) {
        $('#product_id').val(product.product_id);
        $('#product_name').val(product.name);
    }

    // 专题选择回调
    function choose_series(series) {
        $('#series_id').val(series.series_id);
        $('#series_name').val(series.name);
    }
</script>
{/block}
