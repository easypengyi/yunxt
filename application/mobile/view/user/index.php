{extend name="public/base" /}

{block name="before_scripts"}
<style>

    .midd-pass{
        width: 95%;
        margin-bottom: 1.2rem;
        border-radius: 10px;
    }

    .midd-pass ul li:last-child {
        border-bottom: 1px solid #ccc;
    }
    .ii{
        display: inline-block;
        height: 0.4rem;
        margin-top: 0.35rem;
        float: left;
        margin-left: 0.2rem;
    }

    .order-item ul li{
        height: 1.2rem;
        margin-top: 0.3rem;
        border-right: 1px solid #ccc;
    }
    .order-item-h4{
        color: #505050 !important;
        font-size: 12px !important;
    }

</style>
{/block}

{block name="main-content"}
<div class="center" >
    <div class="personal-head">
           <a href="{:folder_url('User/personal_info')}">
            <h4>
                <img src="{$member.member_headpic.full_url|default='__MODULE_IMG__/ic28.png'}" alt=""/>
            </h4>
           </a>
            <h3>
                <span style="font-weight: bold;font-size: 0.3rem;">{$member.member_realname|default=''}</span>
                <span style="font-size: 0.2rem;"> {$member.group_name|default=''}
                      <?php if (isset($member['is_center']) && !empty($member['is_center'])): ?>  | {$member.area}报单中心<?php endif; ?>
                </span>
                <span style="display: block;line-height: 0;">余额: {$member.balance_amount}</span>
            </h3>

        <a href="{:folder_url('User/message')}"><span class="mess">
                <?php if (($no_read_number ?? 0) !== 0): ?>
                    <span class="mesd" style="margin-top:0;float: left;margin-left: .4rem;"></span>
                <?php endif; ?>
            </span>
        </a>
    </div>
    <div class="order-box" style=" height: 2.3rem; top: 2rem;  position: absolute; right: 0; left: 0;">
        <p><span class="my-order">我的订单</span><a style="display:inline-block;float: right;margin-right: 0.2rem;"  href="{:folder_url('User/order1')}"><span class="all-order">全部订单 &nbsp;<i style="    display: inline-block;
    width: .25rem;
    height: .25rem;
    background: url(__MODULE_IMG__/right-icon.png) center no-repeat;
    -webkit-background-size: 100% 100%;
    background-size: 100% 100%;
    float: right;
    margin-right: .3rem;
    margin-top: -0.05rem;
"></i></span></a></p>
        <ul>
            <li class="order_li">
                <a href="{:folder_url('User/order1?category=2')}">
                    <h4><span><img  class="order_img" src="__MODULE_IMG__/dfk.png" alt=""/></span></h4>
                    <?php if (($order_status_num['dfk'] ?? 0) !== 0): ?>
                        <span class="order_mesd">{$order_status_num['dfk']}</span>
                    <?php endif; ?>
                    <h4>待付款</h4>
                </a>
            </li>
            <li class="order_li">
                <a href="{:folder_url('User/order1?category=3')}">
                    <h4><span><img   class="order_img" src="__MODULE_IMG__/dfh.png" alt=""/></span></h4>
                    <?php if (($order_status_num['dfh'] ?? 0) !== 0): ?>
                        <span class="order_mesd">{$order_status_num['dfh']}</span>
                    <?php endif; ?>
                    <h4>待发货</h4>
                </a>
            </li>
            <li class="order_li">
                <a href="{:folder_url('User/order1?category=4')}">
                    <h4><span><img  class="order_img" src="__MODULE_IMG__/dsh.png" alt=""/></span></h4>
                    <?php if (($order_status_num['dsh'] ?? 0) !== 0): ?>
                        <span class="order_mesd">{$order_status_num['dsh']}</span>
                    <?php endif; ?>
                    <h4>待收货</h4>
                </a>
            </li>
            <li class="order_li">
                <a href="{:folder_url('User/order1?category=6')}">
                    <h4><span><img   class="order_img" src="__MODULE_IMG__/ywc.png" alt=""/></span></h4>
                    <h4>已完成</h4>
                </a>
            </li>
        </ul>
    </div>

    <div style=" margin-top: -0.5rem; background: #f4f4f4; border-radius: 20px;">

        <div class="zsdiv" style="width: 95%">
            <img  class="zsbanner" src="__MODULE_IMG__/zsbanner.jpg">
            <a href="{:folder_url('User/investment')}" >
            <ul >
                <li><a href="{:folder_url('help/article_detail',['article_id'=>29])}">执行董事</a></li>
                <li><a href="{:folder_url('help/article_detail',['article_id'=>30])}">全球合伙人</a></li>
                <li style="border: unset;"><a href="{:folder_url('help/article_detail',['article_id'=>31])}">联合创始人</a></li>
            </ul>
            </a>
        </div>


            <?php if (($member['group_id'] ?? 0) <= 2 || ($member['group_id'] ?? 0) == 7): ?>
                <div class="order-box">
                    <div class="order-item" >
                        <ul>
                            <li>
                                <a href="{:folder_url('User/reward')}">
                                    <h4>&nbsp;{$member.commission}</h4>
                                    <h4 class="order-item-h4">&nbsp;奖金币(元)</h4>
                                </a>
                            </li>
                            <li style="border: unset;">
                                <a href="{:folder_url('User/amount1')}">
                                    <h4 >&nbsp;{$member.balance}</h4>
                                    <h4 class="order-item-h4">&nbsp;云库存(瓶)</h4>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            <?php else: ?>
                <div class="order-box">
                    <div class="order-item" >
                        <ul>
                            <li style="width: 33.3%">
                                <a href="{:folder_url('User/reward')}">
                                    <h4>&nbsp;{$member.commission}</h4>
                                    <h4 class="order-item-h4">&nbsp;奖金币(元)</h4>
                                </a>
                            </li>
                            <li style="width: 33.3%">
                                <a href="{:folder_url('User/amount1')}">
                                    <h4 >&nbsp;{$member.balance}</h4>
                                    <h4  class="order-item-h4">&nbsp;云库存(瓶)</h4>
                                </a>
                            </li>
                            <?php if (($member['group_id'] ?? 0) === 5): ?>
                                <li style="width: 33.3%; border: unset;">
                                    <a href="{:folder_url('User/amount2')}">
                                        <h4>&nbsp;{$first_commission}</h4>
                                        <h4 class="order-item-h4">奖金池(元)</h4>
                                    </a>
                                </li>
                            <?php endif; ?>

                            <?php if (($member['group_id'] ?? 0) === 4): ?>
                                <li style="width: 33.3%;border: unset;">
                                    <a href="{:folder_url('User/amount2')}">
                                        <h4>&nbsp;{$second_commission}</h4>
                                        <h4 class="order-item-h4">&nbsp;奖金池(元)</h4>
                                    </a>
                                </li>
                            <?php endif; ?>

                            <?php if (($member['group_id']?? 0) === 3): ?>
                                <li style="width: 33.3%;border: unset;">
                                    <a href="{:folder_url('User/amount2')}">
                                        <h4>&nbsp;{$three_commission}</h4>
                                        <h4 class="order-item-h4">&nbsp;奖金池(元)</h4>
                                    </a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </div>
                </div>
            <?php endif; ?>

        <div class="midd-pass">
            <ul>
                <a href="{:folder_url('User/invitation_code')}">
                    <li><img src="__MODULE_IMG__/user_index16.png"  class="ii"> &nbsp;&nbsp;推广二维码<i></i></li>
                </a>
                    <a href="{:folder_url('Product/one_index')}">
                        <li><img src="__MODULE_IMG__/user_index1.png"  class="ii"> &nbsp;&nbsp;系统报单<i></i></li>
                    </a>
                    <a href="{:folder_url('User/order')}">
                        <li><img src="__MODULE_IMG__/user_index2.png"  class="ii">&nbsp;&nbsp;系统订单<i></i></li>
                    </a>
                    <a href="{:folder_url('User/team')}">
                        <li><img src="__MODULE_IMG__/user_index3.png" class="ii">&nbsp;&nbsp;我的团队<i></i></li>
                    </a>
                <?php if ($member['bind_mobile'] == false): ?>
                    <a href="{:folder_url('Login/phone_bind')}">
                        <li><img src="__MODULE_IMG__/phone_bind.png"  class="ii"> &nbsp;&nbsp;绑定手机<i></i></li>
                    </a>
                <?php endif; ?>
                <a href="{:folder_url('User/address')}">
                    <li><img src="__MODULE_IMG__/user_index15.png"  class="ii"> &nbsp;&nbsp;我的地址<i></i></li>
                </a>
                <a href="{:folder_url('User/personal_info')}">
                    <li><img src="__MODULE_IMG__/myself.png"  class="ii"> &nbsp;&nbsp;个人信息<i></i></li>
                </a>

                <a href="{:folder_url('User/personal')}" >
                    <li style="border-bottom: unset !important;"><img src="__MODULE_IMG__/user_index5.png"  class="ii"> &nbsp;&nbsp;设置<i></i></li>
                </a>
            </ul>
        </div>
    </div>




</div>


<!--底部菜单栏开始-->
{include file='public/footer' activate='5'/}
<!--底部菜单栏结束-->
{/block}

{block name="hide-content"}
{/block}
{block name="scripts"}
<script>
    $(function () {
        $('.personal-head span.menus').click(function () {
            $('.alert').show();
            $('.slide-menu').animate({'top': 0 + 'rem'}, 300);
        });
        $('.slide-menu span.close').click(function () {
            $('.alert').hide();
            $('.slide-menu').animate({'top': -6.55 + 'rem'}, 300);
        });
        $('.slide-menu li').click(function () {
            window.location.href = $(this).data('href');
        })

    });

    function copy() {
        var message = $('#UID').text();
        var input = document.createElement("input");
        input.value = message;
        document.body.appendChild(input);
        input.select();
        input.setSelectionRange(0, input.value.length), document.execCommand('Copy');
        document.body.removeChild(input);
        show_message('UID复制成功');
    }



</script>
{/block}
