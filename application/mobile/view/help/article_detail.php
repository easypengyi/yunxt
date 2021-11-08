{extend name="public/base" /}

{block name="before_scripts"}
{/block}

{block name="main-content"}
<div class="center">
    <div class="header"><a href="{$return_url}"><img src="__MODULE_IMG__/ic21.png" alt=""></a>
        <!--        <span class="mens"></span>-->
    </div>
    <div class="agreement">
        {$detail_url}
    </div>
</div>
{/block}


{block name="scripts"}
<script>
    $(function () {
        $('.header span.mens').click(function () {
            $('.alert').show();
            $('.slide-nav').animate({"top": 0 + "rem"}, 300)
        });
        $('.slide-nav h5 img.close').click(function () {
            $('.alert').hide();
            $('.slide-nav').animate({"top": -6.55 + "rem"}, 300)
        });
    });
</script>
{/block}
