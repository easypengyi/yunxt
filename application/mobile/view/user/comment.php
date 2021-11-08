{extend name="public/base" /}

{block name="before_scripts"}
<link rel="stylesheet" type="text/css" href="__STATIC__/minirefresh/dist-2.0.2/minirefresh.min.css"/>
{/block}

{block name="main-content"}
<div class="mobile-wrap center">
    <div class="header" style="position:fixed;z-index:999;top:0;left:0;">
        <a href="{$return_url}"><img src="__MODULE_IMG__/ic21.png" alt=""></a>
        <h1>我的评价</h1>
    </div>
    <div class="inform" style="position: relative;top:1.01rem;left:0;">
        <div class="inform-mess evaluation-list inform-mess-mb">
            <ul id="comprehensive" class="minirefresh-wrap">
                <div id="comprehensive-data" class="minirefresh-scroll">
                </div>
            </ul>
        </div>
    </div>
</div>
{/block}

{block name="hide-content"}
<div id="comment_data">
    <li>
        <div>
            <h4><img src="__MODULE_IMG__/ceshi1.jpg" class="product_image" alt=""/></h4>
            <h3>
                <b class="product_name"></b>
                <span>
                    <em class="comment_time"></em>
                    <b class="score">
                        <i></i>
                        <i></i>
                        <i></i>
                        <i></i>
                        <i></i>
                    </b>
                </span>
            </h3>
            <p class="content">
                内容
            </p>
        </div>
    </li>
</div>
{/block}

{block name="scripts"}
<script src="__STATIC__/minirefresh/dist-2.0.2/minirefresh.min.js"></script>
<!--suppress JSCheckFunctionSignatures, JSValidateTypes, JSUnresolvedFunction -->
<script>
    var comment_data = {
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
                        url: "{:folder_url('Ajax/comment_list')}",
                        data: {page: comment_data.page},
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
                                var html = $('#comment_data').find('li').clone();

                                html.find('.comment_time').text(format_time('yyyy-MM-dd hh:mm', val['create_time'] * 1000));
                                html.find('.product_image').attr('src', val['product']['image']['full_url']);
                                html.find('.product_name').text(val['product']['name']);
                                html.find('.content').text(val['content']);

                                var star = html.find('b.score').children('i');

                                for (var i = 0; i < parseInt(val['score']); i++) {
                                    $(star.get(i)).addClass('acti');
                                }

                                $('#comprehensive-data').append(html);
                            });
                            if (data['page'] === data['total_page']) {
                                comprehensive.endUpLoading(true);
                                $('.minirefresh-upwrap').hide();
                            } else {
                                comment_data.page = data['page'] + 1;
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
