{extend name="public/base" /}

{block name="main-content"}
<div class="mobile-wrap center" >
    <div class="header" style="position:fixed;z-index:999;top:0;left:0;">
        <a href="{$return_url}"><img src="__MODULE_IMG__/ic21.png" alt=""></a>
    </div>
    <div class="invitation-card" style="position: relative;top:1.01rem;left:0;">
        <h2 style="color: #ccc;">核销码</h2>
    </div>
    <?php $qr_code = helper\HttpHelper::qr_code_url(url('mobile/user/write_off', ['order_id'=>$order_id], true, true));?>
    <div class="qr-code"><img src="{$qr_code}" alt=""></div>
</div>
{/block}

{block name="hide-content"}
{/block}

{block name="scripts"}
<script>
    $(function () {
        $('body').addClass('bg2')
    })
</script>
{/block}
