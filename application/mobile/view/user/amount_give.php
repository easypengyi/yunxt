{extend name="public/base" /}

{block name="main-content"}
<div class="mobile-wrap center">
    <div class="header" style="position:fixed;z-index:999;top:0;left:0;">
        <a href="{$return_url}"><img src="__MODULE_IMG__/ic21.png" alt=""></a>
    </div>
    <div class="pers-inform" style="position: relative;top:0.71rem;left:0;">
        <form class="ajax-form" action="{$full_url}" method="post">
            <div>
                <label>云库存数量：</label>
                <input style="width: 70%;height: 0.6rem;" type="number" maxlength="8" placeholder="请输入转赠的云库存" name="balance"/>
            </div>
            <div>
                <label>转赠人手机：</label>
                <input type="number" placeholder="绑定的手机号" name="mobile"/>
            </div>
        </form>
    </div>
    <div class="null"></div>
    <div class="fixed-btn center"><a id="submit" href="javascript:">交易</a></div>
</div>
{/block}

{block name="hide-content"}
{/block}

{block name="scripts"}
<!--suppress JSUnusedLocalSymbols -->
<script>
    $(function () {
        $('#submit').click(function () {
            $('.ajax-form').submit();
        });

    });

</script>
{/block}
