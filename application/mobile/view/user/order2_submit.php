{extend name="public/base" /}

{block name="before_scripts"}
{block name="styles"}
<style>

    #buy_number{
        width: 0.7rem;
        text-align: center;
        color: #8a8a8a;
        font-size: 0.35rem;
        background: #f4f4f4;
        margin-left: 0.2rem;
        margin-right: 0.2rem;
        line-height: 0.5rem;
        height: 0.5rem;
    }
</style>
{/block}

{block name="main-content"}
<div class="mobile-wrap center">
    <div class="header header-bt" style="position:fixed;z-index:999;top:0;left:0;">
        <a href="{$return_url}">
            <img src="__MODULE_IMG__/ic21.png" alt="">
        </a>
    </div>
    <form class="ajax-form" action="{$full_url}" method="post"  style=" margin-top: 1.1rem">
        <?php foreach ($data_list['order'] as $key => $value): ?>
            <div class="list-public2 order-info" >
                <input type="hidden" class="product_id" value="{$value.product_id}"/>
                <input type="hidden" class="order_money" value="{$value.money}"/>
                <input type="hidden" id="product_num"  name="product_num" value="1"/>

                <div class="box">
                    <ul>
                        <li>
                            <a href="#">
                                <div class="pic"><img src="{$value.product_image.full_url}" alt=""></div>
                                <div class="text">
                                    <strong>{$value.product_name}</strong>
                                    <span style="color: #fa5b57;">￥</span><span id="money" style="color: #fa5b57;font-size: 0.2rem;font-weight: bold;">{$value.unit_price}</span>
                                </div>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        <?php endforeach; ?>
        <div class="inform-related" style=" height: 1rem; line-height: 1rem;">
            &nbsp;&nbsp;&nbsp;发货数量：
            <div style=" float: right;margin-right: 0.5rem;">
                <b class="buy_ddd"  style="font-size: 0.6rem;">-</b>
                <input type="number" id="buy_number" value="1">
                <b class=" buy_add"  style="font-size: 0.45rem;">+</b>
            </div>
        </div>

        <?php if (isset($address_list['data']) && !empty($address_list['data'])): ?>
            <div class="receipt-list" >
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
                    添加收货地址
                </a>
            </div>
        <?php endif;?>


        <div class="null"></div>

        <div class="fixed-btn center">
            <strong>应付库存： <span  id="total_money">1</span></strong>
            <a class="payment-a" href="#">去发货</a>
        </div>
    </form>

    </div>
</div>
{/block}


{block name="scripts"}

<script>
    $(function () {

        $('.payment-a').click(function () {

            show_confirm_dialog('确定要发货吗？', function () {
                $('.ajax-form').submit();
            });

        });


        //数量增加+
        $(".buy_add").click(function () {
            var strong = $('#buy_number').val();
            if(strong < 100){
                strong++;
            }
            $('#buy_number').val(strong);
            $('#product_num').val(strong);
            money_change();
        });
        //数量减少
        $(".buy_ddd").click(function () {
            var strong = $('#buy_number').val();
            if (strong > 1){
                strong--;
            }
            $('#buy_number').val(strong);
            $('#product_num').val(strong);
            money_change();
        });
    });

    // 金额变更
    function money_change() {
        var number = $('#buy_number').val();
        $('#total_money').html(number)
    }

</script>
{/block}
