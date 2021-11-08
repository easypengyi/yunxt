{extend name="public/base" /}

{block name="main-content"}
<div class="mobile-wrap center">
    <div class="header" style="position:fixed;z-index:999;top:0;left:0;"><a href="{$return_url}"><img src="__MODULE_IMG__/ic21.png" alt=""></a>
        <h1>邀请好友</h1>
    </div>
    <div class="invite dt" style="margin-top:1.01rem;left:0;">
        <img src="__MODULE_IMG__/invi_icon.png" alt="">
        <div class="text">
            <strong>邀请好友得现金奖励</strong>
            <p>邀请好友成功注册巧盒基因，您和TA均可获20元现金奖励。邀请越多可获得现金越多，赶紧行动起来吧！</p>
        </div>
    </div>
    <div class="tips dt"><span>任选以下方法可获得奖励</span></div>
    <div class="invite-list dt">
        <ul>
            <li>
                <div class="round"><b>1</b>方法</div>
                <p>点击本页底部的”复制链接“按钮，通过QQ、微信等工具将链接发送给好友，好友通过链接进入巧盒基因官网注册页，注册成功即可获得奖励。</p>
            </li>
            <li>
                <div class="round"><b>2</b>方法</div>
                <p>点击本页底部的”生成邀请函“按钮，将生成的邀请函（可截图或长按保存）发送给好友，好友通过识别邀请函上的二维码进入巧盒基因官网注册页，注册成功即可获得奖励。</p>
            </li>
            <li>
                <div class="round"><b>3</b>方法</div>
                <p>告诉好友您的用户ID：{$member.invitation_code}，并让好友登录巧盒基因官网进行注册，注册的时候在推荐人ID栏填入您的用户ID，注册成功即可获得奖励。</p>
            </li>
        </ul>
    </div>
    <div class="null"></div>
    <input type="text" id="invitation_url" value="{:folder_url('Login/register',['invitation' =>$member.invitation_code], true, true)}" placeholder="" style="width:1px;"/>
    <div class="fixed-btn center">
        <a class="a1" href="javascript:" id="copy_invitation" data-clipboard-action="copy" data-clipboard-target="#invitation_url">复制链接</a>
        <a class="a2" href="{:controller_url('invi_card')}">生成邀请函</a>
    </div>
</div>
{/block}

{block name="hide-content"}

{/block}

{block name="scripts"}
<script src="__JS__/clipboard.min.js"></script>
<!--suppress JSUnusedLocalSymbols -->
<script>
    $(function () {
        var clipboard = new ClipboardJS('#copy_invitation');
        clipboard.on('success', function (e) {
            show_message('复制成功,请将链接发送给好友');
        });
    })

</script>
{/block}
