{extend name="public/base" /}

{block name="main-content"}
<div class="mobile-wrap center">
    <div class="header" style="border-bottom: none;position:fixed;z-index:999;top:0;left:0;">
        <a href="{$return_url}"><img src="__MODULE_IMG__/ic21.png" alt=""></a>
        <h1>我的优惠券</h1>
    </div>
    <div class="tabs" style="margin-bottom: 30px;position: fixed;top:1rem;left:0;width:100%;z-index:999;">
        <span data-id="1" class="acti"><i>通用券</i></span>
        <span data-id="2"><i>专用券</i></span>
    </div>
    <div class="item" id="coupon_data"  style="left:0;margin-top:2.01rem;">
        <div class="box b1">
            <ul>
            </ul>
        </div>
    </div>
</div>
{/block}

{block name="hide-content"}
<div id="coupon_list">
    <li>
        <div class="top">
            <strong><b class="coupon_value">200</b>元</strong>
            <div class="right">
                <span>有效期 </span>
                <var>2018.05.01 - 2018.06.01</var>
            </div>
        </div>
        <p>全场通用，购物满800元可使用</p>
    </li>
</div>
{/block}

{block name="scripts"}
<script>
    var coupon_type = 1;

    $(function () {
        coupon_list();

        //优惠券分类选择
        $('.tabs span').click(function () {
            $(this).addClass('acti').siblings().removeClass('acti');
            var id = parseInt($(this).data('id'));
            if (coupon_type === id) {
                return;
            }
            coupon_type = id;
            var coupon_data = $('#coupon_data');
            switch (coupon_type) {
                case 1:
                    coupon_data.find('div').removeClass('b2');
                    break;
                case 2:
                    coupon_data.find('div').addClass('b2');
                    break;
                default:
                    break;
            }
            coupon_data.find('ul').html('');
            coupon_list();
            return false;
        });
    });

    // 优惠券列表
    function coupon_list() {
        if (!coupon_type) {
            return;
        }
        $.ajax({
            type: 'POST',
            url: "{:folder_url('Ajax/coupon_list')}",
            data: {coupon_type: coupon_type},
            success: function (result) {
                if (result.code !== 1) {
                    show_message(result.msg);
                    return;
                }
                var data = result.data;

                data['list'].forEach(function (val) {
                    var li = $('#coupon_list').find('li').clone();
                    li.find('.coupon_value').html(val['value']);
                    var star_date = new Date(parseInt(val['start_time']) * 1000);
                    var star_year = star_date.getFullYear();
                    var star_month = star_date.getMonth() + 1;
                    star_month = star_month < 10 ? ('0' + star_month) : star_month;
                    var star_dates = star_date.getDate();
                    star_dates = star_dates < 10 ? ('0' + star_dates) : star_dates;
                    var star_time = star_year + '-' + star_month + '-' + star_dates;

                    var date = new Date(parseInt(val['end_time']) * 1000);
                    var year = date.getFullYear();
                    var month = date.getMonth() + 1;
                    month = month < 10 ? ('0' + month) : month;
                    var dates = date.getDate();
                    dates = dates < 10 ? ('0' + dates) : dates;
                    var end_time = year + '-' + month + '-' + dates;

                    if (val['time_limit']) {
                        li.find('.right var').html(star_time + '-' + end_time);
                    } else {
                        li.find('.right').html('无期限');
                    }

                    var str = val['product_limit'] ? '仅' + val['product_name'] + '使用' : '全场通用';
                    li.find('p').html(str + ',购物满' + val['fill'] + '元可使用');
                    $('#coupon_data').find('ul').append(li);
                });
            }
        });
    }
</script>
{/block}
