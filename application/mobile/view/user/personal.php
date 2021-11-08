{extend name="public/base" /}

{block name="before_scripts"}
<style>
    .midd-pass ul li {
        border:unset !important;
    }
</style>
{/block}

{block name="main-content"}
<div class="mobile-wrap center">
    <div class="header" style="position:fixed;z-index:999;top:0;left:0;">
        <a href="{:folder_url('User/index')}"><img src="__MODULE_IMG__/ic21.png" alt=""></a>
    </div>
    <div class="midd-pass" style="position: relative;top:0.71rem;left:0;">
        <ul>
            <a href="{:folder_url('Login/pwd_change')}">
                <li>修改登录密码<i></i></li>
            </a>
        </ul>
    </div>
    <div class="fixed-btn center"><a class="exita">退出登录</a></div>
</div>
<div class="alert">
    <div class="exit">
        <h4>
            <img src="__MODULE_IMG__/alert-icon.png" alt=""/>
            <i>是否退出登录？</i>
        </h4>
        <h5>
            <a>取消</a>
            <a class="sub" href="{:folder_url('Login/logoff')}">确认</a>
        </h5>
    </div>
</div>
{/block}

{block name="hide-content"}
{/block}

{block name="scripts"}
<script>
    $(function () {
        $('.exita').click(function () {
            $('.alert').show();
            $('.exit').show();
        });
        $('.exit h5 a').click(function () {
            $('.exit').slideUp();
            $('.alert').hide();
        });
    });
</script>
{/block}
