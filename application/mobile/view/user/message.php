{extend name="public/base" /}

{block name="before_scripts"}
<link rel="stylesheet" type="text/css" href="__STATIC__/minirefresh/dist-2.0.2/minirefresh.min.css"/>
{/block}

{block name="main-content"}
<div class="mobile-wrap center">
    <div class="header" style="position:fixed;z-index:999;top:0;left:0;">
        <a href="{$return_url}"><img src="__MODULE_IMG__/ic21.png" alt=""></a>
        <h1></h1>
    </div>
    <div class="notice" >
        <ul id="comprehensive" class="minirefresh-wrap">
            <div id="comprehensive-data" class="minirefresh-scroll"></div>
        </ul>
    </div>
</div>
{/block}

{block name="hide-content"}
<div id="comprehensive_notice">
    <li>
        <h5>
            <span>
                <img src="__MODULE_IMG__/ic1.png" alt=""/>
            </span>
            <b class="title">通知标题</b>
            <i class="create_time">2018-05-02 12:00</i>
        </h5>
        <p class="content">
            评论内容
        </p>
    </li>
</div>
{/block}

{block name="scripts"}
<script src="__STATIC__/minirefresh/dist-2.0.2/minirefresh.min.js"></script>
<!--suppress JSCheckFunctionSignatures, JSValidateTypes, JSUnresolvedFunction -->
<script>
    var notice_data = {
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
                        url: "{:folder_url('Ajax/message_list')}",
                        data: {page: notice_data.page},
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
                                var html = $('#comprehensive_notice').find('li').clone();
                                html.find('.title').html(val['send_member']['member_nickname']);

                                var star_date = new Date(parseInt(val['show_time']) * 1000);
                                var star_year = star_date.getFullYear();
                                var star_month = star_date.getMonth() + 1;
                                star_month = star_month < 10 ? ('0' + star_month) : star_month;
                                var star_dates = star_date.getDate();
                                star_dates = star_dates < 10 ? ('0' + star_dates) : star_dates;
                                var star_time = star_year + '-' + star_month + '-' + star_dates;
                                html.find('.create_time').html(star_time);
                                html.find('.content').html(val['message']);
                                $('#comprehensive-data').append(html);
                            });
                            if (data['page'] === data['total_page']) {
                                comprehensive.endUpLoading(true);
                                $('.minirefresh-upwrap').hide();
                            } else {
                                notice_data.page = data['page'] + 1;
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
