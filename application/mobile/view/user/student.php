{extend name="public/base" /}

{block name="before_scripts"}
<link rel="stylesheet" type="text/css" href="__STATIC__/minirefresh/dist-2.0.2/minirefresh.min.css"/>
{/block}
{block name="styles"}
<style>
    .user-form .top-box span {
        width: 50%;
    }
    .user-form ul li .col {
        width: 50%;
    }
</style>
{/block}
{block name="main-content"}
<div class="center">
    <div class="header" style="background: #fafafa; border-bottom: none;position:fixed;z-index:999;top:0;left:0;">
        <a href="{$return_url}"><img src="__MODULE_IMG__/ic21.png" alt=""></a>
    </div>
    <div class="tabs dt" style="position:fixed;z-index:999;top:1.01rem;left:0;text-align: center;color: #999;">
       学员人数{$team_num}位
    </div>
    <div class="user-form" style="left:0;margin-top:1.8rem;">
        <div class="box">
            <div class="top-box">
                <span>姓名</span>
                <span>手机号</span>
            </div>
            <div class="agent-list" style="top: 2.65rem;">
                <ul id="parent" class="minirefresh-wrap">
                    <div id="parent-data" class="minirefresh-scroll">
                    </div>
                </ul>
            </div>
        </div>



    </div>
</div>
{/block}

{block name="hide-content"}

<div id="parent_data">
    <li data-id="" class="agent_below" style="height: 1.6rem;" data-href="{:folder_url('User/agent_below')}">
<!--        <div class="col"><p class="group_name" style=" line-height: 1.1rem;"></p></div>-->
        <div class="col" >
            <div class="pic"><img src="" alt="" class="member_headpic"/></div>
            <span class="member_nickname">用户昵称</span>
        </div>
        <div class="col"  ><p class="member_tel" style=" line-height: 1.1rem;">2018-09-09</p></div>
<!--        <div class="col" ><p class="register_time" style=" line-height: 1.1rem;">2018-09-09</p></div>-->
    </li>
</div>


{/block}

{block name="scripts"}
<script src="__STATIC__/minirefresh/dist-2.0.2/minirefresh.min.js"></script>
<script>

    var parent_data = {
        page: 1,
    };


    $(function () {
        tabs_cg('.tabs span', '.user-form .box', 'click', 'acti', '', 0);

        var parent = new MiniRefresh({
            container: '#parent',
            down: {
                isLock: true
            },
            up: {
                callback: function () {
                    invitation_list(parent_data, function (data) {
                        if (data['page'] === 1) {
                            $('#parent-data').html('');
                        }

                        data['data']['list'].forEach(function (val) {
                            var html = $('#parent_data').find('li').clone();
                            html.attr('data-id', val['member_id']);
                            html.find('.member_headpic').attr('src', val['member_headpic']['full_url']);
                            html.find('.member_nickname').text(val['member_nickname']);
                            html.find('.member_tel').text(val['member_tel']);
                            $('#parent-data').append(html);
                        })

                        $('.count').text(data['data']['count']);

                        if (data['page'] === data['total_page']) {
                            parent.endUpLoading(true);
                            $('.minirefresh-upwrap').hide();
                        } else {
                            parent_data.page = data['page'] + 1;
                            parent.endUpLoading();
                            $('.minirefresh-upwrap').hide();
                        }
                    });
                    $('.minirefresh-upwrap').hide();
                }
            }
        });

    });

    $('body').on('click', '.agent_below', function () {
        window.location.href = encode_url($(this).data('href'), {invitation_id: $(this).data('id')});
    });


    // 邀请列表
    function invitation_list(param, callback) {
        $.ajax({
            type: 'POST',
            url: "{:folder_url('Ajax/student_list')}",
            data: param,
            success: function (result) {
                if (result.code !== 1) {
                    show_message(result.msg);
                    return;
                }

                var data = result.data;

                callback(data);
            }
        });
    }
</script>
{/block}
