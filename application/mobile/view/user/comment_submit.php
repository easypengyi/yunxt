{extend name="public/base" /}

{block name="main-content"}
<div class="mobile-wrap center">
    <div class="header" style="position:fixed;z-index:999;top:0;left:0;">
        <a href="{$return_url}"><img src="__MODULE_IMG__/ic21.png" alt=""></a>
        <h1>评价订单</h1>
    </div>
    <form class="ajax-form" action="{$full_url}" method="post" style="position: relative;top:1.01rem;left:0;">
        <input type="hidden" name="product_id" value="{$order_detail.product_id}"/>
        <div class="evaluation-order">
            <div class="eva-ord-box">
                <h4>
                    <img class="product_image" src="{$order_detail.product_image.full_url}" alt=""/>
                </h4>
                <h3>
                    {$order_detail.product_name}
                </h3>
                <h6>
                    <span>
                        <em>评分</em>
                        <b id="score">
                            <i></i>
                            <i></i>
                            <i></i>
                            <i></i>
                            <i></i>
                        </b>
                        <input type="hidden" name="score" value="0"/>
                    </span>
                </h6>
                <h5>
                    <textarea name="content" id="content" placeholder="请输入评价内容"></textarea>
                </h5>
            </div>
        </div>
        <div class="null"></div>
        <div class="fixed-btn center">
            <a href="javascript:" class="public-button">发表评价</a>
        </div>
    </form>
</div>
{/block}

{block name="hide-content"}
{/block}

{block name="scripts"}
<script>
    $(function () {
        $('#score').click(function () {
            $('input[name=score]').val($(this).children('i.acti').length);
        });

        $('.public-button').click(function () {
            $('.ajax-form').submit();
        })
    });
</script>
{/block}
