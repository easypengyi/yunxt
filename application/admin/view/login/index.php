{extend name="public/base" /}
{block name="styles"}
<style>
    .verify_img {
        cursor: pointer;
        width: 100%;
        border: 1px solid #d5d5d5;
    }
</style>
{/block}
{block name="main-content"}
<?php isset($name) OR $name = ''; ?>
<?php isset($verify) OR $verify = false; ?>

<div class="row">
    <div class="col-sm-10 col-sm-offset-1">
        <div class="login-container">
            <div class="center">
                <h1>
                    <i class="ace-icon fa fa-leaf" style="color:#69CA72;"></i>
                    <span class="red"><?php echo $name ?></span>
                    <span  style="color:#fff;font-family:microsoft yahei,serif" id="id-text2">后台管理</span>
                </h1>
            </div>

            <div class="space-6"></div>

            <div class="position-relative">
                <div id="login-box" class="login-box visible widget-box no-border">
                    <div class="widget-body">
                        <div class="widget-main">
                            <h4 class="header lighter bigger text-center" style="color:#69CA72;">
                                <i class="ace-icon fa fa-coffee" style="color:#69CA72;"></i> 后台登录
                            </h4>

                            <div class="space-6"></div>
                            <form class="ajax-form" method="post" action="{$current_url}">
                                <fieldset>
                                    <label class="block clearfix">
                                        <span class="block input-icon input-icon-right">
                                            <input type="text" class="form-control" name="username" id="username" placeholder="用户名" required/>
                                            <i class="ace-icon fa fa-user"></i>
                                        </span>
                                    </label>

                                    <label class="block clearfix">
                                        <span class="block input-icon input-icon-right">
                                            <input type="password" class="form-control" name="pwd" id="pwd" placeholder="输入密码" required/>
                                            <i class="ace-icon fa fa-lock"></i>
                                        </span>
                                    </label>

                                    <?php if ($verify): ?>
                                        <label class="block clearfix">
                                            <span class="block input-icon input-icon-right">
                                                <input type="text" class="form-control" name="verify" id="verify" placeholder="输入验证码" required/>
                                                <i class="ace-icon fa fa-sort-alpha-asc"></i>
                                            </span>
                                        </label>
                                        <label class="block clearfix">
                                            <span class="block text-center">
                                                <img class="verify_img" id="verify_img" src="{:controller_url('verify')}" title="点击获取" alt=""/>
                                            </span>
                                        </label>
                                    <?php endif; ?>

                                    <div class="clearfix">
                                        <button type="submit" class="width-35 pull-right btn btn-sm btn-primary">
                                            <i class="ace-icon fa fa-key"></i>
                                            <span class="bigger-110">登录</span>
                                        </button>
                                    </div>

                                    <div class="space-4"></div>
                                </fieldset>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
{/block}

{block name="scripts"}
<!--suppress JSUnusedLocalSymbols -->
<script>
    $(function () {
        $('body').addClass('login-layout blur-login');

        <?php if ($verify): ?>
        $('#verify_img').click(function () {
            $(this).attr('src', "{:controller_url('verify')}" + '?' + Math.random());
        });
        <?php endif; ?>
    });

    // 提交完成处理
    function complete(data) {
        if (data.code === 1) {
            window.location.href = data.url;
        } else {
            <?php if ($verify): ?>
            $('#verify').val('');
            $('#verify_img').click();
            <?php endif; ?>
            alert_error(data.msg);
        }
    }
</script>
{/block}
