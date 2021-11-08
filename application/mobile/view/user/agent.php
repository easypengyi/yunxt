{extend name="public/base" /}

{block name="before_scripts"}
<link rel="stylesheet" type="text/css" href="__STATIC__/minirefresh/dist-2.0.2/minirefresh.min.css"/>
{/block}

{block name="main-content"}
<div class="center ohei">
    <div class="header" style="background: #fafafa; border-bottom: none;position:fixed;z-index:999;top:0;left:0;">
        <a href="{$return_url}"><img src="__MODULE_IMG__/ic21.png" alt=""></a>
        <h1>我的分销商</h1>
    </div>
    <div class="tabs dt" style="position:fixed;z-index:999;top:1.01rem;left:0;">
        <span class="acti" data-cate="1"><i>一级分销商</i></span>
        <span data-cate="2"><i>二级分销商</i></span>
    </div>
    <div class="user-form" style="left:0;margin-top:2.01rem;">
        <div class="box">
            <div class="top-box tb2">
                <span>用户</span>
                <span>加入时间</span>
                <span>消费金额</span>
                <span>佣金收入</span>
                <span>下级</span>
            </div>
            <div class="agent-list">
                <ul id="agent_parent" class="minirefresh-wrap ul2">
                    <div id="agent-parent-data" class="minirefresh-scroll">
                    </div>
                </ul>
            </div>
        </div>
        <div class="box">
            <div class="top-box">
                <span>用户</span>
                <span>加入时间</span>
                <span>消费金额</span>
                <span>佣金收入</span>
            </div>
            <div class="agent-list">
                <ul id="agent_child" class="minirefresh-wrap">
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
    <li data-id="">
        <div class="col">
            <div class="pic"><img src="" alt="" class="member_headpic"/></div>
            <span class="member_nickname">用户昵称</span>
        </div>
        <div class="col"><p class="register_time">2018-09-09</p></div>
        <div class="col"><p class="total_amount">￥299</p></div>
        <div class="col"><p class="total_income">￥29</p></div>
        <div class="col"><a href="{:controller_url('agent_below')}" class="show-agent">查看</a></div>
    </li>
</div>

<div id="agent_child_data">
    <li data-id="">
        <div class="col">
            <div class="pic"><img src="" alt="" class="member_headpic"/></div>
            <span class="member_nickname">用户昵称</span>
        </div>
        <div class="col"><p class="register_time">2018-09-09</p></div>
        <div class="col"><p class="total_amount">￥299</p></div>
        <div class="col"><p class="total_income">￥29</p></div>
    </li>
</div>
{/block}

{block name="scripts"}
<script src="__STATIC__/minirefresh/dist-2.0.2/minirefresh.min.js"></script>
<!--suppress JSCheckFunctionSignatures, JSValidateTypes, JSUnresolvedFunction -->
<script>
    var agent_parent_data = {
        page: 1,
        level: 1
    };

    var agent_child_data = {
        page: 1,
        level: 2
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
                    invitation_list(agent_parent_data, function (data) {
                        if (data['page'] === 1) {
                            $('#agent-parent-data').html('');
                        }

                        data['list'].forEach(function (val) {
                            var html = $('#agent_parent_data').find('li').clone();
                            html.attr('data-id', val['member']['member_id']);
                            html.find('.member_headpic').attr('src', val['member']['member_headpic']['full_url']);
                            html.find('.member_nickname').text(val['member']['member_nickname']);
                            html.find('.register_time').text(format_time('yyyy-MM-dd hh:mm', val['member']['create_time'] * 1000));
                            html.find('.total_amount').text('￥' + val['total_amount']);
                            html.find('.total_income').text('￥' + val['total_income']);
                            $('#agent-parent-data').append(html);
                        });
                        if (data['page'] === data['total_page']) {
                            agent_parent.endUpLoading(true);
                            $('.minirefresh-upwrap').hide();
                        } else {
                            agent_parent_data.page = data['page'] + 1;
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
                            html.attr('data-id', val['member']['member_id']);
                            html.find('.member_headpic').attr('src', val['member']['member_headpic']['full_url']);
                            html.find('.member_nickname').text(val['member']['member_nickname']);
                            html.find('.register_time').text(format_time('yyyy-MM-dd hh:mm', val['member']['create_time'] * 1000));
                            html.find('.total_amount').text('￥' + val['total_amount']);
                            html.find('.total_income').text('￥' + val['total_income']);
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

        $('body').on('click', '.show-agent', function () {
            var li = $(this).parents('li');
            var id = li.data('id');
            window.location.href = encode_url(this.href, {invitation_id: id});
            return false;
        });
    });

    // 邀请列表
    function invitation_list(param, callback) {
        $.ajax({
            type: 'POST',
            url: "{:folder_url('Ajax/invitation_list')}",
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
