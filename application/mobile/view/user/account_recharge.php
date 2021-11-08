{extend name="public/base" /}

{block name="main-content"}
<div class="mobile-wrap center">
    <div class="header" style="position:fixed;z-index:999;top:0;left:0;">
        <a href="{$return_url}"><img src="__MODULE_IMG__/ic21.png" alt=""></a>
        <h1>账户充值</h1>
    </div>
    <form class="ajax-form" action="{$full_url}" method="post" style="position: relative;top:1.01rem;left:0;">
        <div class="cz-box dt">
            <span>充值金额</span>
            <input type="text" name="money" placeholder="输入充值金额（元）">
        </div>
    </form>
    <div class="null"></div>
    <div class="fixed-btn center"><a href="javascript:" class="submit">立即充值</a></div>
</div>
{/block}

{block name="hide-content"}
{/block}

{block name="scripts"}
<script>
    $(function () {
        $('.submit').click(function () {
            $('.ajax-form').submit();
        })
    });

    function complete_success(data) {
        window.location.href = data.data
    }
</script>
{/block}
