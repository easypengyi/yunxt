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
        width: 33.3%;
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
        <div class="order-tag" style="position:fixed;z-index:999;top:0.71rem;left:0;">
            <ul>
                <li class="category acti" data-id="0">全部</li>
                <li class="category" data-id="3">待审核</li>
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
                <strong class="product_price">
                </strong>
                <strong class="product_level">
                </strong>
                <strong class="product_type">
                </strong>
            </h3>
        </div>
        <h6>
            <span class="status">
                待支付
            </span>
            <strong>
                　产品数量:<b class="amount">1299</b>
            </strong>
        </h6>
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
                        url: "{:folder_url('Ajax/order_list')}",
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
                                html.find('.product_name').text('用户姓名：'+val['nick_name']);
                                html.find('.product_price').text('联系电话：'+val['mobile']);

                                switch (val['product_id']) {
                                    case 0:
                                        html.find('.product_type').text('结算方式：平台结算');
                                        break;
                                    case 1:
                                        html.find('.product_type').text('结算方式：云库存结算');
                                        break;
                                        default:
                                         html.find('.product_type').text('结算方式：平台结算');
                                         break;

                                }

                                switch (val['product_id']) {
                                    case 1:
                                        html.find('.product_level').text('经销级别：云系统创客');
                                        break;
                                    case 2:
                                        html.find('.product_level').text('经销级别：代理人');
                                        break;
                                    case 3:
                                        html.find('.product_level').text('经销级别：执行董事');
                                        break;
                                    case 4:
                                        html.find('.product_level').text('经销级别：全球合伙人');
                                        break;
                                    case 5:
                                        html.find('.product_level').text('经销级别：联合创始人');
                                        break;

                                }

                                html.find('.amount').text(val['product_num']);
                                html.find('.status').text(status_array[val['status']]);
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
