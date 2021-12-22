{extend name="public/base" /}

{block name="before_scripts"}
<style>
    .main{
        background: url(/static/img/mobile/img_invite_2.png);
        background-size: 100%;
        width: 100%;
        height: calc(100vh - 0.7rem);
        display: flex;
        justify-content: center;
        align-items: flex-end;
        margin-top: 0.7rem;
    }
    .content{
        width: 6.34rem;
        height: 8.73rem;
        background: url(/static/img/mobile/img_invite_1.png);
        background-size: 100%;
        display: flex;
        flex-direction: column;
        align-items: center;
    }
    .avatar{
        width: 1.1rem;
        height: 1.1rem;
        border-radius: 50%;
        background: #000;
        margin-top: .9rem;
        overflow: hidden;
    }
    .qrcode{
        width: 2.4rem;
        height: 2.4rem;
        border: 1px #FFDCB9 solid;
        display: flex;
        justify-content: center;
        align-items: center;
    }
</style>
{/block}
{block name="main-content"}
<div class="mobile-wrap center">
    <div class="header" style="position:fixed;z-index:999;top:0;left:0;"><a href="{$return_url}"><img src="__MODULE_IMG__/ic21.png" alt=""></a>
        <h1>推广二维码</h1>
    </div>
    <div class="main">
        <div class="content">
            <div class="avatar">
                <img src="{$img}" style="width: 100%;height: 100%;"/>
            </div>
            <p style="margin-top: 0.1rem;margin-bottom: 0.1rem;">{$member.member_realname|default=''}</p>
            <div class="qrcode" id="qrcode">

            </div>
        </div>
    </div>
</div>
{/block}

{block name="hide-content"}

{/block}

{block name="scripts"}
<script src="__STATIC__/js/qrcode.min.js"></script>
<!--suppress JSUnusedLocalSymbols -->
<script>
    $(function () {
        var qrcode = new QRCode(document.getElementById("qrcode"), {
            text: '{$share_code}',
            width: 100,
            height: 100,
            correctLevel: QRCode.CorrectLevel.L
        });
    })

</script>
{/block}
