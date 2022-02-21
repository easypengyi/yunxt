{extend name="public/base" /}

{block name="main-content"}
<div class="mobile-wrap center">
    <div class="logo"><img src="__MODULE_IMG__/geno-icon.png" alt=""></div>
    <div class="container">
        <h3 class="two">注册账号</h3>
        <div class="login-box">
            <form class="ajax-form" action="{$full_url}" method="post">
                <ul>
                    <li>
                        <div class="pic"><img src="__MODULE_IMG__/phone.png" alt=""></div>
                        <input type="tel" placeholder="请输入手机号" maxlength="11" name="telephone" id="telephone">
                    </li>
                    <li>
                        <input type="text" placeholder="请输入右侧验证码" maxlength="5" name="verify" id="verify_reg">
                        <div class="yzm" style="width: 35%;"><img src="{:folder_url('Sms/reg_verify')}" id="verify_img" alt=""></div>
                    </li>
                    <li>
                        <input type="text" placeholder="请输入短信验证码" name="code">
                        <span id="yzm_reg" data-wait="0" data-interval="0" style="width: 35%;">获取验证码</span>
                    </li>
                    <li>
                        <div class="pic"><img class="img2" src="__MODULE_IMG__/password.png" alt=""></div>
                        <input type="password" placeholder="请输入密码" maxlength="20" name="password">
                    </li>
                    <li style="margin-bottom: .15rem;">
                        <div class="pic"><img class="img3" src="__MODULE_IMG__/invite-id.png" alt=""></div>
                        <input type="text" placeholder="请输入推荐人ID（选填）" name="invitation" id="invitation" value="{$invitation|default=''}">
                    </li>
                    <p><label><input type="checkbox" checked>我同意<a href="#">《基源巧盒服务协议》</a></label></p>
                    <li>
                        <input type="submit" value="注册">
                    </li>
                </ul>
            </form>
            <p><a href="{:folder_url('Login/index')}">已有账号，直接登录</a></p>
        </div>
    </div>
</div>
{/block}

{block name="hide-content"}
{/block}

{block name="scripts"}
<script>
    $(function () {
        $('body').removeClass('bg');
        $('#verify_img').click(function () {
            $(this).attr('src', "{:folder_url('Sms/reg_verify')}" + '?' + Math.random());
        });

        $('#yzm_reg').click(function () {
            var verify = $('#verify_reg').val();

            if ($(this).data('wait') !== 0) {
                return false;
            }

            var mobile = $('#telephone').val();
            if (!mobile.match(/^(((13[0-9])|(14[57])|(15[0-9])|(16[6])|(17[0-9])|(18[0-9])|(19[8-9]))+\d{8})$/)) {
                show_message('请输入正确手机格式！');
                return false;
            }

            sms_count_down(60);

            $.ajax({
                type: 'POST',
                url: "{:folder_url('Sms/register')}",
                data: {mobile: mobile, verify: verify},
                success: function (data) {
                    if (data.code !== 1) {
                        sms_count_down(0);
                    }
                    show_message(data.msg);
                }
            });
            return false;
        });
    });

    // 注册短信验证码倒计时
    function sms_count_down(value) {
        set_sms_count_down(value, '#yzm_reg', function (view, second) {
            view.text('重新发送(' + second + ')');
            //view.attr('style', 'color:#ffffff;width:30%;');
        }, function (view) {
            view.text('获取验证码');
            //view.attr('style', 'color:#ffffff;width:30%;');
        });
    }
</script>
{/block}
