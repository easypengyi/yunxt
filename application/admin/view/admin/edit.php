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
                    <label class="col-sm-2 control-label no-padding-right" for="group_id"> 管理员分组 </label>
                    <div class="col-sm-10">
                        <?php isset($data_info['admin_id']) OR $data_info['admin_id'] = 0; ?>
                        <select name="group_id" id="group_id" data-href="{:folder_url('Ajax/select_admin_group',['id'=>$data_info.admin_id])}" class="col-xs-10 col-sm-5 selectpicker select-ajax" title="请选择" required></select>
                    </div>
                </div>

                <div class="space-4"></div>

                <div class="form-group">
                    <label class="col-sm-2 control-label no-padding-right" for="admin_username"> 登录用户名 </label>
                    <div class="col-sm-10">
                        <input type="text" name="admin_username" id="admin_username" value="{$data_info.admin_username|default=''}" placeholder="输入登录用户名" class="col-xs-10 col-sm-5" <?php echo $edit ? 'readonly' : 'required'; ?>/>
                        <span class="lbl col-xs-12 col-sm-7"><span class="red">*</span>用户名必须是字母，数字，符号</span>
                    </div>
                </div>

                <div class="space-4"></div>

                <div class="form-group">
                    <label class="col-sm-2 control-label no-padding-right" for="admin_pwd"> 登录密码 </label>
                    <div class="col-sm-10">
                        <input type="text" name="admin_pwd" id="admin_pwd" maxlength="15" minlength="6" value="" placeholder="输入登录密码<?php echo $edit ? ',为空不更改' : ''; ?>" class="col-xs-10 col-sm-5" <?php echo $edit ? '' : 'required'; ?>/>
                        <span class="lbl col-xs-12 col-sm-7"><span class="red">*</span>密码必须大于等于6位，小于等于15位</span>
                    </div>
                </div>

                <div class="space-4"></div>

                <div class="form-group">
                    <label class="col-sm-2 control-label no-padding-right" for="admin_email"> 用户邮箱 </label>
                    <div class="col-sm-10">
                        <input type="email" name="admin_email" id="admin_email" value="{$data_info.admin_email|default=''}" placeholder="输入用户邮箱" class="col-xs-10 col-sm-5" required/>
                        <span class="lbl col-xs-12 col-sm-7"><span class="red">*</span>用于密码找回，请认真填写</span>
                    </div>
                </div>

                <div class="space-4"></div>

                <div class="form-group">
                    <label class="col-sm-2 control-label no-padding-right" for="admin_tel"> 手机号码 </label>
                    <div class="col-sm-10">
                        <input type="number" name="admin_tel" id="admin_tel" value="{$data_info.admin_tel|default=''}" placeholder="输入手机号码" class="col-xs-10 col-sm-5" required/>
                        <span class="lbl col-xs-12 col-sm-7"><span class="red">*</span>只能填写数字</span>
                    </div>
                </div>

                <div class="space-4"></div>

                <div class="form-group">
                    <label class="col-sm-2 control-label no-padding-right" for="admin_realname"> 姓名 </label>
                    <div class="col-sm-10">
                        <input type="text" name="admin_realname" id="admin_realname" value="{$data_info.admin_realname|default=''}" placeholder="输入姓名" class="col-xs-10 col-sm-5" required/>
                        <span class="lbl col-xs-12 col-sm-7"><span class="red">*</span></span>
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
<!--suppress JSUnusedLocalSymbols -->
<script>
    // 表单检查
    function check_form() {
        var admin_username = $.trim($('input[name=admin_username]').val()); //获取INPUT值
        var myReg = /^[\u4e00-\u9fa5]+$/;//验证中文
        if (admin_username.indexOf(' ') >= 0) {
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
</script>
{/block}
