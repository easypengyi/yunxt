{extend name="public/base" /}

{block name="main-content"}
<div class="mobile-wrap center">
    <div class="header header-bt" style="position:fixed;z-index:999;top:0;left:0;">
        <a href="{$return_url}">
            <img src="__MODULE_IMG__/ic21.png" alt="">
        </a>
    </div>
    <form class="ajax-form" action="{$full_url}" method="post" style=" margin-top: 0.71rem;">
        <input type="hidden" name="order_id" value="{$order_data.order_id}"/>
<!--        <div class="cz-box dt">-->
<!--            <div>-->
<!--                订单号：{$order_data.order_sn}-->
<!--            </div>-->
<!--            <div>-->
<!--                金额：{$order_data.money}-->
<!--            </div>-->
<!--        </div>-->
        <div class="pay-way dt">
            <h3>选择支付方式</h3>
            <?php if (isset($payment_list) && !empty($payment_list)): ?>
                <ul>
                    <?php foreach ($payment_list as $k => $v): ?>
                        <?php if ($type == 'recharge' && $v['id'] == \tool\PaymentTool::BALANCE): ?>
                            <?php continue; ?>
                        <?php endif; ?>
                        <li>
                            <label>
                                <?php if ($v['id'] == \tool\PaymentTool::ALIPAY): ?>
                                    <span>{$v.name}</span>
                                <?php elseif ($v['id'] == \tool\PaymentTool::WXPAY): ?>
                                    <span class="s2">{$v.name}</span>
                                <?php elseif ($v['id'] == \tool\PaymentTool::UPACPAY): ?>
                                    <span class="s3">{$v.name}</span>
                                <?php elseif ($v['id'] == \tool\PaymentTool::BALANCE): ?>
                                    <span class="s4">{$v.name}</span>
                                <?php endif; ?>
                                <input type="radio" name="payment_id" value="{$v.id}" <?php echo $k == 0 ? 'checked' : '' ?>>
                            </label>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
    </form>
    <div class="null"></div>
    <div class="fixed-btn center"><a href="javascript:" class="submit">立即支付</a></div>
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
