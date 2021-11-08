{extend name="public/base" /}

{block name="main-content"}
<div class="mobile-wrap center">
    <div class="container" style="    margin-top: 5rem;">
        <div class="login-box">
            <form class="ajax-form" action="{$full_url}" method="post">
                <ul>
                    <li>
                        <input type="tel" placeholder="请输入身份证号码" maxlength="18" id="mobile" name="uid">
                    </li>
                    <li>
                        <input type="submit" value="绑定">
                    </li>
                </ul>
            </form>
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

        $('#verify_bind_img').click(function () {
            $(this).attr('src', "{:folder_url('Sms/bind_phone_verify')}" + '?' + Math.random());
        });

        $('#yzm_pwd').click(function () {

            if ($(this).data('wait') !== 0) {
                return false;
            }

            var mobile = $('#mobile').val();
            if (!mobile.match(/^(((13[0-9])|(14[57])|(15[0-9])|(16[6])|(17[0-9])|(18[0-9])|(19[8-9]))+\d{8})$/)) {
                show_message('请输入正确手机格式！');
                return false;
            }

            sms_count_down(60);

            $.ajax({
                type: 'POST',
                url: "{:folder_url('Sms/bind_phone')}",
                data: {mobile: mobile},
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
        set_sms_count_down(value, '#yzm_pwd', function (view, second) {
            view.text('重新发送(' + second + ')');
            view.attr('style', 'background-color:#c8ccc8;color:#ffffff;');
        }, function (view) {
            view.text('获取验证码');
            view.attr('style', 'background-color:#00a4bc;color:#ffffff;');
        });
    }

    // 提交完成处理
    function complete(data) {
        if (data.code === 1) {
            window.location.href = data.url;
        } else {
            $('#verify_bind').val('');
            $('#verify_bind_img').click();
            show_message(data.msg);
        }
    }
</script>
{/block}
