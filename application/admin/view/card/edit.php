{extend name="public/base" /}
{block name="main-content"}
<?php isset($edit) OR $edit = false; ?>
<?php isset($data_info) OR $data_info = []; ?>
<link rel="stylesheet" href="__STATIC__/layui/layui/css/layui.css">
<div class="page-content">
    <div class="row top20">
        <div class="col-xs-12">
            <form class="form-horizontal ajax-form" method="post" action="{$full_url}">
                <input type="hidden" name="return_url" value="{$return_url}"/>
                <div class="tabbable">
                    <ul class="nav nav-tabs" id="myTab">
                        <li class="active">
                            <a data-toggle="tab" href="#base">
                                活动基本信息
                            </a>
                        </li>
                        <li>
                            <a data-toggle="tab" href="#image">
                                活动图片信息
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="tab-content">
                    <div id="base" class="tab-pane fade in active">
                        <div class="form-group">
                            <label class="col-sm-2 control-label no-padding-right" for="form-field-1"> 活动地区 </label>
                            <div class="col-sm-10">
                                <div class="layui-form">
                                    <div class="layui-form-item" id="area-picker">
                                        <div class="layui-input-inline" style="width: 200px;">
                                            <select name="province" class="province-selector" data-value="{$data_info.province|default=''}" lay-filter="province-1">
                                                <option value="">请选择省</option>
                                            </select>
                                        </div>
                                        <div class="layui-input-inline" style="width: 200px;">
                                            <select name="city" class="city-selector" data-value="{$data_info.city|default=''}" lay-filter="city-1">
                                                <option value="">请选择市</option>
                                            </select>
                                        </div>
                                        <div class="layui-input-inline" style="width: 200px;">
                                            <select name="area" class="county-selector" data-value="{$data_info.area|default=''}" lay-filter="county-1">
                                                <option value="">请选择区</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>


                        <div class="space-4"></div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label no-padding-right" for="form-field-1"> 详细地址 </label>
                            <div class="col-sm-10">
                                <input type="text" name="address" id="address" value="{$data_info.address|default=''}" placeholder="活动详细地址" class="col-xs-10 col-sm-5"/>
                                <span class="lbl col-xs-12 col-sm-7"><span class="red">*</span></span>
                            </div>
                        </div>

                        <div class="space-4"></div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label no-padding-right" for="longitude"> 经度 </label>
                            <div class="col-sm-5">
                                <input type="text" name="longitude"  value="{$data_info.longitude|default=''}" placeholder="输入经度" class="col-xs-10 col-sm-5" required/>
                                <span class="lbl col-xs-12 col-sm-7"><span class="red">*</span></span>
                            </div>
                        </div>

                        <div class="space-4"></div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label no-padding-right" for="latitude"> 纬度 </label>
                            <div class="col-sm-5">
                                <input type="text" name="latitude"  value="{$data_info.latitude|default=''}" placeholder="输入 纬度" class="col-xs-10 col-sm-5" required/>
                                <span class="lbl col-xs-12 col-sm-7"><span class="red">*</span></span>
                            </div>
                        </div>


                        <div class="space-4"></div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label no-padding-right" for="form-field-1"> 活动名称 </label>
                            <div class="col-sm-10">
                                <input type="text" name="name" id="name" value="{$data_info.name|default=''}" placeholder="活动名称" class="col-xs-10 col-sm-5"/>
                            </div>
                        </div>



                        <div class="space-4"></div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label no-padding-right" for="form-field-1"> 活动寄语 </label>
                            <div class="col-sm-10">
                                <input type="text" name="description" id="description" value="{$data_info.description|default=''}" placeholder="活动寄语" class="col-xs-10 col-sm-5"/>
                            </div>
                        </div>



                        <div class="space-4"></div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label no-padding-right" for="form-field-1"> 活动开始时间 </label>
                            <div class="col-sm-10">
                                <input type="text" name="start_time" id="start_time" value="{$edit ? date('Y-m-d H:i',$data_info.start_time) : ''}" placeholder="活动开始时间" class="col-xs-10 col-sm-5 datetimepicker"/>
                                <span class="lbl col-xs-12 col-sm-7"><span class="red">*</span></span>
                            </div>
                        </div>

                        <div class="space-4"></div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label no-padding-right" for="form-field-1"> 活动结束时间 </label>
                            <div class="col-sm-10">
                                <input type="text" name="end_time" id="end_time" value="{$edit ? date('Y-m-d H:i',$data_info.end_time) : ''}" placeholder="活动结束时间" class="col-xs-10 col-sm-5 datetimepicker"/>
                                <span class="lbl col-xs-12 col-sm-7"><span class="red">*</span></span>
                            </div>
                        </div>


                        <div class="space-4"></div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label no-padding-right" for="phone"> 咨询电话 </label>
                            <div class="col-sm-10">
                                <input type="text" name="phone" id="phone" value="{$data_info.phone|default=''}" placeholder="输入咨询号码" class="col-xs-10 col-sm-5" required/>
                                <span class="lbl col-xs-12 col-sm-7"><span class="red">*</span></span>
                            </div>
                        </div>


                        <div class="space-4"></div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label no-padding-right" for="form-field-1"> 活动名额 </label>
                            <div class="col-sm-10">
                                <input type="number" name="num" id="num" value="{$data_info.num|default='100'}" placeholder="活动名额" class="col-xs-10 col-sm-5"/>
                            </div>
                        </div>


                        <div class="space-4"></div>

                        <?php $field_name = 'image'; ?>
                        <?php $thumb = $data_info[$field_name] ?? []; ?>
                        {include file="piece/thumb_upload_piece" field_desc="活动封面图片" image_size=""/}

                        <div class="space-4"></div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label no-padding-right" for="form-field-1"> 排序 </label>
                            <div class="col-sm-10">
                                <input type="text" name="sort" id="sort" value="{$data_info.sort|default='50'}" placeholder="输入排序" class="col-xs-10 col-sm-5"/>
                                <span class="lbl col-xs-12 col-sm-7"><span class="red">*</span>从小到大排序</span>
                            </div>
                        </div>


                        <div class="space-4"></div>

                        <?php if ($edit): ?>
                            <div id="business_div" >
                                <div class="form-group">
                                    <label class="col-sm-2 control-label no-padding-right" for="form-field-1"> 选择主办人员 </label>
                                    <div class="col-sm-10">
                                        <div class="col-xs-10 col-sm-5">
                                            <table class="table table-striped table-bordered table-hover">
                                                <thead>
                                                <tr>
                                                    <th class="center">
                                            <span data-param="member_group,admin_id[]" data-callback="choose_business" data-href="{:folder_url('Common/choose_people',['choose_all'=>true])}" class="purple new-row choose" >
                                                <i class="ace-icon fa fa-plus-circle bigger-130"></i>
                                            </span>
                                                    </th>
                                                    <th>姓名</th>
                                                    <th>称号</th>
                                                    <th class="th-operate">操作</th>
                                                </tr>
                                                </thead>
                                                <tbody id="fields-data">
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>

                                <div class="space-4"></div>
                            </div>
                        <?php endif; ?>


                        <div class="space-4"></div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label no-padding-right" for="form-field-1"> 活动详情 </label>
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

<table id="data_table">
    <tr>
        <td><input type="hidden" name="admin_id[]" value=""/></td>
        <td class="admin_realname"></td>
        <td class="admin_telephone"></td>
        <td class="th-operate">
            <div class="action-buttons">
                <a class="red field-delete" data-toggle="tooltip" title="删除">
                    <i class="ace-icon fa fa-trash-o bigger-130"></i>
                </a>
            </div>
        </td>
    </tr>
</table>

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
<script src="__STATIC__/layui/layui/layui.js"></script>
<script src="__STATIC__/wangEditor/dist-3.1.0/wangEditor.min.js"></script>
<script>


    $(function () {

        <?php isset($data_info['admin_ids']) OR $data_info['admin_ids'] = ''; ?>
        <?php if($data_info['admin_ids']):?>
        var url = "{:folder_url('Ajax/people_list',['id'=>$data_info.admin_ids])}";
        $.get(url, function (data) {
            data.data.forEach(function (value) {
                console.log(value)
                choose_business(value);
            });
        }, 'json');
        <?php endif;?>

        init_editor('editor', 'detail_id', '{$data_info.detail_id|default=0}');

        $('.datetimepicker').each(function () {
            laydate.render({
                elem: this,
                type: 'datetime',
                format: 'yyyy-MM-dd HH:mm'
            });
        });
    });


    $('body').on('click', '.field-delete', function () {
        var index = $('#fields-data tr').index($(this).closest('tr'));
        $('#fields-data').find('tr').eq(index).remove();
    });

    layui.config({
        base: '__STATIC__/layui/mods/'
        , version: '1.0'
    });
    layui.use(['layer', 'form', 'layarea'], function () {
        var layer = layui.layer
            , form = layui.form
            , layarea = layui.layarea;

        layarea.render({
            elem: '#area-picker',
            change: function (res) {
                //选择结果
                console.log(res);
            }
        });
    });


    // 活动人员选择回调
    function choose_business(member) {
        var data_table = $('#data_table');
        var html = data_table.find('tr').eq(0).clone();
        html.find('.admin_realname').html(member['name']);
        html.find('.admin_telephone').html(member['title']);
        html.find('[name="admin_id[]"]').val(member['id']);
        $("#fields-data").append(html);
    }



    //文件回调
    function file_callback(data) {
        var html = $('#image_div').find('div').clone();
        html.find('[name="image_id[]"]').val(data['file_id']);
        html.find('img').attr('src', data['full_url']);
        $('#image_data').append(html);
    }

</script>
{/block}
