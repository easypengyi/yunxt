{extend name="public/base" /}
{block name="before_scripts"}
<link rel="stylesheet" type="text/css" href="__STATIC__/minirefresh/dist-2.0.2/minirefresh.min.css"/>
{/block}
{block name="styles"}
<style>
    .minirefresh-totop {
        bottom: 60px;
    }
</style>
{/block}
{block name="main-content"}
<div class="mobile-wrap center">
    <div class="search search-bg">
        <a href="{:folder_url('Search/index')}">
            <div class="sea-col sea-line">
                <input type="submit" name="" id="" value=""/>
                <input type="text" readonly name="" id="" value="" placeholder="输入商品名称"/>
            </div>
        </a>
    </div>
    <div class="main">
        <div class="menu-fl fl">
            <?php if (isset($category_list) && !empty($category_list)): ?>
                <ul>
                    <?php foreach ($category_list as $v): ?>
                        <a class="category_id" href="javascript:" data-id="{$v.category_id}">
                            <li class="{$category_id == $v.category_id ? 'acti' : ''}">
                                <h3>
                                    <b>{$v.name}</b>
                                    <span>
                                    <em>{$v.order_number}/{$v.product_number}</em>
                                    <?php $percent = empty($v['product_number']) ? 0 : round($v['order_number'] * 100 / $v['product_number'],
                                        2) ?>
                                    <small><i style="width: <?php echo $percent ?>%"></i></small>
                                </span>
                                </h3>
                            </li>
                        </a>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
        <div class="prod-col">
            <div class="prod-tag show minirefresh-wrap" id="comprehensive" style="display: block;">
                <ul id="comprehensive-data" class="minirefresh-scroll" style="margin-bottom: 1.8rem;">

                </ul>
            </div>
        </div>
    </div>
</div>

<!--底部菜单栏开始-->
{include file='public/footer' activate='2'/}
<!--底部菜单栏结束-->
{/block}

{block name="hide-content"}
<div id="comprehensive_product">
    <li data-href="{:folder_url('Product/detail')}" data-productid="0" class="detail">
        <h4>
        <span class="thumb">
            <img src="__MODULE_IMG__/ceshi1.png" alt=""/><i style="display: none;">已检测</i>
        </span>
        </h4>
        <div>
            <h3 class="product_name">大咖 - 咖啡因代谢基因检测</h3>
            <h6>
                <span class="current_price">￥1299</span>
                <!--                <b><img src="__MODULE_IMG__/pro-cart.png" alt=""/></b>-->
            </h6>
        </div>
    </li>
</div>
{/block}
{block name="scripts"}
<script src="__STATIC__/minirefresh/dist-2.0.2/minirefresh.min.js"></script>
<!--suppress JSUnresolvedFunction -->
<script>
    var product_data = {
        category_id: '{$category_id}',
        page: 1
    };

    $(function () {
        // 订单列表下拉刷新组件
        var comprehensive = new MiniRefresh({
            container: '#comprehensive',
            down: {
                isLock: true,
            },
            up: {
                isAuto: true,
                callback: function () {
                    $.ajax({
                        type: 'POST',
                        url: "{:folder_url('Ajax/product_list')}",
                        data: product_data,
                        success: function (result) {
                            if (result.code !== 1) {
                                show_message(result.msg);
                                return;
                            }
                            var data = result.data;
                            if (data['page'] === 1) {
                                $('#comprehensive-data').html('');
                            }

                            replaceUrl('category_id', product_data.category_id);

                            data['list'].forEach(function (val) {
                                var html = $('#comprehensive_product').find('li').clone();

                                html.attr('data-productid', val['product_id']);
                                html.find('.thumb img').attr('src', val['image']['full_url']);
                                if (val['purchased']) {
                                    html.find('.thumb i').show();
                                }
                                html.find('.product_name').text(val['name']);
                                html.find('.current_price').text('￥' + val['current_price']);

                                $('#comprehensive-data').append(html);
                            });

                            if (data['page'] === data['total_page']) {
                                comprehensive.endUpLoading(true);
                                $('.minirefresh-upwrap').hide();
                            } else {
                                product_data.page = data['page'] + 1;
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
        $('.category_id').click(function () {
            var old_category_id = product_data.category_id;
            product_data.category_id = $(this).data('id');

            if (old_category_id === product_data.category_id) {
                return;
            }

            product_data.page = 1;
            $('.category_id').find('li').removeClass('acti');
            $(this).find('li').addClass('acti');

            $('#comprehensive-data').html('');
            comprehensive.triggerUpLoading();

            return false;
        });

        $('body').on('click', '.detail', function () {
            window.location.href = encode_url($(this).data('href'), {product_id: $(this).data('productid')});
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
