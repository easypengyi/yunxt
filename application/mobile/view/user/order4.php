{extend name="public/base" /}

{block name="before_scripts"}
<link rel="stylesheet" type="text/css" href="__STATIC__/minirefresh/dist-2.0.2/minirefresh.min.css"/>
<style>

    .order-list{
        background-size: 40% 22%;
        background-repeat: no-repeat;
        background-position: 2.5rem 2.5rem;
    }
</style>
{/block}

{block name="main-content"}
<div class="center">
    <div class="header header-bt" style="position:fixed;z-index:999;top:0;left:0;">
        <a href="{$return_url}">
            <img src="__MODULE_IMG__/ic21.png" alt="">
        </a>
    </div>
    <div class="order" style="margin-top:0.01rem;left:0;">
        <div class="order-tag" style="position:fixed;z-index:999;top:1.01rem;left:0;">
            <ul>
                <li class="category acti" data-id="0">全部</li>
                <li class="category" data-id="2">待支付</li>
                <li class="category" data-id="5">待检测</li>
                <li class="category" data-id="6">已完成</li>
            </ul>
        </div>
        <div class="order-list">
            <ul id="comprehensive" class="minirefresh-wrap">
                <div id="comprehensive-data" class="minirefresh-scroll">
                </div>
            </ul>
        </div>
    </div>
    <div class="null"></div>
</div>
{/block}

{block name="hide-content"}
<div id="order_data">
    <li data-orderid="">
        <h5>
            <span class="order_sn">
                订单号：H1234567890
            </span>
            <i class="order_time">2018-05-11 12:13</i>
        </h5>
        <div>
            <h4>
                <span>
                    <img class="product_image" src="" alt=""/>
                </span>
            </h4>
            <h3>
                <strong class="product_name">

                </strong>
                <span>
                    <span class="product_price"></span>
                </span>
            </h3>
        </div>
        <h6>
            <span class="status">
                待支付
            </span>
            <strong>
                　价格：￥<b class="amount">1299</b>
            </strong>
        </h6>
        <p class="order-button">
            <a href="{:controller_url('continue_pay')}" class="pay none"><i>立即支付</i></a>
        </p>
    </li>
</div>
{/block}

{block name="scripts"}
<script src="__STATIC__/minirefresh/dist-2.0.2/minirefresh.min.js"></script>
<!--suppress JSCheckFunctionSignatures, JSValidateTypes, JSUnresolvedFunction -->
<script>
    var order_data = {
        page: 1,
        category: '{$category}'
    };

    var status_array = JSON.parse('{:json_encode($status_array)}');

    $(function () {
        var comprehensive = new MiniRefresh({
            container: '#comprehensive',
            down: {
                isLock: true
            },
            up: {
                callback: function () {
                    $.ajax({
                        type: 'POST',
                        url: "{:folder_url('Ajax/order4_list')}",
                        data: {page: order_data.page, category: order_data.category},
                        success: function (result) {
                            if (result.code !== 1) {
                                show_message(result.msg);
                                return;
                            }

                            var data = result.data;
                            if (data['page'] === 1) {
                                $('#comprehensive-data').html('');
                            }

                            replaceUrl('category', order_data.category);

                            data['list'].forEach(function (val) {
                                var html = $('#order_data').find('li').clone();
                                html.data('orderid', val['order_id']);
                                html.find('.order_sn').text(val['order_sn']);
                                html.find('.order_time').text(format_time('yyyy-MM-dd hh:mm', val['order_time'] * 1000));
                                html.find('.product_image').attr('src', val['product_image']['full_url']);
                                html.find('.product_name').text('用户姓名：'+val['user_name']);
                                html.find('.product_price').text('手机号：'+val['user_phone']);
                                html.find('.amount').text(val['amount']);
                                html.find('.status').text(status_array[val['status']]);
                                switch (val['status']) {
                                    case 2:
                                        html.find('.pay').show();
                                        break;
                                    // case 3:
                                    // case 6:
                                    //     html.find('.order-button').remove();
                                }
                                $('#comprehensive-data').append(html);
                            });

                            if (data['list'].length == 0){
                                $('.order-list').css('background-image','url("__MODULE_IMG__/zwnrs.png")');
                            }else{
                                $('.order-list').css('background-image','unset');
                            }


                            if (data['page'] === data['total_page']) {
                                comprehensive.endUpLoading(true);
                                $('.minirefresh-upwrap').hide();
                            } else {
                                order_data.page = data['page'] + 1;
                                comprehensive.endUpLoading();
                                $('.minirefresh-upwrap').hide();
                            }
                        }
                    });
                    $('.minirefresh-upwrap').hide();
                }
            }
        });

        // 头部点击切换
        $('.category').click(function () {
            var old_category_id = order_data.category;
            order_data.category = $(this).data('id');

            if (old_category_id === order_data.category) {
                return;
            }

            order_data.page = 1;
            $('.category').removeClass('acti');
            $(this).addClass('acti');

            $('#comprehensive-data').html('');
            comprehensive.triggerUpLoading();

            return false;
        });


        <?php if (isset($order_payment) && !empty($order_payment)): ?>
        setTimeout(function () {
            $('.order-tag').find('li').eq(0).click();
        }, 1000);
        <?php endif; ?>

        $('body').on('click', '.evaluate', function () {
            var li = $(this).parents('li');
            var order_id = li.data('orderid');
            window.location.href = encode_url(this.href, {order_id: order_id, category: order_data.category});
            return false;
        }).on('click', '.pay', function () {
            var li = $(this).parents('li');
            var amount = li.find('.amount').text();
            var order_sn = li.find('.order_sn').text();
            var order_id = li.data('orderid');
            window.location.href = encode_url(this.href, {order_id: order_id, amount: amount, order_sn: order_sn});
            return false;
        });
    });

    /*
    * 替换当前url 并不导致浏览器页面刷新
    * name 参数名
    * value 参数值
    */
    function replaceUrl(name, value) {
        history.replaceState({name: value}, '', '?' + name + '=' + value);
    }
</script>
{/block}
