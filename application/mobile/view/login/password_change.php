{extend name="public/base" /}

{block name="main-content"}
<div class="mobile-wrap center">
    <div class="header">
        <a href="{:folder_url('User/personal_sec')}"><img src="__MODULE_IMG__/ic21.png" alt=""></a>
        <h1>修改登录密码</h1>
    </div>
    <div class="pers-inform">
        <form>
            <div>
                <label>旧密码</label>
                <input type="password" value="" placeholder="请输入新手机号">
            </div>
            <div>
                <label>新密码</label>
                <input type="password" value="" placeholder="请输入新手机号">
            </div>
            <div>
                <label>确认密码</label>
                <input type="password" value="" placeholder="请输入新手机号">
            </div>
        </form>
    </div>
    <div class="null"></div>
    <div class="fixed-btn center"><a href="#">完成</a></div>
</div>
{/block}

{block name="hide-content"}

{/block}

{block name="scripts"}
{/block}
