{extend name="public/base" /}

{block name="before_scripts"}
<link rel="stylesheet" type="text/css" href="__MODULE_CSS__/payment.css?version={$file_version}"/>
{/block}

{block name="main-content"}
<div class="grzx" style="overflow: hidden">
    <div class="grzx-c">
        <!--头部开始-->
        <div class="grzx-ct">
            <a href="{$return_url}" class="grzx-ctl"></a>
            <span class="grzx-ctm">订单支付</span>
        </div>
        <!--头部结束-->
        <!--内容开始-->
        <div class="grzx-cx grzx-cx2">
            <!--订单支付开始-->
            <div class="ddzf">
                <div class="ddzf_cont">
                    <p>待付款</p>
                    <span class="ddzf_cont_em">
                        <em>该笔订单支付金额<b><?php if (isset($order_data) && !empty($order_data)): ?>{$order_data.money}<?php endif; ?></b></em>
                        <em>交易方式：<b style="color:#B3B3B3;">微信支付</b></em>
                    </span>
                    <input style="background-color: #095E3B;font-size: 15px;" class="ddzf_cont_ipt" type="button" onclick="callpay()" value="立即支付"/>
                </div>
            </div>
            <!--订单支付结束-->
        </div>
        <?php echo base64_decode($data_info['payment']);?>
        <!--内容结束-->
    </div>
</div>
{/block}

{block name="scripts"}
<!--suppress JSUnresolvedVariable -->
<script>
    //调用微信JS api 支付
    function jsApiCall() {
        WeixinJSBridge.invoke(
            'getBrandWCPayRequest',
            JSON.parse('{:base64_decode($data_info.payment)}'),
            function (res) {
                WeixinJSBridge.log(res.err_msg);
                if (res.err_msg === 'get_brand_wcpay_request:ok') {
                    window.location.href = "{:folder_url('Payment/payment_finish')}";
                } else {
                    //返回跳转到订单详情页面
                    alert('支付失败');
                }
            }
        );
    }

    function callpay() {
        if (typeof WeixinJSBridge === 'undefined') {
            if (document.addEventListener) {
                document.addEventListener('WeixinJSBridgeReady', jsApiCall, false);
            } else if (document.attachEvent) {
                document.attachEvent('WeixinJSBridgeReady', jsApiCall);
                document.attachEvent('onWeixinJSBridgeReady', jsApiCall);
            }
        } else {
            jsApiCall();
        }
    }
</script>
{/block}
