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
                    <label class="col-sm-2 control-label no-padding-right" for="form-field-1"> 推荐人手机号 </label>
                    <div class="col-sm-10">
                        <input type="tel"  name="top_tel" id="top_tel" maxlength="11" placeholder="请输入手机号码" class="col-xs-10 col-sm-5" required/>
                        <span class="lbl col-xs-12 col-sm-7"><span class="red">*必填</span></span>
                    </div>
                </div>

                <div class="space-4"></div>

                <div class="form-group">
                    <label class="col-sm-2 control-label no-padding-right" for="form-field-1"> 被推荐人手机号 </label>
                    <div class="col-sm-10">
                        <input type="tel"  name="member_tel" id="member_tel" maxlength="11" placeholder="请输入手机号码" class="col-xs-10 col-sm-5" required/>
                        <span class="lbl col-xs-12 col-sm-7"><span class="red">*必填</span></span>
                    </div>
                </div>

                <div class="space-4"></div>

                <div class="form-group">
                    <label class="col-sm-2 control-label no-padding-right" for="group_id"> 选择经销级别 </label>
                    <div class="col-sm-10">
                        <select name="group_id" id="group_id" class="col-xs-10 col-sm-5 selectpicker" title="请选择" required>
                            <option value="2" >代理人</option>
                            <option value="3" >执行董事</option>
                            <option value="4" >全球合伙人</option>
                            <option value="5" >联合创始人</option>
                        </select>
                    </div>
                </div>

                <div class="space-4"></div>

                <div class="form-group">
                    <label class="col-sm-2 control-label no-padding-right" for="is_admin"> 选择结算方式 </label>
                    <div class="col-sm-10">
                        <select name="is_admin" id="is_admin" class="col-xs-10 col-sm-5 selectpicker" title="请选择" required>
                            <option value="1" >云库存结算</option>
                            <option value="0" >平台结算</option>
                        </select>
                    </div>
                </div>

<!--                <div class="space-4"></div>-->
<!---->
<!--                <div class="form-group">-->
<!--                    <label class="col-sm-2 control-label no-padding-right" for="form-field-1"> 接点人手机号 </label>-->
<!--                    <div class="col-sm-10">-->
<!--                        <input type="tel"  name="two_mobile" id="two_mobile" maxlength="11" placeholder="请输入手机号码" class="col-xs-10 col-sm-5"/>-->
<!--                        <span class="lbl col-xs-12 col-sm-7"></span>-->
<!--                    </div>-->
<!--                </div>-->
<!---->
<!--                <div class="space-4"></div>-->
<!---->
<!--                <div class="form-group">-->
<!--                    <label class="col-sm-2 control-label no-padding-right" for="form-field-1"> 接点人姓名 </label>-->
<!--                    <div class="col-sm-10">-->
<!--                        <input type="text" name="two_name" id="two_name" placeholder="接点人姓名" class="col-xs-10 col-sm-5"/>-->
<!--                        <span class="lbl col-xs-12 col-sm-7"></span>-->
<!--                    </div>-->
<!--                </div>-->

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
</script>
{/block}
