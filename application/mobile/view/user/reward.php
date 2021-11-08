{extend name="public/base" /}

{block name="before_scripts"}
<link rel="stylesheet" type="text/css" href="__STATIC__/minirefresh/dist-2.0.2/minirefresh.min.css"/>
<style>

    .midd-pass{
        margin-top: 0.75rem;
        padding: unset;
    }
    .midd-pass ul li:last-child {
        border-bottom: 1px solid #ccc;
    }
    .midd-pass span{
        color: red;
    }

    .ii{
        display: inline-block;
        height: 0.4rem;
        margin-top: 0.35rem;
        float: left;
        margin-left: 0.2rem;
    }
    .user_text p{
        height: 0.5rem;
        line-height: 0.5rem;
    }
</style>
{/block}

{block name="main-content"}
<div class="center">
    <div class="header" style="position:fixed;z-index:999;top:0;left:0;">
        <a href="{$return_url}"><img src="__MODULE_IMG__/ic21.png" alt=""></a>
    </div>

    <div class="midd-pass" >
        <ul>
            <a href="{:folder_url('User/amount?type=11')}">
                <li><img src="__MODULE_IMG__/user_index6.png"  class="ii"> &nbsp;&nbsp;提现：<span>￥{$res['WITHDRAWALS']|default = '0.00'}</span><i></i></li>
            </a>

                <a href="{:folder_url('User/amount?type=3')}">
                    <li><img src="__MODULE_IMG__/user_index6.png"  class="ii"> &nbsp;&nbsp;分销奖：<span>￥{$res['maker3']|default = '0.00'}</span><i></i></li>
                </a>
                <a href="{:folder_url('User/amount?type=4')}">
                    <li><img src="__MODULE_IMG__/user_index6.png"  class="ii">&nbsp;&nbsp;批发奖：<span>￥{$res['maker4']|default = '0.00'}</span><i></i></li>
                </a>
                <a href="{:folder_url('User/amount?type=5')}">
                    <li><img src="__MODULE_IMG__/user_index6.png" class="ii">&nbsp;&nbsp;管理奖：<span>￥{$res['maker5']|default = '0.00'}</span><i></i></li>
                </a>
            <a href="{:folder_url('User/amount?type=6')}">
                <li><img src="__MODULE_IMG__/user_index6.png"  class="ii"> &nbsp;&nbsp;维护奖：<span>￥{$res['maker6']|default = '0.00'}</span><i></i></li>
            </a>

            <a href="{:folder_url('User/amount?type=14')}">
                <li><img src="__MODULE_IMG__/user_index6.png"  class="ii"> &nbsp;&nbsp;开发奖：<span>￥{$res['maker14']|default = '0.00'}</span><i></i></li>
            </a>
            <a href="{:folder_url('User/amount?type=7')}">
                <li><img src="__MODULE_IMG__/user_index6.png"  class="ii"> &nbsp;&nbsp;职级奖：<span>￥{$res['maker7']|default = '0.00'}</span><i></i></li>
            </a>

            <a href="{:folder_url('User/amount?type=8')}">
                <li><img src="__MODULE_IMG__/user_index6.png"  class="ii"> &nbsp;&nbsp;报单奖：<span>￥{$res['maker8']|default = '0.00'}</span><i></i></li>
            </a>

            <a href="{:folder_url('User/amount?type=15')}">
                <li><img src="__MODULE_IMG__/user_index6.png"  class="ii"> &nbsp;&nbsp;城市特权奖：<span>￥{$res['city']|default = '0.00'}</span><i></i></li>
            </a>

            <a href="{:folder_url('User/amount?type=10')}">
                <li style="border: unset;"><img src="__MODULE_IMG__/user_index6.png"  class="ii"> &nbsp;&nbsp;活动推荐奖：<span>￥{$res['recommend']|default = '0.00'}</span><i></i></li>
            </a>
        </ul>
    </div>
</div>
{/block}


{block name="scripts"}
<script src="__STATIC__/minirefresh/dist-2.0.2/minirefresh.min.js"></script>
<script>

</script>
{/block}
