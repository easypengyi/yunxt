{extend name="public/base" /}

{block name="before_scripts"}
<link rel="stylesheet" type="text/css" href="__STATIC__/minirefresh/dist-2.0.2/minirefresh.min.css"/>
<style>
    .details-box-wrap{
        background-size: 2.2rem 2rem;
        background-repeat: no-repeat;
        background-position: 2.75rem 3rem;
    }
</style>
{/block}

{block name="main-content"}
<div class="center">
    <div class="header" style="position:fixed;z-index:999;top:0;left:0;">
        <a href="{$return_url}"><img src="__MODULE_IMG__/ic21.png" alt=""></a>
    </div>
    <?php if (isset($message_list) && !empty($message_list)) : ?>
        <div style="position: absolute; overflow: scroll; border: 1px solid #ccc; bottom: 1rem; width: 100%; height: 1rem;">
            <?php foreach ($message_list as $v): ?>
                <?php if ($v['readed'] == 0):?>
                    <h3 style="    line-height: 1rem;font-size: 15px; text-align: center;">
                        {$v.message}
                    </h3>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
    <div class="details-box" style="left:0;">
        <div class="details-box-wrap">
            <ul id="comprehensive" class="minirefresh-wrap" style="display: initial">
                <div id="comprehensive-data" class="minirefresh-scroll">
                </div>
            </ul>
        </div>
    </div>
    <?php if ($type == 11) : ?>
    <div class="null"></div>
    <div style=" border-right: 1px solid white;" class="fixed-btn center"><a href="{:folder_url('User/amount_withdraw')}">奖金提现</a></div>
    <?php endif; ?>
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
        type:'{$type|default= 2}'
    };

    if (record_data.type == 11){
        record_data.type = 2;
    }

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
                        url: "{:folder_url('Ajax/commission_record_list')}",
                        data: {page: record_data.page,type:record_data.type},
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
                                var html = $('#record_data').find('li').clone();
                                html.find('.description').text(val['description']);
                                html.find('.amount-time').text(format_time('yyyy-MM-dd hh:mm', val['create_time'] * 1000));
                                if(val['type'] === 2){
                                    html.find('.amount').text((parseFloat(val['value']) > 0 ? '-￥' : '') + val['value']);
                                }else{
                                    html.find('.amount').text((parseFloat(val['value']) > 0 ? '+￥' : '') + val['value']);
                                }
                                $('#comprehensive-data').append(html);
                            });

                            if (data['list'].length == 0){
                                $('.details-box-wrap').css('background-image','url("__MODULE_IMG__/zwnrs.png")');
                            }

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
</script>
{/block}
