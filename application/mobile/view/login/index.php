{extend name="public/base" /}

{block name="main-content"}
<div class=" mobile-wrap center">
    <div class="header" style="position:fixed;z-index:999;top:0;left:0;">
        <a href="/mobile/Index/index.html"><img src="/static/img/mobile/ic21.png" alt=""></a>
    </div>
    <div class="logo"><img src="https://ydn-product.oss-cn-hangzhou.aliyuncs.com/upload/image/geno-icon.png" alt=""></div>
    <div class="container">
        <div class="login-box">
            <form class="ajax-form" action="{$full_url}" method="post">
                <input type="hidden" name="type" value="2">
                <input type="hidden" name="return_url" value="{$return_url}"/>
                <ul>
                    <li>
                        <div class="pic"><img src="__MODULE_IMG__/phone.png" alt=""></div>
                        <input type="tel" placeholder="请输入手机号" name="telephone" maxlength="11"/>
                    </li>
                    <li class="tb">
                        <div class="pic"><img class="img2" src="__MODULE_IMG__/password.png" alt=""/></div>
                        <input type="password" placeholder="请输入密码" name="password" maxlength="20"/>
                    </li>
                    <li>
                        <input type="submit" value="登录"/>
                    </li>
                </ul>
            </form>

            <p> <a  href="{:folder_url('Login/oauth_wx')}"><img style="width: 1.2rem; margin-top: 0.5rem;" src="__MODULE_IMG__/wx_login.png"></a></p>
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

        tabs_cg('.login-tabs span', '.login-box', 'click', 'acti', '', 0);

        $('#verify_img').click(function () {
            $(this).attr('src', "{:folder_url('Sms/login_verify')}" + '?' + Math.random());
        });

        $('#yzm_login').click(function () {
            var verify = $('#verify_code').val();

            if ($(this).data('wait') !== 0) {
                return false;
            }


            var mobile = $('#telephone_login').val();
            if (!mobile.match(/^(((13[0-9])|(14[57])|(15[0-9])|(16[6])|(17[0-9])|(18[0-9])|(19[8-9]))+\d{8})$/)) {
                show_message('请输入正确手机格式！');
                return false;
            }

            login_sms_count_down(60);

            $.ajax({
                type: 'POST',
                url: "{:folder_url('Sms/login')}",
                data: {mobile: mobile, verify: verify},
                success: function (data) {
                    if (data.code !== 1) {
                        login_sms_count_down(0);
                    }
                    show_message(data.msg);
                }
            });
            return false;
        });
    });

    // 登录短信验证码倒计时
    function login_sms_count_down(value) {
        set_sms_count_down(value, '#yzm_login', function (view, second) {
            view.text('重新发送(' + second + ')');
            view.attr('style', 'background-color:#c8ccc8;color:#ffffff;');
        }, function (view) {
            view.text('获取验证码');
            view.attr('style', 'background-color:#00a4bc;color:#ffffff;');
        });
    }
</script>
{/block}
