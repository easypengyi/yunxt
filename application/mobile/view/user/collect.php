{extend name="public/base" /}
{block name="before_scripts"}
<link rel="stylesheet" type="text/css" href="__STATIC__/minirefresh/dist-2.0.2/minirefresh.min.css"/>
{/block}

{block name="main-content"}
<div class="mobile-wrap center">
    <div class="header" style="position:fixed;z-index:999;top:0;left:0;">
        <a href="{$return_url}"><img src="__MODULE_IMG__/ic21.png" alt=""></a>
        <h1>我的收藏</h1>
    </div>
    <div class="collect" >
        <ul id="comprehensive" class="minirefresh-wrap">
            <div id="comprehensive-data" class="minirefresh-scroll"></div>
        </ul>
    </div>
    <div class="null"></div>
</div>
{/block}

{block name="hide-content"}
<div id="comprehensive_list">
    <li data-id="0" data-href="{:folder_url('Product/detail')}">
        <h4><span><img class="image" src="__MODULE_IMG__/ceshi1.png" alt=""/></span></h4>
        <div>
            <h3 class="title">大咖 - 咖啡因代谢基因检测</h3>
            <h6>
                <span class="price">￥1299</span>
                <a class="collect-del" href="javascript:">删除</a>
            </h6>
        </div>
    </li>
</div>
{/block}

{block name="scripts"}
<script src="__STATIC__/minirefresh/dist-2.0.2/minirefresh.min.js"></script>
<!--suppress JSCheckFunctionSignatures, JSValidateTypes, JSUnresolvedFunction -->
<script>
    var list_data = {
        page: 1
    };

    $(function () {
        $('body').on('click', '.collect-del', function () {
            var view = $(this);
            var product_id = view.parents('li').data('id');
            show_confirm_dialog('是否删除商品收藏？', function () {
                $.ajax({
                    type: 'POST',
                    url: "{:folder_url('Ajax/product_collection_change')}",
                    data: {product_id: product_id, collection: 1},
                    success: function (data) {
                        if (data.code !== 1) {
                            show_message(data.msg);
                            return;
                        }
                        show_message(data.msg);
                        view.parents('li').remove();
                    }
                });
            });
            return false;
        }).on('click', '.collect li', function () {
            // 商品跳转
            var product_id = $(this).data('id');
            var href = $(this).data('href');
            window.location.href = encode_url(href, {product_id: product_id});
            return false;
        });

        var comprehensive = new MiniRefresh({
            container: '#comprehensive',
            down: {
                isLock: true
            },
            up: {
                callback: function () {
                    $.ajax({
                        type: 'POST',
                        url: "{:folder_url('Ajax/product_collection_list')}",
                        data: {page: list_data.page},
                        success: function (result) {
                            if (result.code !== 1) {
                                show_message(result.msg);
                                return;
                            }

                            var data = result.data;
                            if (data['page'] === 1) {
                                $('#comprehensive-data').html('');
                            }

                            data['list'].forEach(function (val) {
                                var html = $('#comprehensive_list').find('li').clone();
                                html.attr('data-id', val['product_id']);
                                html.find('.title').html(val['name']);
                                html.find('.image').attr('src', val['image']['full_url']);
                                html.find('.price').html('￥' + val['current_price']);
                                $('#comprehensive-data').append(html);
                            });
                            if (data['page'] === data['total_page']) {
                                comprehensive.endUpLoading(true);
                                $('.minirefresh-upwrap').hide();
                            } else {
                                list_data.page = data['page'] + 1;
                                comprehensive.endUpLoading();
                                $('.minirefresh-upwrap').hide();
                            }
                        }
                    });
                    $('.minirefresh-upwrap').hide();
                }
            }
        });
    })

</script>
{/block}
