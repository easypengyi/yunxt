{extend name="public/base" /}

{block name="main-content"}
<div class="mobile-wrap center" style="background:#0c124e">
    <div class="header" style="position:fixed;z-index:999;top:0;left:0;"><a href="{$return_url}"><img src="__MODULE_IMG__/ic21.png" alt=""></a>

    </div>
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
