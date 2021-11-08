{extend name="public/base" /}

{block name="before_scripts"}
 <style>
     .header {
         padding: unset;
     }

 </style>
{/block}

{block name="main-content"}
<div class="center">
    <div class="header"><a href="{$return_url}"><img src="__MODULE_IMG__/ic21.png" alt=""></a>
        <div class="agreement">
            {:file_get_contents_no_ssl($detail_url)}
        </div>

</div>
{/block}


{block name="scripts"}
<script>
</script>
{/block}
