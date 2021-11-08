{extend name="public/base" /}
{block name="before_scripts"}
<link rel="stylesheet" type="text/css" href="__STATIC__/minirefresh/dist-2.0.2/minirefresh.min.css"/>
{/block}

{block name="main-content"}
<div class="mobile-wrap center" >
    <div class="header" style="position:fixed;z-index:999;top:0;left:0;">
        <a href="{$return_url}"><img src="__MODULE_IMG__/ic21.png" alt=""></a>
        <h1>我的账户</h1>
    </div>
    <div class="commission dt" style="position: relative;top:1.01rem;left:0;height:2.5rem;">
        <ul>
            <li>
                <img src="__MODULE_IMG__/acc_icon1.png" alt="">
                <strong>余额</strong>
                <span>{$member.balance}</span>
            </li>
            <li>
                <img src="__MODULE_IMG__/acc_icon2.png" alt="">
                <strong>总收入</strong>
                <span class="s1">{$info.total_income}</span>
            </li>
            <li>
                <img src="__MODULE_IMG__/acc_icon3.png" alt="">
                <strong>总支出</strong>
                <span class="s2">{$info.total_cost}</span>
            </li>
        </ul>
    </div>
    <div class="details-box" style="left:0;margin-top:1.21rem;">
        <h3>收支明细</h3>
        <div class="details-box-wrap">
            <ul id="comprehensive" class="minirefresh-wrap" style="display: initial">
                <div id="comprehensive-data" class="minirefresh-scroll"></div>
            </ul>
        </div>
    </div>
    <div class="null"></div>
    <div class="fixed-btn center"><a href="{:folder_url('User/account_recharge')}">账户充值</a></div>
</div>
{/block}

{block name="hide-content"}
<div id="comprehensive_list">
    <li>
        <div class="left">
            <strong class="description">账户充值</strong>
            <span class="create_time">2018-05-23</span>
        </div>
        <div class="right">
            <strong class="money">+100.5</strong>
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
        var comprehensive = new MiniRefresh({
            container: '#comprehensive',
            down: {
                isLock: true
            },
            up: {
                callback: function () {
                    $.ajax({
                        type: 'POST',
                        url: "{:folder_url('Ajax/balance_record_list')}",
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

                            replaceUrl();

                            data['list'].forEach(function (val) {
                                var html = $('#comprehensive_list').find('li').clone();
                                html.find('.description').html(val['description']);

                                var star_date = new Date(parseInt(val['create_time']) * 1000);
                                var star_year = star_date.getFullYear();
                                var star_month = star_date.getMonth() + 1;
                                star_month = star_month < 10 ? ('0' + star_month) : star_month;
                                var star_dates = star_date.getDate();
                                star_dates = star_dates < 10 ? ('0' + star_dates) : star_dates;
                                var star_time = star_year + '-' + star_month + '-' + star_dates;
                                html.find('.create_time').html(star_time);
                                html.find('.money').html(val['value']);
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

        <?php if (isset($order_payment) && !empty($order_payment)): ?>
        setTimeout(function () {
            window.location.reload();
        }, 1000);
        <?php endif; ?>
    });

    /*
    * 替换当前url 并不导致浏览器页面刷新
    * name 参数名
    * value 参数值
    */
    function replaceUrl() {
        history.replaceState({}, '', '?');
    }
</script>
{/block}
