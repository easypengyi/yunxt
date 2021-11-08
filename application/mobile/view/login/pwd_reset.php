{extend name="public/base" /}

{block name="main-content"}
<div class="mobile-wrap center">
    <div class="logo"><img src="__MODULE_IMG__/geno-icon.png" alt=""></div>
    <div class="container">
        <h3>重置密码</h3>
        <div class="login-box">
            <form class="ajax-form" action="{$full_url}" method="post">
                <ul>
                    <li>
                        <div class="pic"><img class="img2" src="__MODULE_IMG__/password.png" alt=""></div>
                        <input type="password" id="new_password" name="new_password" placeholder="请输入密码" maxlength="20">
                    </li>
                    <li class="tb">
                        <div class="pic"><img class="img2" src="__MODULE_IMG__/password.png" alt=""></div>
                        <input type="password" id="check_password" name="check_password" placeholder="再次输入新密码" maxlength="20">
                    </li>
                    <li>
                        <input type="submit" value="提交">
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
    $('body').removeClass('bg');

    // 提交验证
    function check_form() {
        var new_password = $('#new_password').val();
        if (new_password === '') {
            show_message('请输入密码！');
            return false;
        }

        var check_password = $('#check_password').val();
        if (check_password === '') {
            show_message('请输入确认密码！');
            return false;
        }

        if (new_password !== check_password) {
            show_message('新密码和确认密码不相同！');
            return false;
        }

        return true;
    }
</script>
{/block}
