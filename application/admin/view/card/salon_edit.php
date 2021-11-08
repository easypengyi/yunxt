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
                    <label class="col-sm-2 control-label no-padding-right" for="form-field-1"> 活动描述 </label>
                    <div class="col-sm-10">
                        <input type="text" name="description" id="description" value="{$data_info.description|default=''}" placeholder="活动描述" class="col-xs-10 col-sm-5"/>
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

                <div class="form-group">
                    <label class="col-sm-2 control-label no-padding-right" for="form-field-1"> 活动时间 </label>
                    <div class="col-sm-10">
                        <input type="text" name="time" id="time" value="{$edit ? date('Y-m-d H',$data_info.time) : ''}" placeholder="活动时间" class="col-xs-10 col-sm-5 datetimepicker"/>
                        <span class="lbl col-xs-12 col-sm-7"><span class="red">*</span></span>
                    </div>
                </div>

                <div class="space-4"></div>

                <div class="form-group">
                    <label class="col-sm-2 control-label no-padding-right" for="form-field-1"> 活动报名开始时间 </label>
                    <div class="col-sm-10">
                        <input type="text" name="start_time" id="start_time" value="{$edit ? date('Y-m-d H:i',$data_info.start_time) : ''}" placeholder="开始时间" class="col-xs-10 col-sm-5 datetimepicker"/>
                        <span class="lbl col-xs-12 col-sm-7"><span class="red">*</span></span>
                    </div>
                </div>

                <div class="space-4"></div>

                <div class="form-group">
                    <label class="col-sm-2 control-label no-padding-right" for="form-field-1"> 活动报名结束时间 </label>
                    <div class="col-sm-10">
                        <input type="text" name="end_time" id="end_time" value="{$edit ? date('Y-m-d H:i',$data_info.end_time) : ''}" placeholder="结束时间" class="col-xs-10 col-sm-5 datetimepicker"/>
                        <span class="lbl col-xs-12 col-sm-7"><span class="red">*</span></span>
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
<script src="__STATIC__/layui/layui/layui.js"></script>
<script>


    $(function () {


        $('.datetimepicker').each(function () {
            laydate.render({
                elem: this,
                type: 'datetime',
                format: 'yyyy-MM-dd HH:mm'
            });
        });
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



</script>
{/block}
