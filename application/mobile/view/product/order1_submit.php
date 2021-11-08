{extend name="public/base" /}

{block name="before_scripts"}
<style>
    .list-public span {
         border: unset;
    }

</style>
{/block}

{block name="main-content"}
<div class="mobile-wrap center">
    <div class="header" style="z-index:999;top:0;left:0;position:fixed;">
        <a href="{$return_url}"><img src="__MODULE_IMG__/ic21.png" alt=""></a>
    </div>
    <form class="ajax-form" action="{$full_url}" method="post" style="margin-top: 0.71rem;" >
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
                                    <strong>{$value.product_name}</strong>
                                    <span style="color: #fa5b57;font-size: 0.28rem;">￥{$value.unit_price}</span>
                                    <?php if ($value['unit_price'] != $value['original_unit_price']): ?>
                                        <span style="font-size: 0.25rem;text-decoration: line-through;">￥{$value.original_unit_price}</span>
                                    <?php endif; ?>
                                </div>
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="list-public">
                    <strong>购买数量：</strong>
                    <span>{$value.product_num}</span><span>Ⅹ</span>
                </div>
                <p>共 <span style="color: #fa5b57;">{$value.product_num}</span> 件商品，合计<span style="color: #fa5b57;">￥</span><span id="money" style="color: #fa5b57;">{$value.money}</span></p>
            </div>
        <?php endforeach; ?>


        <?php if (isset($address_list['data']) && !empty($address_list['data'])): ?>
            <div class="receipt-list"  >
                <ul>
                    <a href="/mobile/user/address.html">
                        <li style="border-radius: unset;">
                            <input type="hidden" name= "address_id"  value="{$address_list.data.address_id}"/>
                            <h3><span>{$address_list.data.consignee}</span><var>{$address_list.data.mobile}</var></h3>
                            <p style="border-bottom: unset;"><span>{$address_list.data.province.name}{$address_list.data.city.name}{$address_list.data.district.name}{$address_list.data.address}</span><em style="float: right">编辑</em></p>
                        </li>
                    </a>
                </ul>
            </div>
        <?php else: ?>
            <div class="add_address"><a style="color: white;font-size: 0.3rem;" href="/mobile/user/address_add.html?choose=0">
                    （添加收货地址）
                </a>
            </div>
        <?php endif;?>

        <div class="null"></div>
        <div class="fixed-btn center" style="background: white;">
            <strong style="color: #fa5b57;font-size: 0.3rem;font-weight: bold;">应付： <span  style="color: #fa5b57;font-size: 0.3rem;font-weight: bold;" id="total_money">￥{$data_list.total_money}</span></strong>
            <a class="payment-a" href="#" style=" background-color: #095E3B;">去支付</a>
        </div>
    </form>

</div>
{/block}

{block name="hide-content"}

{/block}

{block name="scripts"}

<script>
    $(function () {
        $('.payment-a').click(function () {
            $('.ajax-form').submit();
            return false;
        });
    });

</script>
{/block}
