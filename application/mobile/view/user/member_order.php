{extend name="public/base" /}

{block name="before_scripts"}
{/block}

{block name="main-content"}
<div class="mobile-wrap center">
    <div class="header header-bt" style="position:fixed;z-index:999;top:0;left:0;">
        <a href="{$return_url}"><img src="__MODULE_IMG__/ic21.png" alt=""></a>
        <h1>会员订单</h1>
    </div>

    <?php if (isset($data_list) && !empty($data_list)): ?>
        <div class="order-list" style="top: 1rem;">
            <ul>
                <?php foreach ($data_list as $v): ?>
                    <li data-orderid="">
                        <h5>
                            <span class="order_sn">
                                订单号：{$v.order_sn}
                            </span>
                            <i class="order_time">{:date('Y-m-d H:i', $v.order_time)}</i>
                        </h5>
                        <div>
                            <h4>
                        <span>
                            <img class="product_image" src="__MODULE_IMG__/vip.png" alt=""/>
                        </span>
                            </h4>
                            <h3>
                                <strong class="product_name">
                                    开通会员
                                </strong>
                                <span>
                                <span class="product_price">￥{$v.amount}</span>
                                </span>
                            </h3>
                        </div>
                        <h6>
                            <span class="status">
                               {$status_array[$v.status]}
                            </span>
                            <strong>
                                共1件商品　合计：￥<b class="amount">{$v.amount}</b>
                            </strong>
                        </h6>
                        <?php if (in_array($v['status'], [4, 5])): ?>
                            <p class="order-button" style="line-height: .8rem">
                                <span class="fr">快递单号：{$v.courier_sn}</span>
                            </p>
                        <?php endif; ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>
</div>
{/block}

{block name="hide-content"}
{/block}

{block name="scripts"}
<!--suppress JSCheckFunctionSignatures, JSValidateTypes, JSUnresolvedFunction -->
<script>
</script>
{/block}
