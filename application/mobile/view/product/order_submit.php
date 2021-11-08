{extend name="public/base" /}

{block name="before_scripts"}
{/block}

{block name="main-content"}
<div class="mobile-wrap center">


    <form class="ajax-form" action="{$full_url}" method="post">
        <?php foreach ($data_list['order'] as $key => $value): ?>
            <div class="list-public2 order-info">
                <input type="hidden" class="product_id" value="{$value.product_id}"/>
                <input type="hidden" class="order_money" value="{$value.money}"/>
                <div class="box">
                    <ul>
                        <li>
                            <a href="#">
                                <div class="pic"><img src="{$value.product_image.full_url}" alt=""></div>
                                <div class="text">
                                     <strong>报单姓名：{$value.nick_name}</strong>
                                     <strong>报单手机：{$value.mobile}</strong>
                                </div>
                            </a>
                        </li>
                    </ul>
                </div>
                <p>共 <span>1</span> 件商品，合计<span id="money">{$value.money}</span></p>
            </div>
        <?php endforeach; ?>


        <div class="null"></div>

        <div class="fixed-btn center">
            <strong>应付： <span  id="total_money">{$data_list.total_money}</span></strong>
            <a class="payment-a" href="#">去报单</a>
        </div>
    </form>

    </div>
</div>
{/block}

{block name="hide-content"}
<div id="coupon_data">
    <div class="offer-list" data-coupon="">
        <!-- l2或l3追加则变更背景颜色 -->
        <div class="left">￥<b class="coupon-value">300</b></div>
        <div class="text">
            <strong>
                <!-- c2或c3追加则变更文字颜色 -->
                <span>通用券</span><b class="coupon-name"></b>
            </strong>
            <p class="coupon-time">2018.05.01 - 2018.06.30</p>
        </div>
    </div>
</div>
{/block}

{block name="scripts"}
<!--suppress JSUnusedLocalSymbols -->
<script>
    $(function () {

        $('.payment-a').click(function () {
            $('.ajax-form').submit();
            return false;
        });
    });

</script>
{/block}
