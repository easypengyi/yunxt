{extend name="public/base" /}

{block name="before_scripts"}
{/block}

{block name="main-content"}
<div class="mobile-wrap center">
    <div class="header" style="position:fixed;z-index:999;top:0;left:0;">
        <a href="{$return_url}"><img src="__MODULE_IMG__/ic21.png" alt=""></a>
        <h1>开通会员</h1>
    </div>
    <form class="ajax-form" action="{$full_url}" method="post" style="position: relative;top:1.01rem;left:0;">
        <div class="open-member">
            <div class="open-addr">
                <?php if (isset($address) && !empty($address)): ?>
                    <input type="hidden" name="address_id" value="{$address.address_id}"/>
                    <h3>
                        <strong>
                            {$address.consignee}　{$address.mobile}
                        </strong>
                        <span>
                        {$address.province.name} {$address.city.name} {$address.district.name}{$address.address}
                    </span>
                        <i></i>
                    </h3>
                <?php else: ?>
                    <h3 style="height:1.3rem; line-height: 1.3rem;text-align: center; color: #bbb;">
                        新增收货地址
                        <i></i>
                    </h3>
                <?php endif; ?>
            </div>
            <div class="open-list">
                <ul>
                    <li>
                        <h4>
                            <span>开通服务</span>
                            <strong>ONCE会员</strong>
                        </h4>
                    </li>
                </ul>
            </div>
        </div>

        <div class="list-public">
            <strong>发票信息</strong>
            <span class="fp-class">不开发票</span>
            <input type="hidden" name="invoice_type" id="invoice_type" value="1"/>
        </div>

        <div class="fp-info fl none" id="invoice_content">
            <ul class="fl">
                <li>
                    <strong>发票抬头</strong>
                    <input type="text" name="invoice_head" id="invoice_head" placeholder="请输入公司发票抬头">
                </li>
                <li>
                    <strong>纳税人识别号</strong>
                    <input type="text" name="invoice_code" id="invoice_code" placeholder="请输入纳税人识别号">
                </li>
                <li>
                    <strong>邮箱</strong>
                    <input type="text" name="email" id="email" placeholder="请输入邮箱">
                </li>
            </ul>
        </div>
        <div class="null"></div>
        <div class="open-pay">
            <span>应付：￥{$data_info.leaguer_price}</span>
            <input type="hidden" name="money" value="{$data_info.leaguer_price}">
            <a href="javascript:" id="submit">去付款</a>
        </div>
    </form>

    <div class="pup-box center">
        <div id="drop2" class="drop-box">
            <h3>选择发票类型<i><img src="__MODULE_IMG__/ic20.png" alt=""></i></h3>
            <ul>
                <li data-type="1">不开票</li>
                <li data-type="2">个人</li>
                <li data-type="3">企业</li>
            </ul>
        </div>
    </div>
</div>
{/block}

{block name="hidden"}
{/block}

{block name="scripts"}
<!--suppress JSUnusedLocalSymbols -->
<script>
    $(function () {
        $('#submit').click(function () {
            $('.ajax-form').submit();
        });

        $('.open-addr').click(function () {
            window.location.href = "{:folder_url('User/address',['choose'=>true,'return_url'=>$full_url])}";
            return false;
        });

        $('.fp-class').click(function (event) {
            $('.pup-box').fadeIn().find('#drop2').addClass('acti');
        });

        $('#drop2 h3 i').click(function (event) {
            $('.pup-box').fadeOut().find('.drop-box').removeClass('acti');
        });

        $('#drop2 ul li').click(function (event) {
            var text = $(this).text();
            var type = $(this).data('type');
            var invoice_head = $('#invoice_head');
            var invoice_code = $('#invoice_code');
            var info_div = $('#invoice_content');
            $('#invoice_type').val(type);
            switch (type) {
                case 1:
                    info_div.hide();
                    break;
                case 2:
                    info_div.show();
                    invoice_head.attr('placeholder', '输入名字');
                    invoice_code.attr('placeholder', '身份证号码（选填）');
                    break;
                case 3:
                    info_div.show();
                    invoice_head.attr('placeholder', '请填写发票抬头');
                    invoice_code.attr('placeholder', '请填写纳税人识别号');
                    break;
            }
            $('.pup-box').fadeOut().find('.drop-box').removeClass('acti');
            $('.fp-class').text(text);
        });
    });
</script>
{/block}
