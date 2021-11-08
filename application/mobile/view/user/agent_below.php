{extend name="public/base" /}

{block name="before_scripts"}
<link rel="stylesheet" type="text/css" href="__STATIC__/minirefresh/dist-2.0.2/minirefresh.min.css"/>
{/block}
{block name="styles"}
<style>
    .user-form .top-box span {
        width: 25%;
    }
    .user-form ul li .col{
        width: 25%;
    }
</style>
{/block}
{block name="main-content"}
<div class="center">
    <div class="header" style="position:fixed;z-index:999;top:0;left:0;">
        <a href="{$return_url}"><img src="__MODULE_IMG__/ic21.png" alt=""></a>
    </div>

    <div class="user-name" style="position: relative;top:0.71rem;left:0;">
        <div class="pic"><img  style=" width:100%;min-height:100%;" src="{$agent.member_headpic.full_url}" alt="" class="member_headpic"/></div>
        <strong>{$agent.member_realname}的团队人数{$team_num}位</strong>
    </div>

    <div class="user-form dt" style="left:0;margin-top:0.75rem;">
        <div class="top-box">
            <span>职位</span>
            <span>姓名</span>
            <span>手机号</span>
            <span>团队人数</span>
        </div>
        <div class="agent-below-list">
            <ul id="agent" class="minirefresh-wrap">
                <div id="agent-data" class="minirefresh-scroll">
                </div>
            </ul>
        </div>
    </div>
</div>
{/block}

{block name="hide-content"}
<div id="agent_data">
    <li class="agent_below" style="height: 1.6rem;"  >
        <div class="col"><p class="group_name" style=" line-height: 1.1rem;">1323</p></div>
        <div class="col pic_div" data-id=""   data-href="{:folder_url('User/agent_below')}">
            <div class="pic"><img src="" alt="" class="member_headpic"/></div>
            <span class="member_nickname">用户昵称</span>
        </div>
        <div class="col" ><p class="member_tel" style=" line-height: 1.1rem;color:#74bfff">2018-09-09</p></div>
        <div class="col" ><p class="register_time" style=" line-height: 1.1rem;">2018-09-09</p></div>
    </li>
</div>
{/block}

{block name="scripts"}
<script src="__STATIC__/minirefresh/dist-2.0.2/minirefresh.min.js"></script>
<!--suppress JSCheckFunctionSignatures, JSValidateTypes, JSUnresolvedFunction -->
<script>
    var agent_data = {
        page: 1,
        invitation_id: '{$agent.member_id}'
    };

    $(function () {
        var agent = new MiniRefresh({
            container: '#agent',
            down: {
                isLock: true
            },
            up: {
                callback: function () {
                    invitation_list(agent_data, function (data) {
                        if (data['page'] === 1) {
                            $('#agent-data').html('');
                        }

                        data['list'].forEach(function (val) {
                            var html = $('#agent_data').find('li').clone();
                            html.find('.pic_div').attr('data-id', val['member']['member_id']);
                            html.find('.member_headpic').attr('src', val['member']['member_headpic']['full_url']);
                            html.find('.member_nickname').text(val['member']['member_realname']);
                            html.find('.group_name').text(val['group_name']);
                            html.find('.member_tel').text(val['member']['member_tel']);
                            html.find('.register_time').text(val['team_num']);
                            $('#agent-data').append(html);
                        });
                        $('.count').text(data['count']);
                        if (data['page'] === data['total_page']) {
                            agent.endUpLoading(true);
                            $('.minirefresh-upwrap').hide();
                        } else {
                            agent_data.page = data['page'] + 1;
                            agent.endUpLoading();
                            $('.minirefresh-upwrap').hide();
                        }
                    });
                    $('.minirefresh-upwrap').hide();
                }
            }
        });
    });

    $('body').on('click', '.pic_div', function () {
        window.location.href = encode_url($(this).data('href'), {invitation_id: $(this).data('id')});
    });

    $('body').on('click', '.member_tel', function () {
        var message = $(this).text();
        var input = document.createElement("input");
        input.value = message;
        document.body.appendChild(input);
        input.select();
        input.setSelectionRange(0, input.value.length), document.execCommand('Copy');
        document.body.removeChild(input);
        show_message('手机号复制成功');
    });


    // 邀请列表
    function invitation_list(param, callback) {
        $.ajax({
            type: 'POST',
            url: "{:folder_url('Ajax/team_list')}",
            data: param,
            success: function (result) {
                if (result.code !== 1) {
                    show_message(result.msg);
                    return;
                }

                var data = result.data;
                console.log(result)

                callback(data);
            }
        });
    }
</script>
{/block}
