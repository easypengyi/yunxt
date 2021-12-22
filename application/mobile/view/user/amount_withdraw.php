{extend name="public/base" /}

{block name="before_scripts"}
<link rel="stylesheet" type="text/css" href="__STATIC__/mobileSelect/css/mobileSelect.css"/>
<style>
    li {
        float: unset;
    }
</style>
{/block}
{block name="main-content"}
<div class="mobile-wrap center">
    <div class="header" style="position:fixed;z-index:999;top:0;left:0;">
        <a href="{$return_url}"><img src="__MODULE_IMG__/ic21.png" alt=""></a>
    </div>
    <div class="midd-pass" style="margin-top: 0.75rem;padding-bottom: 0.2rem;">
        <ul>
            <li style="border-bottom: none !important;">
                <div style="display:inline-block;">
           <span style="color:#333">
              提现方式：
            </span>
                    <span style="color: darkred;position:relative;top: 0.3rem;left: -1.5rem;font-size: 0.05rem;">提现将额外扣除0.6%手续费。</span>
                </div>
                <div style="display:inline-block;float: right;">
                    <span style="margin-right: 0.1rem;" class="trigger">银行卡</span>
                    <i style="margin-right: 0.05rem;"></i>
                </div>
            </li>
        </ul>
    </div>
    <div class="pers-inform" style="position: relative;left:0;">
        <form class="ajax-form" action="{$full_url}" method="post">
<!--            <div>-->
<!--                <div class="label fl" style="color: darkred">提现将额外扣除{$service_money}%手续费。</div>-->
<!--            </div>-->
            <div>
                <label>提现金额：</label>
                <input type="tel" maxlength="8" placeholder="请输入提现金额" name="money"/>
            </div>
            <div class="div1 sl">
                <label>银行卡号：</label>
                <input type="text" placeholder="请输入银行卡号"   value="{$member.account}" name="account"/>
            </div>
            <div class="div1 sl">
                <label>银行名称：</label>
                <input type="text" placeholder="请输入银行名称"  value="{$member.bank_name}"  name="bank_name"/>
            </div>
            <div class="div1 sl">
                <label>分行/支行：</label>
                <input type="text" placeholder="请输入开户行/分行/支行"  value="{$member.blank}"  name="blank"/>
            </div>
            <div class="div1 sl">
                <label>持卡人：</label>
                <input type="text" placeholder="请输入持卡人姓名"   value="{$member.real_name}"  name="real_name"/>
            </div>
            <div style="height: 4rem;display: none" class="div2 div3 sl">
                <label style="line-height: 3rem;">上传收款码:</label>
                <div style="background: #f3f3f3;border-radius: 0.2rem;position: relative;width: 3.0rem;height: 3rem;margin-top: 1rem;border: none;">
                    <img id="ewm_code" src="" alt="" style="width: 100%;display: none">
                    <div class="jia" style="width: 1.5rem;height: 0rem;transform: translate(0, 1.5rem);border-top: 0.04rem solid #DCDCDC;"></div>
                    <div class="jia" style="width: 0rem;height: 1.5rem;transform: translate(0, 0.75rem);transform-origin: center;border-left: 0.04rem solid #DCDCDC;"></div>
                    <input type="file" name="file" id="file" accept="image/*" style="position:absolute;top:0;left:0;opacity: 0;width: 3.5rem;height: 3.5rem;">
                </div>
            </div>
            <div class="div1 sl">
                <label>手机号码：</label>
                <input type="text" value="{$member.member_tel|default=''}" style="color: #333333;" placeholder="" readonly/>
                <input type="hidden" name="mobile" id="mobile" value="{$member.member_tel|default=''}">
                <input type="hidden" name="type" id="type" value="1">
            </div>
        </form>
    </div>
    <div class="null"></div>
    <div class="fixed-btn center"><a id="submit" href="javascript:">立即提现</a></div>
</div>
{/block}

{block name="hide-content"}
{/block}

{block name="scripts"}
<script src="__STATIC__/mobileSelect/js/mobileSelect.js"></script>
<!--suppress JSUnusedLocalSymbols -->
<script>
    $(function () {
        $('#submit').click(function () {
            $('.ajax-form').submit();
        });

        $('#file').change(function () {
            var objUrl = get_file_url(this.files[0]);
            if (objUrl) {
                $('#ewm_code').show();
                $('.jia').hide();
                $('#ewm_code').attr('src', objUrl);
            }
        });

        $('#yzm_tx').click(function () {

            if ($(this).data('wait') !== 0) {
                return false;
            }

            var mobile = $('#mobile').val();
            if (!mobile.match(/^(((13[0-9])|(14[57])|(15[0-9])|(16[6])|(17[0-9])|(18[0-9])|(19[8-9]))+\d{8})$/)) {
                show_message('请输入正确手机格式！');
                return false;
            }

            sms_count_down(60);

            $.ajax({
                type: 'POST',
                url: "{:folder_url('Sms/add_receivables_card')}",
                data: {mobile: mobile},
                success: function (data) {
                    if (data.code !== 1) {
                        sms_count_down(0);
                    }
                    show_message(data.msg);
                }
            });
            return false;
        });
    });

    // 注册短信验证码倒计时
    function sms_count_down(value) {
        set_sms_count_down(value, '#yzm_phone2', function (view, second) {
            view.text('重新发送(' + second + ')');
        }, function (view) {
            view.text('获取验证码');
        });
    }

    var weekdayArr = ["银行卡", "微信", "支付宝", "余额"];

    var mobileSelect1 = new MobileSelect({
        trigger: ".trigger",
        title: "选择收款方式",
        wheels: [{ data: weekdayArr }],
        position: [0], //初始化定位 打开时默认选中的哪个 如果不填默认为0
        callback: function (indexArr, data) {
            var type = parseInt(indexArr)+1;
            $('#type').val(type);
            // $('#type_name').text(data);
            selectType(type)
        },
    });

    function selectType(type){
        $(".sl").hide();
        $(".div"+type).show();
    }
</script>
{/block}
