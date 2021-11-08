{extend name="public/base" /}

{block name="main-content"}
<div class="mobile-wrap center">
    <div class="header" style="z-index:999;top:0;left:0;position:fixed;">
        <a href="{$return_url}"><img src="__MODULE_IMG__/ic21.png" alt=""></a>
        <h1>购物车</h1>
        <span class="remove">清空</span>
    </div>
    <div class="shop-car" style="top:1.01rem;">
        <?php if (isset($product_list) && !empty($product_list)): ?>
            <form class="ajax-form" action="{$full_url}" method="post">
                <ul>
                    <?php foreach ($product_list as $v): ?>
                        <?php if (!$v['enable']): ?>
                            <?php continue; ?>
                        <?php endif; ?>
                        <li>
                            <input type="hidden" name="all_product_id[]" value="{$v.product_id}"/>
                            <i class="check-one">
                                <input type="checkbox" name="product_id[]" class="check" value="{$v.product_id}" placeholder="">
                            </i>
                            <h4><span><img src="{$v.image.full_url}" alt=""/></span></h4>
                            <div>
                                <h3>{$v.product_name}</h3>
                                <h6>
                                    <span>￥</span><span class="unit-price">{$v.current_price}</span>
                                    <em class="del" style=" border:1px solid #bbb;">删除</em>
                                </h6>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </form>
        <?php endif; ?>
    </div>
    <div class="fix-settle">
        <h4>
            <span>
                <i>
                    <input type="checkbox" class="all-check" placeholder="">
                </i>
                全选
            </span>
            <strong>
                合计 : <em>￥</em><em id="total">0</em>
            </strong>
        </h4>
        <a href="#" class="settlement">去结算<em>(<em id="number">0</em>)</em></a>
    </div>
</div>
<div class="alert">
    <div class="exit removes">
        <h4>
            <img src="__MODULE_IMG__/alert-icon.png" alt=""/>
            <i>是否清空购物车？</i>
        </h4>
        <h5>
            <a class="ext">取消</a>
            <a class="sub">确认</a>
        </h5>
    </div>
</div>

<!--底部菜单栏开始-->
{include file='public/footer' activate='4'/}
<!--底部菜单栏结束-->
{/block}

{block name="scripts"}
<script>
    $(function () {

        // 删除单个
        $(".del").click(function () {
            var view = $(this);
            var product_id = view.parents('li').find('input[name="product_id[]"]').val();
            $.ajax({
                type: 'POST',
                url: "{:folder_url('Ajax/product_delete')}",
                data: {product_id: product_id},
                success: function (data) {
                    show_message(data.msg);
                    if (data.code !== 1) {
                        return;
                    }
                    view.parents('li').remove();
                }
            });
        });

        // 清空
        $('.sub').click(function () {
            var product_id = [];
            $('input[name="product_id[]"]').each(function () {
                product_id.push($(this).val());
            });
            if (!product_id.length) {
                show_message('购物车商品为空！');
            }

            $.ajax({
                type: 'POST',
                url: "{:folder_url('Ajax/product_delete')}",
                data: {product_id: product_id.toString()},
                success: function (data) {
                    show_message(data.msg);
                    if (data.code !== 1) {
                        return;
                    }
                    window.location.href = "{:controller_url('cart')}";
                }
            });
        });

        // 商品选择
        $('.check').click(function () {
            var view = $(this);
            if (view.is(':checked')) {
                view.parent().parent().addClass('acti');
            } else {
                view.parent().parent().removeClass('acti');
            }

            money_change();
        }).change(function () {
            all_choose_change();
        });

        // 全选
        $('.all-check').click(function () {
            var view = $(this);
            if (view.is(':checked')) {
                $('.check-one').each(function () {
                    $(this).find('input[type=checkbox]').each(function () {
                        $(this).prop('checked', true);
                        $(this).parent().parent().addClass('acti');
                    });
                });
            } else {
                $('.check-one').each(function () {
                    $(this).find('input[type=checkbox]').each(function () {
                        $(this).prop('checked', false);
                        $(this).parent().parent().removeClass('acti');
                    });
                });
            }

            money_change();
            all_choose_change();
        });

        // 结算
        $('.settlement').click(function () {
            $('.ajax-form').submit();
            return false;
        });
    });

    // 全选变更
    function all_choose_change() {
        var check_all = $('.check-one');
        var check_view = check_all.find('input[type=checkbox]');
        var choose_check_view = check_all.find('input[type=checkbox]:checked');
        if (check_view.length === choose_check_view.length) {
            $('.all-check').prop('checked', true);
            $('.fix-settle').find('h4').addClass('acti');
        } else {
            $('.all-check').prop('checked', false);
            $('.fix-settle').find('h4').removeClass('acti');
        }
    }

    // 金额变更
    function money_change() {
        var total_money = parseFloat(0);
        var number = 0;
        $('.check').each(function () {
            var view = $(this);
            if (!view.is(':checked')) {
                return;
            }

            var price = view.parents('li').find('.unit-price').text();
            total_money += parseFloat(price);
            number++;
        });
        $('#number').text(number);
        $('#total').text(total_money.toFixed(2));
    }

    // 提交完成处理
    function complete(result) {
        if (result.code !== 1) {
            if (result.data.code === 203) {
                show_confirm_dialog(result.data.msg, function () {
                    window.location.href = "{:folder_url('User/leaguer_open')}"
                });
                return;
            }
            show_message(result.msg);
            return;
        }
        window.location.href = result.url;
    }
</script>
{/block}
