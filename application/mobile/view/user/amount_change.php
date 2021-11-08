{extend name="public/base" /}

{block name="main-content"}
<div class="mobile-wrap center">
    <div class="header" style="position:fixed;z-index:999;top:0;left:0;">
        <a href="{$return_url}"><img src="__MODULE_IMG__/ic21.png" alt=""></a>
    </div>
    <div class="pers-inform" style="position: relative;top:1.01rem;left:0;">
        <form class="ajax-form" action="{$full_url}" method="post">
            <div>
                <div class="label fl" style="color: darkred">1奖金币 = 1报单币，转换比例：1:1。</div>
            </div>
            <div style=" border-bottom: 1px solid #ccc;">
                <label>转换金额：</label>
                <input style="width: 70%;height: 0.6rem;" type="tel" maxlength="8" placeholder="请输入需要转换的报单币金额" name="money"/>
            </div>
        </form>
    </div>
    <div class="null"></div>
    <div class="fixed-btn center"><a id="submit" href="javascript:">转换</a></div>
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

        $('#yzm_tx').click(function () {

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
                url: "{:folder_url('Sms/add_receivables_card')}",
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
        set_sms_count_down(value, '#yzm_phone2', function (view, second) {
            view.text('重新发送(' + second + ')');
        }, function (view) {
            view.text('获取验证码');
        });
    }
</script>
{/block}
