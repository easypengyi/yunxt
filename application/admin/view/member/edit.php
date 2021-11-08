{extend name="public/base" /}
{block name="styles"}
<style>
    .btn-add-area,.btn-delete-area{
        color:#666;
        transition: all 300ms;
    }
    .btn-add-area:hover{
        color: rgb(111 179 224);
    }
    .btn-delete-area{
        color: rgb(203 108 108);
    }
    .btn-delete-area:hover{
        color: rgb(185 73 73);
    }
    .btn-add-area i,.btn-delete-area i{
        font-size: 20px !important;
    }
    .city_name{
        display: none;
        margin-top: -20px;
    }
</style>
{/block}
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
                    <label class="col-sm-2 control-label no-padding-right" for="group_id"> 所属职位 </label>
                    <div class="col-sm-10">
                        <?php isset($data_info['member_id']) OR $data_info['member_id'] = 0; ?>
                        <select name="group_id" id="group_id" data-href="{:folder_url('Ajax/select_member_group',['id'=>$data_info.member_id])}" class="col-xs-10 col-sm-5 selectpicker select-ajax" title="请选择" required></select>
                    </div>
                </div>


                <div class="space-4"></div>

                <div class="form-group">
                    <label class="col-sm-2 control-label no-padding-right" for="form-field-1"> 登录账号 </label>
                    <div class="col-sm-10">
                            <input type="tel"  name="member_tel" id="member_tel" maxlength="11" value="{$data_info.member_tel|default=''}" placeholder="输入手机号码" class="col-xs-10 col-sm-5" required/>
                        <span class="lbl col-xs-12 col-sm-7"><span class="red">*必填</span></span>
                    </div>
                </div>

                <div class="space-4"></div>

                <div class="form-group">
                    <label class="col-sm-2 control-label no-padding-right" for="form-field-1"> 登录密码 </label>
                    <div class="col-sm-10">
                        <input type="text" name="member_pwd" id="member_pwd" placeholder="输入登录密码" class="col-xs-10 col-sm-5" maxlength="15" minlength="6" <?php echo $edit ? '' : 'required' ?>/>
                        <span class="lbl col-xs-12 col-sm-7">
                            <?php if ($edit): ?>
                                保留为空，密码不修改，密码必须大于等于6位，小于等于15位
                            <?php else: ?>
                                <span class="red">*必填，密码必须大于等于6位，小于等于15位</span>
                            <?php endif; ?>
                        </span>
                    </div>
                </div>

                <div class="space-4"></div>

                <div class="form-group">
                    <label class="col-sm-2 control-label no-padding-right" for="form-field-1"> 用户姓名 </label>
                    <div class="col-sm-10">
                        <input type="text" name="member_realname" id="member_realname" value="{$data_info.member_realname|default=''}" placeholder="输入用户姓名" class="col-xs-10 col-sm-5"/>
                        <span class="lbl col-xs-12 col-sm-7"><span class="red">*必填</span></span>
                    </div>
                </div>

                <?php if ($edit): ?>

                    <div class="space-4"></div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label no-padding-right" for="form-field-1"> 佣金 </label>
                        <div class="col-sm-10">
                            <input type="number"  min="0" step="0.01" name="commission" id="commission" value="{$data_info.commission|default='0'}" placeholder="输入佣金" class="col-xs-10 col-sm-5"/>
                            <span class="lbl col-xs-12 col-sm-7"><span class="red"></span></span>
                        </div>
                    </div>

                <?php endif; ?>
                <div class="space-4"></div>
                    <div id="member_div">
                        <div class="form-group">
                            <label class="col-sm-2 control-label no-padding-right" for="form-field-1"> 推荐人</label>
                            <div class="col-sm-10">
                                <div class="input-group col-xs-10 col-sm-5">
                                    <span data-href="{:folder_url('Common/choose_member')}" data-param="" data-callback="choose_member" class="input-group-addon choose">选择推荐人</span>
                                    <input type="text" id="member_nickname" name="invitation_name" value="" placeholder="" class="form-control" readonly/>
                                    <input type="hidden" id="member_id" name="invitation_id" value=""/>
                                    <span class="input-group-addon choose-clear" data-input="invitation_name,invitation_id">清空</span>
                                </div>
                            </div>
                        </div>

                        <div class="space-4"></div>
                    </div>

                <div class="form-group">
                    <label class="col-sm-2 control-label no-padding-right" for="form-field-1"> 身份证号码 </label>
                    <div class="col-sm-10">
                        <input type="text" name="uid" id="uid" value="{$data_info.uid|default=''}" placeholder="输入身份证号码" class="col-xs-10 col-sm-5"/>
                        <span class="lbl col-xs-12 col-sm-7"><span class="red">*必填</span></span>
                    </div>
                </div>


                <div class="space-4"></div>

                <div class="form-group">
                    <label class="col-sm-2 control-label no-padding-right" for="form-field-1"> 报单服务中心 </label>
                    <div class="col-sm-10" style="padding-top:5px;">
                        <input type="checkbox" name="is_center" id="tolerant" <?php echo ($data_info['is_center'] ?? false) ? 'checked' : ''; ?> value="1" placeholder="" class="ace ace-switch ace-switch-6"/>
                        <span class="lbl">&nbsp;</span>
                    </div>
                </div>

                <div class="space-4"></div>

                {empty name="areas"}
                <div class="form-group" id="city_name">
                    <label class="col-sm-2 control-label no-padding-right" for="form-field-1"> 报单区域（县/区） </label>
                    <div class="col-sm-10">
                        <div class="layui-form">
                            <div class="layui-form-item" id="area-picker" style="align-items:center;display:flex">
                                <div class="layui-input-inline" style="width: 200px;">
                                    <select name="province[]" class="province-selector" data-value="{$data_info.province|default=''}" lay-filter="province-1">
                                        <option value="">请选择省</option>
                                    </select>
                                </div>
                                <div class="layui-input-inline" style="width: 200px;">
                                    <select name="city[]" class="city-selector" data-value="{$data_info.city|default=''}" lay-filter="city-1">
                                        <option value="">请选择市</option>
                                    </select>
                                </div>
                                <div class="layui-input-inline" style="width: 200px;">
                                    <select name="area[]" class="county-selector" data-value="{$data_info.area|default=''}" lay-filter="county-1">
                                        <option value="">请选择区</option>
                                    </select>
                                </div>
                                <span title="添加区域" class="btn-add-area">
                                    <i class="ace-icon fa fa-plus-circle bigger-110"></i>
                                </span>
                            </div>
                        </div>
                        
                    </div>
                </div>
                {/empty}
                {volist name="areas" id="v" offset="0" length='1'}
                <div class="form-group" id="city_name">
                    <label class="col-sm-2 control-label no-padding-right" for="form-field-1"> 报单区域（县/区） </label>
                    <div class="col-sm-10">
                        <div class="layui-form">
                            <div class="layui-form-item" id="area-picker" style="align-items:center;display:flex">
                                <div class="layui-input-inline" style="width: 200px;">
                                    <select name="province[]" class="province-selector" data-value="{$v.province|default=''}" lay-filter="province-1">
                                        <option value="">请选择省</option>
                                    </select>
                                </div>
                                <div class="layui-input-inline" style="width: 200px;">
                                    <select name="city[]" class="city-selector" data-value="{$v.city|default=''}" lay-filter="city-1">
                                        <option value="">请选择市</option>
                                    </select>
                                </div>
                                <div class="layui-input-inline" style="width: 200px;">
                                    <select name="area[]" class="county-selector" data-value="{$v.area|default=''}" lay-filter="county-1">
                                        <option value="">请选择区</option>
                                    </select>
                                </div>
                                <span title="添加区域" class="btn-add-area">
                                    <i class="ace-icon fa fa-plus-circle bigger-110"></i>
                                </span>
                            </div>
                        </div>
                        
                    </div>
                </div>
                {/volist}
                {volist name="areas" id="v" offset="1"}
                <div class="form-group city_name">
                    <label class="col-sm-2 control-label no-padding-right" for="form-field-1"></label>
                    <div class="col-sm-10">
                        <div class="layui-form">
                            <div class="layui-form-item area-picker" style="align-items:center;display:flex">
                                <div class="layui-input-inline" style="width: 200px;">
                                    <select name="province[]" class="province-selector" data-value="{$v.province|default=''}" lay-filter="province_{$v.id}">
                                        <option value="">请选择省</option>
                                    </select>
                                </div>
                                <div class="layui-input-inline" style="width: 200px;">
                                    <select name="city[]" class="city-selector" data-value="{$v.city|default=''}" lay-filter="city_{$v.id}">
                                        <option value="">请选择市</option>
                                    </select>
                                </div>
                                <div class="layui-input-inline" style="width: 200px;">
                                    <select name="area[]" class="county-selector" data-value="{$v.area|default=''}" lay-filter="county_{$v.id}">
                                        <option value="">请选择区</option>
                                    </select>
                                </div>
                                <span title="删除区域" class="btn-delete-area" onclick="removeArea(this)">
                                    <i class="ace-icon fa fa-times-circle bigger-110"></i>
                                </span>
                            </div>
                        </div>
                        
                    </div>
                </div>
                {/volist}


                

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
<script src="__STATIC__/layui/layui/layui.js"></script>
<script src="__STATIC__/laydate/dist-5.0.9/laydate.js"></script>
<!--suppress JSUnusedLocalSymbols -->
<script>
    $(function () {
        $('.datetimepicker').each(function () {
            laydate.render({
                elem: this,
                format: 'yyyy-MM-dd'
            });
        });

        var checked = $("#tolerant").is(':checked');
        if (checked == true){
            $("#city_name").show();
            $(".city_name").show();
        }else{
            $("#city_name").hide();
            $(".city_name").hide();
        }

        $("#tolerant").change(function() {
            var checked = $("#tolerant").is(':checked');
            if (checked == true){
                $("#city_name").show();
                $(".city_name").show();
            }else{
                $("#city_name").hide();
                $(".city_name").hide();
            }
        });

        choose_member({
            member_id: "{$data_info.top_id|default=''}",
            member_realname: "{$data_info.top_name|default=''}"
        });

        var areaIndex = 100;

        $('.btn-add-area').on('click',function(){

            areaIndex+=1;

            var picker_area=$('<div class="form-group city_name" style="display:block"><label class="col-sm-2 control-label no-padding-right" for="form-field-1"></label><div class="col-sm-10"><div class="layui-form"><div class="layui-form-item area-picker" style="align-items:center;display:flex"><div class="layui-input-inline" style="width: 200px;"><select name="province[]" class="province-selector" data-value="{$data_info.province|default=\'\'}" lay-filter="province-'+areaIndex+'"><option value="">请选择省</option></select></div><div class="layui-input-inline" style="width: 200px;"><select name="city[]" class="city-selector" data-value="{$data_info.city|default=\'\'}" lay-filter="city-'+areaIndex+'"><option value="">请选择市</option></select></div><div class="layui-input-inline" style="width: 200px;"><select name="area[]" class="county-selector" data-value="{$data_info.area|default=\'\'}" lay-filter="county-'+areaIndex+'"><option value="">请选择区</option></select></div><span title="删除区域" class="btn-delete-area" onclick="removeArea(this)"><i class="ace-icon fa fa-times-circle bigger-110"></i></span></div></div></div></div>')
            $('.form-actions').before(picker_area);

            var layarea = layui.layarea;
            layarea.render({
                elem: picker_area.find('.area-picker')[0],
                change: function (res) {
                    //选择结果
                    console.log(res);
                }
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

        $('.area-picker').each(function(){
            layarea.render({
                elem: this,
                change: function (res) {
                    //选择结果
                    console.log(res);
                }
            });
        })
        // layarea.render({
        //     elem: '.area-picker',
        //     change: function (res) {
        //         //选择结果
        //         console.log(res);
        //     }
        // });
    });

    function removeArea(obj){
        var jqObj=$(obj);
        jqObj.parents('.city_name').remove();
    }

    // 表单检查
    function check_form() {
        if (!$('#member_tel').val().match(/^(((13[0-9])|(14[57])|(15[0-9])|(16[6])|(17[0-9])|(18[0-9])|(19[8-9]))+\d{8})$/)) {
            alert_error('电话号码格式不正确！', function () {
                $('#member_tel').focus();
            });
            return false;
        }
    }

    // 会员选择回调
    function choose_member(member) {
        $('#member_id').val(member.member_id);
        $('#member_nickname').val(member.member_realname);
    }
</script>
{/block}
