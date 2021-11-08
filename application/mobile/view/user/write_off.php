{extend name="public/base" /}

{block name="main-content"}
<div class="mobile-wrap center">
    <div class="header" style="position:fixed;z-index:999;top:0;left:0;">
        <a href="{$return_url}"><img src="__MODULE_IMG__/ic21.png" alt=""></a>
    </div>
    <div class="pers-inform" style="position: relative;top:1.01rem;left:0;">
        <form class="ajax-form" action="{$full_url}" method="post">
            <div style=" border-bottom: 1px solid #ccc;">
                <label>订单号：</label>
                <input style="width: 70%;height: 0.6rem;" type="text" disabled="disabled" value="{$order_sn}"  name="order_sn"/>
            </div>

            <div style=" border-bottom: 1px solid #ccc;">
                <label>姓名：</label>
                <input style="width: 70%;height: 0.6rem;" type="text" disabled="disabled" value="{$user['member_realname']}"  name="member_realname"/>
            </div>
            <div style=" border-bottom: 1px solid #ccc;">
                <label>手机号：</label>
                <input style="width: 70%;height: 0.6rem;" type="text" disabled="disabled" value="{$user['member_tel']}"  name="member_tel"/>
            </div>

            <div style=" border-bottom: 1px solid #ccc;">
                <label>身份证：</label>
                <input style="width: 70%;height: 0.6rem;" type="text" disabled="disabled" value="{$user['uid']}"  name="uid"/>
            </div>

            <div style=" border-bottom: 1px solid #ccc;">
                <label>核销机构：</label>
                <input   style="width: 70%;height: 0.6rem;" type="text"  value="{$name}"  name="name" readonly/>
            </div>

            <div style=" border-bottom: 1px solid #ccc;">
                <label>血液编号：</label>
                <input  id="blood_number"  style="width: 70%;height: 0.6rem;" type="text"   name="blood_number" readonly/>
            </div>


        </form>
    </div>
    <div class="null"></div>
    <div style="width: 50%; border-right: 1px solid white;" class="fixed-btn center">
        <a id="scan" href="javascript:">扫一扫</a>
    </div>
    <div style="width: 50%;right: 0;left: unset" class="fixed-btn center">
        <a id="submit" href="javascript:">核销</a>
    </div>
</div>
{/block}

{block name="hide-content"}
{/block}

{block name="scripts"}
<!--suppress JSUnusedLocalSymbols -->
<script>
    $(function () {
        $('#submit').click(function () {
            $('.ajax-form').submit();
        });

    });

    $('#scan').click(function () {
        scanQRCodeByWX();
    });

    function scanQRCodeByWX() {
        wx.scanQRCode({
            needResult: 1, // 默认为0，扫描结果由微信处理，1则直接返回扫描结果，
            scanType: ['barCode'], // 可以指定扫二维码还是一维码，默认二者都有
            success: function(res){
                $('#blood_number').val(res.resultStr.split(",")[1]);
            }
        });
    }

</script>
{/block}
