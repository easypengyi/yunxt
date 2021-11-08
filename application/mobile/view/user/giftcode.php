{extend name="public/base" /}

{block name="main-content"}
<div class="mobile-wrap center">
    <div class="header" style="position:fixed;z-index:999;top:0;left:0;">
        <a href="{$return_url}"><img src="__MODULE_IMG__/ic21.png" alt=""></a>
        <h1>礼券码</h1>
    </div>
    <form class="ajax-form" action="{$full_url}" method="post" style="position: relative;top:1.01rem;left:0;">
        <div class="pers-inform">
            <form>
                <div>
                    <label>礼券码</label>
                    <input type="text" name="code" id="code" placeholder="请输入礼券码"/>
                </div>
            </form>
        </div>
        <div class="null"></div>
        <div class="fixed-btn center">
            <button class="btms">立即获取</button>
        </div>
    </form>
</div>
{/block}

{block name="hide-content"}
{/block}

{block name="scripts"}
<script>
    $('.btms').click(function () {
        $('.ajax-form').submit();
    })
</script>
{/block}