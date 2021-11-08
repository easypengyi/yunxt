{extend name="public/base" /}
{block name="before_scripts"}
<link rel="stylesheet" type="text/css" href="__STATIC__/minirefresh/dist-2.0.2/minirefresh.min.css"/>
{/block}

{block name="main-content"}
<div class="mobile-wrap center">
    <div class="search" style="position: fixed;z-index: 99">
        <a href="{$return_url}"><span class="retu"></span></a>
        <div class="sea-col">
            <input type="submit" value="" placeholder=""/>
            <input type="text" name="keyword" id="keyword" value="{$keyword}" placeholder="请输入您要搜索的商品"/>
        </div>
        <span class="btm">搜索</span>
    </div>
    <div class="sea-list">
        <ul id="product_list" class="minirefresh-wrap">
            <div id="product-data" class="minirefresh-scroll"></div>
        </ul>
    </div>

    <div id="product_list_blank" class="none" style="font-size:14px;color:#bbb;text-align: center;margin-top: 0.5rem;z-index:999;position: relative;">
        <h4>未搜索到任何结果</h4>
    </div>

</div>
{/block}

{block name="hide-content"}
<div id="product_list_data">
    <li class="detail" data-href="{:folder_url('Product/detail')}" data-id="0">
        <h4 class="product-thumb">
            <span><img src="__MODULE_IMG__/ceshi1.png" alt=""/></span>
        </h4>
        <div>
            <h3 class="product-name">大咖 - 咖啡因代谢基因检测</h3>
            <h6>
                <span class="product-price">￥1299</span>
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
        keyword: '{$keyword}',
        page: 1
    };

    $(function () {
        var product_list = new MiniRefresh({
            container: '#product_list',
            down: {
                isLock: true
            },
            up: {
                isAuto: false,
                callback: function () {
                    $.ajax({
                        type: 'POST',
                        url: "{:folder_url('Ajax/product_list')}",
                        data: {
                            sort_type: product_data.sort_type,
                            keyword: product_data.keyword,
                            page: product_data.page
                        },
                        success: function (result) {
                            if (result.code !== 1) {
                                show_message(result.msg);
                                return;
                            }

                            var data = result.data;
                            if (data['page'] === 1) {
                                $('#product-data').html('');
                                $('#product_list_blank').hide();
                            }
                            data['list'].forEach(function (val) {
                                var html = $('#product_list_data').find('li').clone();
                                html.attr('data-id', val['product_id']);
                                html.find('.product-name').html(val['name']);
                                html.find('.product-price').html('￥' + val['current_price']);
                                html.find('.product-thumb img').attr('src', val['image']['full_url']);

                                $('#product-data').append(html);
                            });
                            if (data['page'] === data['total_page']) {
                                product_list.endUpLoading(true);
                                $('.minirefresh-upwrap').hide();
                            } else {
                                product_data.page = data['page'] + 1;
                                product_list.endUpLoading();
                                $('.minirefresh-upwrap').hide();
                            }
                            if (data['count'] === 0) {
                                $('#product-data').html('');
                                $('#product_list_blank').show();
                            }
                        }
                    });
                    $('.minirefresh-upwrap').hide();
                }
            }
        });

        var search_btn = $('.btm');

        //点击搜索
        search_btn.click(function () {
            product_data.keyword = $('#keyword').val();

            $('#product-data').html('');

            product_list.triggerUpLoading();
            return false;triggerUpLoading
        });

        // 综合商品跳转
        $('body').on('click', '.detail', function () {
            var href = $(this).data('href');
            window.location.href = encode_url(href, {product_id: $(this).data('id')});
            return false;
        });

        if (product_data.keyword !== '') {
            search_btn.click();
        }
    })
</script>
{/block}
