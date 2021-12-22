{extend name="public/base" /}

{block name="before_scripts"}
<link rel="stylesheet" type="text/css" href="__STATIC__/minirefresh/dist-2.0.2/minirefresh.min.css"/>
{/block}
{block name="styles"}
<style>
    .user-form .top-box span {
        width: 25%;
    }
    .user-form ul li .col {
        width: 25%;
    }

    .order-tag ul li {
        width: 50%;
    }
</style>
{/block}
{block name="main-content"}
<div class=" mobile-wrap  center">
    <div class="header" style=" border-bottom: none;position:fixed;z-index:999;top:0;left:0;">
        <a href="{$return_url}"><img src="__MODULE_IMG__/ic21.png" alt=""></a>
    </div>
    <div class="tabs dt" style="position:fixed;z-index:999;top:0.71rem;left:0;">
        <span class="category acti" data-cate="1"><i>团队</i></span>
        <span class="category" data-cate="2"><i>游客</i></span>
    </div>

    <div class="user-form" style="left:0;margin-top:1.4rem;">
        <div class="box">
            <div class="top-box">
                <span>职位</span>
                <span>姓名</span>
                <span>手机号</span>
                <span>团队人数</span>
            </div>
            <div class="agent-list"  style="top:2.24rem;">
                <ul id="agent_parent" class="minirefresh-wrap"  style="display:inline-block;padding-left: 0">
                    <div id="agent-parent-data" class="minirefresh-scroll">
                    </div>
                </ul>
            </div>
        </div>
        <div class="box">
            <div class="top-box">
                <span>职位</span>
                <span>姓名</span>
                <span>手机号</span>
                <span>团队人数</span>
            </div>
            <div class="agent-list" style="top:2.24rem;">
                <ul id="agent_child" class="minirefresh-wrap"  style="display:inline-block;">
                    <div id="agent-child-data" class="minirefresh-scroll">
                    </div>
                </ul>
            </div>
        </div>
    </div>
</div>
{/block}

{block name="hide-content"}

<div id="agent_parent_data">
<!--    <li data-id="" class="agent_below" style="height: 1.6rem;" data-href="{:folder_url('User/agent_below')}">-->
<!--        <div class="col"><p class="group_name" style=" line-height: 1.1rem;"></p></div>-->
<!--        <div class="col" >-->
<!--            <div class="pic"><img src="" alt="" class="member_headpic"/></div>-->
<!--            <span class="member_nickname"></span>-->
<!--        </div>-->
<!--        <div class="col"  ><p class="member_tel" style=" line-height: 1.1rem;"></p></div>-->
<!--        <div class="col" ><p class="register_time" style=" line-height: 1.1rem;"></p></div>-->
<!--    </li>-->
    <li class="agent_below" style="height: 1.6rem;padding-top: 0.3rem;margin-bottom: 0" data-id="" data-href="{:folder_url('User/agent_below')}">
        <div style="width:50%;margin-left: 0.2rem;">
            <div style="width: 1rem;height: 1rem;margin-left: 0.2rem;border-radius: 50px;border: 1px solid #096640;background: #efefef;overflow: hidden;float: left;">
                <img src="" alt="" class="member_headpic" style="width: 100%;min-height: 100%;">
            </div>
            <div style="height: 1rem;width: 1.3rem;float: left;margin-left: 0.3rem;">
                <div style="width: 100%;margin-bottom: 0.15rem"  class="member_nickname">小明</div>
                <div style="width: 100%;margin-bottom: 0.15rem"  class="member_tel">123456487</div>
                <div style="width: 100%;color: #095e3b;" class="group_name">企业合伙人</div>
            </div>
        </div>
        <div style="width: 25%;height: 1rem;/* line-height: 0.6rem; */float: left;">
            <div style="margin-bottom: 0.5rem;text-align: right;color:#A9A9A9;">团队人数</div>
            <div style="text-align: right" class="team_num">232</div>
        </div>
        <div style="width: 25%;display: inline-block;float: right;margin-right: 0.5rem;">
            <p style="margin-bottom: 0.5rem;text-align: right;color:#A9A9A9;">团队业绩</p>
            <div style="text-align: right" class="team_amount">333000</div>
        </div>
    </li>
</div>


<div id="agent_child_data">
    <li  data-id="" class="agent_below" style="height: 1.6rem;" data-href="{:folder_url('User/agent_below')}" >
        <div class="col"><p class="group_name" style=" line-height: 1.1rem;"></p></div>
        <div class="col" >
            <div class="pic"><img src="" alt="" class="member_headpic"/></div>
            <span class="member_nickname"></span>
        </div>
        <div class="col"  ><p class="member_tel" style=" line-height: 1.1rem;"></p></div>
        <div class="col" ><p class="register_time" style=" line-height: 1.1rem;"></p></div>
    </li>
</div>

{/block}

{block name="scripts"}
<script src="__STATIC__/minirefresh/dist-2.0.2/minirefresh.min.js"></script>
<script>

    var parent_data = {
        page: 1,
        category: 1
    };

    var agent_child_data = {
        page: 1,
        category: 2
    };


    $(function () {
        tabs_cg('.tabs span', '.user-form .box', 'click', 'acti', '', 0);

        var agent_parent = new MiniRefresh({
            container: '#agent_parent',
            down: {
                isLock: true
            },
            up: {
                callback: function () {
                    invitation_list(parent_data, function (data) {
                        if (data['page'] === 1) {
                            $('#agent-parent-data').html('');
                        }

                        data['list'].forEach(function (val) {
                            var html = $('#agent_parent_data').find('li').clone();
                            html.attr('data-id', val['member_id']);
                            html.find('.member_headpic').attr('src', val['member']['member_headpic']['full_url']);
                            html.find('.member_nickname').text(val['member']['member_realname']);
                            html.find('.member_tel').text(val['member']['member_tel']);
                            html.find('.group_name').text(val['group_name']);
                            html.find('.team_num').text(val['team_num']);
                            html.find('.team_amount').text(val['team_amount']);
                            $('#agent-parent-data').append(html);
                        })

                        if (data['page'] === data['total_page']) {
                            agent_parent.endUpLoading(true);
                            $('.minirefresh-upwrap').hide();
                        } else {
                            parent_data.page = data['page'] + 1;
                            agent_parent.endUpLoading();
                            $('.minirefresh-upwrap').hide();
                        }
                    });
                    $('.minirefresh-upwrap').hide();
                }
            }
        });

        var agent_child = new MiniRefresh({
            container: '#agent_child',
            down: {
                isLock: true
            },
            up: {
                callback: function () {
                    invitation_list(agent_child_data, function (data) {
                        if (data['page'] === 1) {
                            $('#agent-child-data').html('');
                        }

                        data['list'].forEach(function (val) {
                            var html = $('#agent_child_data').find('li').clone();
                            html.attr('data-id', val['member_id']);
                            html.find('.member_headpic').attr('src', val['member']['member_headpic']['full_url']);
                            html.find('.member_nickname').text(val['member']['member_realname']);
                            html.find('.member_tel').text(val['member']['member_tel']);
                            html.find('.group_name').text(val['group_name']);
                            html.find('.register_time').text(val['team_num']);
                            $('#agent-child-data').append(html);
                        });
                        if (data['page'] === data['total_page']) {
                            agent_child.endUpLoading(true);
                            $('.minirefresh-upwrap').hide();
                        } else {
                            agent_child_data.page = data['page'] + 1;
                            agent_child.endUpLoading();
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
            url: "{:folder_url('Ajax/team_list')}",
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
