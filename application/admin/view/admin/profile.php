{extend name="public/base" /}
{block name="main-content"}
<?php isset($data_info) OR $data_info = []; ?>

<div class="page-content">
    <div class="row top20">
        <div class="col-xs-12">
            <div id="user-profile-1" class="user-profile row">
                <div class="col-xs-12 col-sm-3 center">
                    <div>
                        <span class="profile-picture">
                            <a class="single_thumb" data-callback="thumb_callback">
                                <img id="headpic" src="https://ydn-product.oss-cn-hangzhou.aliyuncs.com/upload/image/admin_img.jpg" class="current-headpic" width="150" alt=""/>
                            </a>
                        </span>

                        <div class="space-4"></div>
                        <div class="width-80 label label-info label-xlg arrowed-in arrowed-in-right">
                            <div class="inline position-relative">
                                <i class="ace-icon fa fa-circle light-green"></i>
                                &nbsp;
                                <span class="white">{$data_info.group_name|default=''}</span>
                            </div>
                        </div>
                    </div>

                    <div class="space-6"></div>

                    <div class="profile-contact-info">
                        <div class="profile-contact-links">
                        <span id="edit" class="btn btn-link" data-toggle="modal" data-target="#eidt_modal">
                            <i class="ace-icon fa fa-pencil bigger-120 green"></i>
                            修改个人信息
                        </span>
                        </div>
                        <div class="space-6"></div>
                    </div>
                </div>

                <div class="col-xs-12 col-sm-9">
                    <div class="profile-user-info profile-user-info-striped">
                        <div class="profile-info-row">
                            <div class="profile-info-name">用户名</div>

                            <div class="profile-info-value">
                                <span class="editable">{$data_info.admin_username|default=''}</span>
                            </div>
                        </div>

                        <div class="profile-info-row">
                            <div class="profile-info-name">联系电话</div>

                            <div class="profile-info-value">
                                <span class="editable">{$data_info.admin_tel|default='未设置'}</span>
                            </div>
                        </div>

                        <div class="profile-info-row">
                            <div class="profile-info-name">真实姓名</div>

                            <div class="profile-info-value">
                                <span class="editable">{$data_info.admin_realname|default='未设置'}</span>
                            </div>
                        </div>
                    </div>

                    <div class="space-20"></div>
                </div>
            </div>
        </div>
    </div>
</div>
{/block}
{block name="modal"}
<!-- 显示修改资料模态框（Modal） -->
<div class="modal fade in" id="eidt_modal" tabindex="-1" role="dialog" aria-labelledby="eidt_modal_title" aria-hidden="true">
    <form class="form-horizontal ajax-form" method="post" action="{$current_url}">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <h4 class="modal-title" id="eidt_modal_title"> 修改个人信息 </h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-xs-12">
                            <div class="form-group">
                                <label class="col-sm-2 control-label no-padding-right" for="form-field-1">
                                    用户邮箱
                                </label>
                                <div class="col-sm-10">
                                    <input type="email" name="admin_email" id="admin_email" value="{$data_info.admin_email|default=''}" placeholder="" class="col-xs-10 col-sm-5" required/>
                                    <span class="lbl col-xs-12 col-sm-7"><span class="red">*</span>用于密码找回，请认真填写</span>
                                </div>
                            </div>

                            <div class="space-4"></div>

                            <div class="form-group">
                                <label class="col-sm-2 control-label no-padding-right" for="form-field-1">
                                    原登录密码
                                </label>
                                <div class="col-sm-10">
                                    <input type="text" name="old_pwd" id="old_pwd" maxlength="15" minlength="6" placeholder="为空不修改密码" class="col-xs-10 col-sm-5"/>
                                    <span class="lbl col-xs-12 col-sm-7"><span class="red">*</span>密码必须大于等于6位，小于等于15位</span>
                                </div>
                            </div>

                            <div class="space-4"></div>

                            <div class="form-group">
                                <label class="col-sm-2 control-label no-padding-right" for="form-field-1">
                                    新登录密码
                                </label>
                                <div class="col-sm-10">
                                    <input type="text" name="new_pwd" id="new_pwd" maxlength="15" minlength="6" placeholder="为空不修改密码" class="col-xs-10 col-sm-5"/>
                                    <span class="lbl col-xs-12 col-sm-7"><span class="red">*</span>密码必须大于等于6位，小于等于15位</span>
                                </div>
                            </div>

                            <div class="space-4"></div>

                            <div class="form-group">
                                <label class="col-sm-2 control-label no-padding-right" for="form-field-1">
                                    手机号码
                                </label>
                                <div class="col-sm-10">
                                    <input type="number" name="admin_tel" id="admin_tel" value="{$data_info.admin_tel|default=''}" placeholder="" class="col-xs-10 col-sm-5" required/>
                                    <span class="lbl col-xs-12 col-sm-7"><span class="red">*</span>只能填写数字</span>
                                </div>
                            </div>

                            <div class="space-4"></div>

                            <div class="form-group">
                                <label class="col-sm-2 control-label no-padding-right" for="form-field-1">
                                    姓名
                                </label>
                                <div class="col-sm-10">
                                    <input type="text" name="admin_realname" id="admin_realname" value="{$data_info.admin_realname|default=''}" placeholder="" class="col-xs-10 col-sm-5" required/>
                                    <span class="lbl col-xs-12 col-sm-7"><span class="red">*</span>用于发布信息所有人，且在前端显示</span>
                                </div>
                            </div>

                            <div class="space-4"></div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-primary">
                        提交保存
                    </button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">
                        关闭
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>
{/block}

{block name="scripts"}
<!--suppress JSUnusedLocalSymbols -->
<script>
    $(function () {

    });

    // 表单检查
    function check_form() {
        var admin_username = $.trim($('input[name=admin_username]').val()); //获取INPUT值
        var myReg = /^[\u4e00-\u9fa5]+$/;//验证中文
        if (admin_username.indexOf(" ") >= 0) {
            alert_error('登录用户名包含了空格，请重新输入！', function () {
                $('#admin_username').focus();
            });
            return false;
        }
        if (myReg.test(admin_username)) {
            alert_error('用户名必须是字母，数字，符号！', function () {
                $('#admin_username').focus();
            });
            return false;
        }
        if (!$('#admin_tel').val().match(/^(((13[0-9])|(14[57])|(15[0-9])|(16[6])|(17[0-9])|(18[0-9])|(19[8-9]))+\d{8})$/)) {
            alert_error('电话号码格式不正确！', function () {
                $('#admin_tel').focus();
            });
            return false;
        }
        return true;
    }

    // 图片选择回调
    function thumb_callback(url, full_url, file_id) {
        $.post('{:folder_url("Ajax/admin_headpic_change")}', {file_id: file_id}, function (data) {
            if (data.code === 1) {
                $('.current-headpic').attr('src', full_url);
            }
            else {
                alert_error(data.msg);
            }
        });
    }
</script>
{/block}
