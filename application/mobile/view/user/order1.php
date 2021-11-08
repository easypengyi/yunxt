{extend name="public/base" /}

{block name="before_scripts"}
<link rel="stylesheet" type="text/css" href="__STATIC__/minirefresh/dist-2.0.2/minirefresh.min.css"/>
<style>

    .order-list{
        background-size: 2.2rem 2rem;
        background-repeat: no-repeat;
        background-position: 2.75rem 3rem;
    }
    .order-tag ul li {
        width: 20%;
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
    <div class="order" style="margin-top:.71rem;left:0;">
        <div class="order-tag" style="position:fixed;z-index:999;top:.71rem;left:0;">
            <ul>
                <li class="category acti" data-id="0">全部</li>
                <li class="category" data-id="2">待付款</li>
                <li class="category" data-id="3">待发货</li>
                <li class="category" data-id="4">待收货</li>
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
        <div style="position:relative;">
            <h4>
                <span>
                    <img  style="width:1.5rem;height:1.5rem;" class="product_image" src="" alt=""/>
                </span>
            </h4>
            <h3>
                <span class="product_price"></span>
            </h3>
            <h3 >
                <span class="product_num"></span>
            </h3>
            <!-- <div style="position:absolute;right:0;top:0;height:100px;width:120px;display:flex;align-items:center;justify-content:center">
                <a class="payment-a" href="#" style="background-color:#095E3B;color:#fff;padding:12px">取消订单</a>
            </div> -->
        </div>
        <h6 style="overflow: hidden;text-overflow: ellipsis;" class="address_h6">
            <span style="color: black;">
                收货信息：
            </span>
            <span style="color: black;" class="address">
            </span>
        </h6>
        <h6>
            <span class="status">
            </span>
            <span class="wl_number" >
            </span>
            <img class="copy_img none"  src="__MODULE_IMG__/copy.png" style="margin-left: 0.2rem;width: 0.3rem">
            <strong>
                　价格：￥<b class="amount">1299</b>
            </strong>
        </h6>
        <p class="order-button" style="display:flex;justify-content:flex-end">
            <a href="{:controller_url('continue_pay1')}" class="pay none" style="margin-right: 10px;"><i>立即支付</i></a>
            <a class="shouhuo none"><i>确认收货</i></a>
            <a href="{:controller_url('cancel_order1')}" class="cancel none"><i>取消订单</i></a>
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
                        url: "{:folder_url('Ajax/order1_list')}",
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
                                html.find('.product_price').text('品名：'+ val['product_name']);
                                html.find('.product_num').text('数量：'+ val['product_num']+' 瓶');
                                html.find('.amount').text(val['amount']);
                                html.find('.status').text(status_array[val['status']]);
                                html.find('.address').text(val['address']['consignee']  +'  '+val['address']['mobile']+'    '+val['address']['city']+'  '+val['address']['district']+'  '+val['address']['address']);


                                switch (val['status']) {
                                    case 1:
                                        html.find('.order-button').remove();
                                        break;
                                    case 2:
                                        html.find('.cancel').show();
                                        html.find('.pay').show();
                                        break;
                                    case 3:
                                        // html.find('.order-button').remove();
                                        html.find('.cancel').show();
                                        break;
                                    case 4:
                                        html.find('.wl_number').text(val['courier_sn']);
                                        html.find('.shouhuo').show();
                                        html.find('.copy_img').show();
                                        html.find('.status').text(val['distribution']['name']+' ：');
                                        break;
                                    case 6:
                                        html.find('.order-button').remove();
                                        break;
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

        action();
        function action() {
            $('.category').removeClass('acti');
            var category = $('.category');
            $.each(category,function (i,v) {
                if ($(v).attr('data-id') == order_data.category){
                    $(v).addClass('acti');
                }
            })
        }



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
        }).on('click', '.cancel', function () {
            var li = $(this).parents('li');
            var order_id = li.data('orderid');
            show_confirm_dialog('确定要取消订单吗？', function () {
                $.ajax({
                    type: 'POST',
                    url: "{:folder_url('Ajax/order_cancel')}",
                    data: {order_id: order_id},
                    success: function (data) {
                        if (data.code !== 1) {
                            show_message(data.data.msg);
                            return;
                        }
                        show_message(data.data.msg);
                        li.remove();
                    }
                });
            });
            return false;
        }).on('click', '.shouhuo', function () {
            var li = $(this).parents('li');
            var order_id = li.data('orderid');
            show_confirm_dialog('是否确认收货？', function () {
                $.ajax({
                    type: 'POST',
                    url: "{:folder_url('Ajax/distribution_examine')}",
                    data: {order_id: order_id},
                    success: function (data) {
                        if (data.code !== 1) {
                            show_message(data.data.msg);
                            return;
                        }
                        show_message(data.data.msg);
                        li.remove();
                    }
                });
            });
            return false;
        }).on('click', '.copy_img', function () {
            var li = $(this).parents('li');
            var message = li.find('.wl_number').text();
            var input = document.createElement("input");
            input.value = message;
            document.body.appendChild(input);
            input.select();
            input.setSelectionRange(0, input.value.length), document.execCommand('Copy');
            document.body.removeChild(input);
            show_message('运单号复制成功');
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
