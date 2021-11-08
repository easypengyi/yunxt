{extend name="public/base" /}

{block name="before_scripts"}
<link rel="stylesheet" type="text/css" href="__STATIC__/minirefresh/dist-2.0.2/minirefresh.min.css"/>
<style>
    .details-box-wrap{
        background-size: 2.2rem 2rem;
        background-repeat: no-repeat;
        background-position: 2.75rem 3rem;
    }
    .details-box-wrap {
        bottom: 1rem;
    }
    .fixed-btn {
        width: 50%;
    }

</style>
{/block}

{block name="main-content"}
<div class="center">
    <div class="header" style="position:fixed;z-index:999;top:0;left:0;">
        <a href="{$return_url}"><img src="__MODULE_IMG__/ic21.png" alt=""></a>
    </div>
    <div class="details-box" style="left:0;">
        <div class="details-box-wrap" style="top: 0.71rem;">
            <ul id="comprehensive" class="minirefresh-wrap" style="display: initial">
                <div id="comprehensive-data" class="minirefresh-scroll">
                </div>
            </ul>
        </div>
    </div>
    <div class="null"></div>
    <div class="fixed-btn center"><a href="{:folder_url('User/amount_give')}">转赠</a></div>
    <div class="fixed-btn pay" style="right: 0;left: unset"><a>我要发货</a></div>
</div>
{/block}

{block name="hide-content"}
<div id="record_data">
    <li>
        <div class="left">
            <strong class="description"></strong>
            <span class="amount-time">2018-05-23</span>
        </div>
        <div class="right">
            <strong class="amount">+100.5</strong>
            <span></span>
        </div>
    </li>
</div>
{/block}

{block name="scripts"}
<script src="__STATIC__/minirefresh/dist-2.0.2/minirefresh.min.js"></script>
<!--suppress JSCheckFunctionSignatures, JSValidateTypes, JSUnresolvedFunction -->
<script>
    var record_data = {
        page: 1,
        product_id: 6,
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
                        data: {page: record_data.page},
                        success: function (result) {
                            if (result.code !== 1) {
                                show_message(result.msg);
                                return;
                            }

                            var data = result.data;
                            if (data['page'] === 1) {
                                $('#comprehensive-data').html('');
                            }

                            if (data['list'].length == 0){
                                $('.details-box-wrap').css('background-image','url("__MODULE_IMG__/zwnrs.png")');
                            }

                            data['list'].forEach(function (val) {
                                var html = $('#record_data').find('li').clone();
                                html.find('.description').text(val['description']);
                                html.find('.amount-time').text(format_time('yyyy-MM-dd hh:mm', val['create_time'] * 1000));
                                if(val['type'] === 9 || val['type'] === 11|| val['type'] === 12){
                                    html.find('.amount').text((parseFloat(val['value']) > 0 ? '+' : '') + val['value']);
                                }else{
                                    html.find('.amount').text((parseFloat(val['value']) > 0 ? '-' : '') + val['value']);
                                }
                                $('#comprehensive-data').append(html);
                            });
                            if (data['page'] === data['total_page']) {
                                comprehensive.endUpLoading(true);
                                $('.minirefresh-upwrap').hide();
                            } else {
                                record_data.page = data['page'] + 1;
                                comprehensive.endUpLoading();
                                $('.minirefresh-upwrap').hide();
                            }
                        }
                    });
                    $('.minirefresh-upwrap').hide();
                }
            }
        });
    });

    $(".pay").click(function () {

            $.ajax({
                type: 'POST',
                url: "{:folder_url('Ajax/single2_settlement')}",
                data: {product_id: record_data.product_id},
                success: function (result) {
                    if (result.code !== 1) {
                        show_message(result.msg);
                        if (result.url !== '' && typeof (result.url) !== 'undefined') {
                            window.location.href = result.url;
                            return;
                        }

                        return;
                    }
                    window.location.href =  encode_url('order2_submit');
                }
            });


    });

</script>
{/block}
