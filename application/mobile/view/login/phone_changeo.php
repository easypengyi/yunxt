{extend name="public/base" /}

{block name="main-content"}
<div class="mobile-wrap center">
    <div class="header"  style="position:fixed;z-index:999;top:0;left:0;">
        <a href="{:folder_url('User/personal_sec')}"><img src="__MODULE_IMG__/ic21.png" alt=""></a>
        <h1>更换手机号</h1>
    </div>
    <div class="pers-inform"  style="position: relative;top:1.01rem;left:0;">
        <form class="ajax-form" action="{$full_url}" method="post">
            <input type="hidden" name="mobile" id="mobile" value="{$member.member_tel}">
            <div>
                <label>旧手机号</label>
                <em>{$member.member_tel}</em>
            </div>
            <div>
                <label>验证码</label>
                <input type="text" value="" placeholder="请输入右侧验证码" name="verify">
                <img src="{:folder_url('Sms/phone1_verify')}" id="verify_img" alt="" class="yzm" style="height: 30px;width: 80px;"/>
            </div>
            <div>
                <label>手机验证码</label>
                <input type="tel" maxlength="6" value="" placeholder="请输入手机验证码" name="code">
                <b>
                    <span id="yzm_phone1" data-wait="0" data-interval="0">获取验证码</span>
                </b>
            </div>
        </form>
    </div>
    <div class="null"></div>
    <div class="fixed-btn center"><a id="submit" href="javascript:">完成</a></div>
</div>
{/block}

{block name="hide-content"}
{/block}

{block name="scripts"}
<!--suppress JSUnusedLocalSymbols -->
<script>
    $(function () {
        $('#submit').click(function () {
            $('.ajax-form').submit();
        });

        $('#verify_img').click(function () {
            $(this).attr('src', "{:folder_url('Sms/phone1_verify')}" + '?' + Math.random());
        });

        $('#yzm_phone1').click(function () {
            var verify = $('input[name="verify"]').val();

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
                url: "{:folder_url('Sms/change_mobile')}",
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
        set_sms_count_down(value, '#yzm_phone1', function (view, second) {
            view.text('重新发送(' + second + ')');
        }, function (view) {
            view.text('获取验证码');
        });
    }

    // 提交完成处理
    function complete(data) {
        if (data.code === 1) {
            window.location.href = data.url;
        } else {
            $('#verify').val('');
            $('#verify_img').click();
            show_message(data.msg);
        }
    }
</script>
{/block}
