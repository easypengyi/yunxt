{extend name="public/base" /}

{block name="before_scripts"}
{/block}

{block name="main-content"}
<div class="mobile-wrap center">
    <div class="header" style="position: fixed;z-index:999;top:0;left:0;">
        <a href="{$return_url}"><img src="__MODULE_IMG__/ic21.png" alt=""></a>
        <h1>ONCE会员</h1>
    </div>
    <div class="onces" style="position: relative;top:1.01rem;left:0;">
        {:file_get_contents_no_ssl($data_info.leaguer_intro)}
    </div>
    <div class="null"></div>
    <?php if (!$member['activation']) : ?>
        <div class="fixed-btn center"><a href="{:controller_url('leaguer_open')}">开通会员</a></div>
    <?php endif; ?>
</div>
{/block}

{block name="hidden"}
{/block}

{block name="scripts"}
{/block}
