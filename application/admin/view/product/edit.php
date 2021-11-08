{extend name="public/base" /}
{block name="main-content"}
<?php isset($edit) OR $edit = false; ?>
<?php isset($data_info) OR $data_info = []; ?>

<div class="page-content">
    <div class="row top20">
        <div class="col-xs-12">
            <form class="form-horizontal ajax-form" method="post" action="{$full_url}">
                <input type="hidden" name="return_url" value="{$return_url}"/>

                <div class="tabbable">
                    <ul class="nav nav-tabs" id="myTab">
                        <li class="active">
                            <a data-toggle="tab" href="#base">
                                基本信息
                            </a>
                        </li>
                        <li>
                            <a data-toggle="tab" href="#image">
                                图片信息
                            </a>
                        </li>

                    </ul>
                </div>

                <div class="tab-content">
                    <div id="base" class="tab-pane fade in active">
                        <div class="space-4"></div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label no-padding-right" for="form-field-1"> 商品名称 </label>
                            <div class="col-sm-10">
                                <input type="text" name="name" id="name" value="{$data_info.name|default=''}" placeholder="商品名称" class="col-xs-10 col-sm-5"/>
                                <span class="lbl col-xs-12 col-sm-7"><span class="red">*</span></span>
                            </div>
                        </div>

                        <div class="space-4"></div>

                        <?php $field_name = 'image'; ?>
                        <?php $thumb = $data_info[$field_name] ?? []; ?>
                        {include file="piece/thumb_upload_piece" field_desc="头像图片" image_size=""/}

                        <div class="space-4"></div>


                        <?php $field_name = 'share_image'; ?>
                        <?php $thumb = $data_info[$field_name] ?? []; ?>
                        {include file="piece/thumb_upload_piece" field_desc="分享海报背景" image_size=""/}

                        <div class="space-4"></div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label no-padding-right" for="form-field-1"> 原价 </label>
                            <div class="col-sm-10">
                                <input type="number" min="0" step="0.01" name="original_price" id="original_price" value="{$data_info.original_price|default=''}" placeholder="商品原价" class="col-xs-10 col-sm-5"/>
                                <span class="lbl col-xs-12 col-sm-7"><span class="red">*</span></span>
                            </div>
                        </div>


                        <div class="space-4"></div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label no-padding-right" for="form-field-1"> 现价 </label>
                            <div class="col-sm-10">
                                <input type="number" min="0" step="0.01" name="current_price" id="current_price" value="{$data_info.current_price|default=''}" placeholder="商品现价" class="col-xs-10 col-sm-5"/>
                                <span class="lbl col-xs-12 col-sm-7"><span class="red">*</span></span>
                            </div>
                        </div>

                        <div class="space-4"></div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label no-padding-right" for="form-field-1">商品描述 </label>
                            <div class="col-sm-10">
                                <input type="text"  name="description" id="description" value="{$data_info.description|default=''}" placeholder="商品描述" class="col-xs-10 col-sm-5"/>
                            </div>
                        </div>

                        <div class="space-4"></div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label no-padding-right" for="form-field-1"> 商品数量 </label>
                            <div class="col-sm-10">
                                <input type="number" name="number" id="number" value="{$data_info.number|default='50'}" placeholder="商品数量" class="col-xs-10 col-sm-5"/>
                                <span class="lbl col-xs-12 col-sm-7"></span>
                            </div>
                        </div>


                        <div class="space-4"></div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label no-padding-right" for="form-field-1"> 库存 </label>
                            <div class="col-sm-10">
                                <input type="number" min="0" name="stock" id="stock" value="{$data_info.stock|default=''}" placeholder="产品库存" class="col-xs-10 col-sm-5"/>
                                <span class="lbl col-xs-12 col-sm-7"><span class="red">*</span></span>
                            </div>
                        </div>


                        <div class="space-4"></div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label no-padding-right" for="form-field-1"> 上架 </label>
                            <div class="col-sm-10" style="padding-top:5px;">
                                <input type="checkbox" name="enable" id="enable" <?php echo ($data_info['enable'] ?? false) ? 'checked' : ''; ?> value="1" placeholder="" class="ace ace-switch ace-switch-6"/>
                                <span class="lbl"></span>
                            </div>
                        </div>

                        <div class="space-4"></div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label no-padding-right" for="form-field-1"> 排序 </label>
                            <div class="col-sm-10">
                                <input type="number" name="sort" id="product_sort" value="{$data_info.sort|default='50'}" placeholder="排序" class="col-xs-10 col-sm-5"/>
                                <span class="lbl col-xs-12 col-sm-7"></span>
                            </div>
                        </div>

                        <div class="space-4"></div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label no-padding-right" for="form-field-1"> 商品详情 </label>
                            <div class="col-sm-10">
                                <div id="editor" class="col-xs-10 col-sm-8"></div>
                                <textarea id="detail_id" class="editor-content" placeholder="" hidden></textarea>
                            </div>
                        </div>

                    </div>
                    <div id="image" class="tab-pane fade">
                        <table>
                            <tr>
                                <td id="image_data">
                                    <?php if ($edit && isset($data_info['detail_image']) && !empty($data_info['detail_image'])): ?>
                                        <?php foreach ($data_info['detail_image'] as $v): ?>
                                            <div style="width:100px; text-align:center; margin: 5px; display:inline-block;">
                                                <input type="hidden" name="image_id[]" value="{$v.file_id}"/>
                                                <img width="100" height="100" src="{$v.full_url}" alt=""/>
                                                <br>
                                                <a class="delete-image">删除</a>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div>
                                        <a href="javascript:" data-href="{:url('tool/Upload/multiple_upload')}" class="upload-multiple" data-param="" data-callback="file_callback">
                                            <img src="__IMG__/add-button.jpg" width="100" height="100" alt=""/>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        </table>
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
<div id="image_div">
    <div style="width:100px; text-align:center; margin: 5px; display:inline-block;">
        <input type="hidden" name="image_id[]"/>
        <img width="100" height="100" src="" alt=""/>
        <br>
        <a class="delete-image">删除</a>
    </div>
</div>
{/block}

{block name="scripts"}
<script src="__STATIC__/laydate/dist-5.0.9/laydate.js"></script>
<script src="__STATIC__/wangEditor/dist-3.1.0/wangEditor.min.js"></script>
<!--suppress JSUnusedLocalSymbols -->
<script>
    $(function () {


        init_editor('editor', 'detail_id', '{$data_info.detail_id|default=0}');

        $('.datetimepicker').each(function () {
            laydate.render({
                elem: this,
                type: 'datetime',
                format: 'yyyy-MM-dd HH:mm'
            });
        });

        $('body').on('click', '.field-delete', function () {
            var index = $('#fields-data tr').index($(this).closest('tr'));
            $('#fields-data').find('tr').eq(index).remove();
        });
    });

    //文件回调
    function file_callback(data) {
        console.log(data)
        var html = $('#image_div').find('div').clone();
        html.find('[name="image_id[]"]').val(data['file_id']);
        html.find('img').attr('src', data['full_url']);
        $('#image_data').append(html);
    }
</script>
{/block}
