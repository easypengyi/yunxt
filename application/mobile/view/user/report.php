{extend name="public/base" /}

{block name="before_scripts"}
<link rel="stylesheet" type="text/css" href="__STATIC__/minirefresh/dist-2.0.2/minirefresh.min.css"/>
{/block}

{block name="main-content"}
<div class="mobile-wrap center">
    <div class="header2" style="position:fixed;z-index:999;top:0;left:0;" >
        <a href="{$return_url}" class="back">
            <img src="__MODULE_IMG__/ic32.png" alt="">
        </a>
        <div class="sech">
            <form action="{:folder_url('Search/report_index')}">
                <input type="text" name="keyword" placeholder="输入商品名称"/>
                <input type="submit" value="搜索"/>
            </form>
        </div>
    </div>
    <div style="position:absolute;width: 100%;top: 1rem; bottom: 0;">
        <div class="sidebar">
            <?php if (isset($category_list) && !empty($category_list)): ?>
                <ul>
                    <?php foreach ($category_list as $v): ?>
                        <li class="category-button" data-cate="{$v.category_id}">
                            <strong>{$v.name}</strong>
                            <span>{$v.order_number}/{$v.product_number}</span>
                            <?php $percent = empty($v['product_number']) ? 0 : round($v['order_number'] * 100 / $v['product_number'], 2); ?>
                            <div class="lines"><i style="width: <?php echo $percent ?>%;"></i></div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
        <div class="r-show-box">
            <div id="comprehensive" class="minirefresh-wrap">
                <ul id="comprehensive-data" class="minirefresh-scroll">
                </ul>
            </div>
        </div>
    </div>
</div>
{/block}

{block name="hide-content"}
<div id="data_div">
    <li data-href="{:folder_url('Product/detail')}" data-id="0">
        <div class="pic"><img src="__MODULE_IMG__/ceshi1.jpg" alt=""></div>
        <div class="text">
            <strong class="product_name"></strong>
            <span class="product_price">￥1299</span>
            <a href="javascript:" class="checking none">报告生成中</a>
            <a href="" target="_blank" class="report none">查看报告</a>
        </div>
    </li>
</div>
{/block}

{block name="scripts"}
<script src="__STATIC__/minirefresh/dist-2.0.2/minirefresh.min.js"></script>
<!--suppress JSUnresolvedFunction -->
<script>
    var data = {
        page: 1,
        sort_type: 6,
        category: parseInt('{$category_id}')
    };

    $('body').on('click', '#comprehensive-data li', function () {
        var id = $(this).data('id');
        console.log(id);
        window.location.href = encode_url($(this).data('href'), {product_id: id})
    });

    $(function () {
        var comprehensive = new MiniRefresh({
            container: '#comprehensive',
            down: {
                isLock: true
            },
            up: {
                isAuto: false,
                callback: function () {
                    $.ajax({
                        type: 'POST',
                        url: "{:folder_url('Ajax/product_list')}",
                        data: {page: data.page, category_id: data.category, sort_type:data.sort_type},
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
                                var html = $('#data_div').find('li').clone();
                                html.attr('data-id', val['product_id']);
                                html.find('.pic img').attr('src', val['image']['full_url']);
                                html.find('.product_name').text(val['name']);
                                html.find('.product_price').text('￥' + val['current_price']);
                                if (val['purchased']) {
                                    if (val['report'].length !== 0) {
                                        html.find('.report').attr('href', val['report']['file']['full_url']);
                                        html.find('.report').show();
                                    }
                                    else {
                                        html.find('.checking').show();
                                    }
                                } else {
                                    html.addClass('no-purchased');
                                }

                                $('#comprehensive-data').append(html);
                            });
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

        var category_button = $('li.category-button');
        // 类型切换按钮
        category_button.click(function () {
            var old_category = data.category;
            data.category = parseInt($(this).data('cate'));

            if (old_category === data.category) {
                return;
            }
            data.page = 1;

            $('li.category-button').removeClass('acti');
            $(this).addClass('acti');

            $('#comprehensive-data').html('');
            comprehensive.triggerUpLoading();

            return false;
        });

        var category_button_cate = $('li.category-button[data-cate=' + data.category + ']');

        if (category_button_cate.length === 0) {
            category_button.get(0).click();
        } else {
            category_button_cate.click();
        }
    });
</script>
{/block}
