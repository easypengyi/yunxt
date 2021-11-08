{extend name="public/base" /}

{block name="main-content"}
<div class="mobile-wrap center">
    <div class="header" style="position:fixed;z-index:999;top:0;left:0;">
        <a href="{:folder_url('User/personal')}"><img src="__MODULE_IMG__/ic21.png" alt=""></a>
    </div>
    <div class="midd-pass" style="position: relative;top:1.01rem;left:0;">
        <ul>
            <a href="{:folder_url('Login/pwd_change')}"><li>修改登录密码<i></i></li></a>
<!--            <a href="{:folder_url('Login/phone_changeo')}"><li>修改手机号码<i></i></li></a>-->
        </ul>
    </div>
</div>
{/block}

{block name="hide-content"}
{/block}

{block name="scripts"}
{/block}
