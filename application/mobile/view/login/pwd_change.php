{extend name="public/base" /}

{block name="main-content"}
<div class="mobile-wrap center">
    <div class="header" style="position:fixed;z-index:999;top:0;left:0;">
        <a href="{:folder_url('User/personal')}"><img src="__MODULE_IMG__/ic21.png" alt=""></a>
    </div>
    <form class="ajax-form" action="{$full_url}" method="post" style="position: relative;top:0.71rem;left:0;">
        <div class="pers-inform">
            <form>
                <div>
                    <label>旧密码</label>
                    <input type="password" id="old_password" name="old_password" placeholder="请输入原始密码">
                </div>
                <div>
                    <label>新密码</label>
                    <input type="password" id="new_password" name="new_password" placeholder="请输入新密码">
                </div>
                <div>
                    <label>确认密码</label>
                    <input type="password" id="check_password" name="check_password" placeholder="请确认密码">
                </div>
            </form>
        </div>
        <div class="null"></div>
        <div class="fixed-btn center"><a id="submit" href="javascript:">完成</a></div>
    </form>
</div>
{/block}

{block name="hide-content"}
{/block}

{block name="scripts"}
<script>
    $(function () {
        $('#submit').click(function () {
            $('.ajax-form').submit();
        })
    });

    // 提交验证
    function check_form() {
        var old_password = $('#old_password').val();
        if (old_password === '') {
            show_message('请输入原密码！');
            return false;
        }

        var new_password = $('#new_password').val();
        if (new_password === '') {
            show_message('请输入新密码！');
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
