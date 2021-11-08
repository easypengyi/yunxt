{extend name="public/base" /}

{block name="before_scripts"}
<link rel="stylesheet" type="text/css" href="__STATIC__/LArea/css/LArea.min.css?version={$file_version}"/>
<link rel="stylesheet" type="text/css" href="__STATIC__/mobileSelect/css/mobileSelect.css"/>
<style>
    li {
        float: unset;
    }
    .address-input{
        font-size: 0.25rem;
        width: 100%;
        border: 1px solid rgb(204, 204, 204) !important;
        height: 0.85rem;
        padding-left: 0.3rem;
        margin-bottom: 0.3rem;
        border-radius: 50px;
        background-size: 0.25rem !important;
    }
</style>
{/block}

{block name="main-content"}
<div class="header header-bt" style="position:fixed;z-index:999;top:0;left:0;">
    <a href="{$return_url}">
        <img src="__MODULE_IMG__/ic21.png" alt="">
    </a>
</div>
    <div  id="head">
        <div class="container" style="margin-top:2rem;">
        <div class="login-box">
            <div >
                <li  style="width: 100%;">
                    <input  class="address-input"  id="trigger" type="text" readonly="readonly" placeholder="选择经销级别">

                </li>
            </div>
            <div >
                <li  style="width: 100%;">
                    <input  class="address-input"  id="trigger1" type="text" readonly="readonly" placeholder="选择结算方式">
                </li>
            </div>
                <ul>
                    <li>
                        <div class="star pic"><b  style=" margin-left: 0.3rem;line-height: 1rem;font-size: 0.6rem;color: #095e3b;">*</b></div>
                        <input type="text" placeholder="请输入用户姓名"  id="nick_name">
                    </li>
                    <li>
                        <div class="star pic"><b  style=" margin-left: 0.3rem;line-height: 1rem;font-size: 0.6rem;color: #095e3b;">*</b></div>
                        <input type="tel" maxlength="11" placeholder="请输入用户手机号"  id="mobile">
                    </li>
                    <li>
                        <div class="star pic"><b style=" margin-left: 0.3rem;line-height: 1rem;font-size: 0.6rem;color: #095e3b;">*</b></div>
                        <input type="tel" maxlength="18"  placeholder="请输入用户身份证号"  id="id_code">
                    </li>


                    <li class="jdr">
                        <div class="star pic"><b  style=" margin-left: 0.3rem;">选填</b></div>
                        <input type="tel"  maxlength="11"  placeholder="请输入接点人手机号"  id="two_mobile">
                    </li>
                    <li class="jdr">
                        <div class="star pic"><b  style=" margin-left: 0.3rem;">选填</b></div>
                        <input type="text"    placeholder="请输入接点人姓名"  id="two_name">
                    </li>
                     <p><a href="{:folder_url('Help/agreement')}" style="color: #095e3b;border-bottom: 1px solid;">报单规则</a></p>
                    <li class="apply" id="buy" style=";background:#095E3B;text-align: center;color: white; margin-top: -0.5rem;">立即报单</li>
                </ul>

        </div>
        </div>
</div>

<!--<div class="activityModel" >-->
<!--    <div class="grzx-tc1">-->
<!--        <div class="grzx-tcc animated" style="background-color:white;height:100%;width:100%;position: fixed;">-->
<!--            <div style="height:8rem;width: 95%;margin-top: 2rem;overflow: scroll;">-->
<!--                {$agreement}-->
<!--            </div>-->
<!--            <p style="text-align: center;margin-top: 0.5rem;"><label><input style="margin-right: 0.1rem;"   type="checkbox" id="checkbox">我 已 阅 读 并 同 意 以 上 协 议</label></p>-->
<!--            <li style="width: 100%;text-align: center;margin-top: 0.5rem;">-->
<!--                <button style="background: #74bfff;text-align: center;color: white;font-size: 0.3rem;width: 70%;height: 0.8rem;" class="next">下一步</button>-->
<!--            </li>-->
<!--        </div>-->
<!--    </div>-->
<!--</div>-->
{/block}


{block name="scripts"}
<script src="__STATIC__/LArea/js/LArea.min.js?version={$file_version}"></script>
<script src="__STATIC__/mobileSelect/js/mobileSelect.js"></script>
<script>
    $(function () {

        $('.next').click(function () {
            var res =  $('#checkbox').is(':checked');
            if (res){
                $('.activityModel').css('display','none');
            }else{
                return;
            }
        });


        $('.submit').click(function () {
            $('.ajax-form').submit();
        })
    });

    var product = {
        group_id:'{$member.group_id}',

    };



    var weekdayArr = ["代理人", "执行董事", "全球合伙人", "联合创始人"];

    var mobileSelect1 = new MobileSelect({
        trigger: "#trigger",
        title: "选择经销级别",
        wheels: [{ data: weekdayArr }],
        position: [0], //初始化定位 打开时默认选中的哪个 如果不填默认为0
        callback: function (indexArr, data) {
            $('#trigger').val(data);
        },
    });




    var weekdayArr1 = ["云库存结算", "平台结算"];

    var weekdayArr1 = new MobileSelect({
        trigger: "#trigger1",
        title: "选择结算方式",
        wheels: [{ data: weekdayArr1 }],
        position: [0], //初始化定位 打开时默认选中的哪个 如果不填默认为0
        callback: function (indexArr, data) {
            $('#trigger1').val(data);
        },
    });


    $('#buy').click(function () {
        var trigger =     $('#trigger').val();
        var trigger1 =     $('#trigger1').val();
        var nick_name =   $('#nick_name').val();
        var mobile =      $('#mobile').val();
        var id_code     = $('#id_code').val();
        var two_mobile =  $('#two_mobile').val();
        var two_name   =  $('#two_name').val();

        if (trigger ==''){
            show_message('经销级别不能为空！');
            return false;
        }

        if (trigger1 ==''){
            show_message('结算方式不能为空！');
            return false;
        }

        if (mobile ==''){
            show_message('用户手机号码不能为空！');
            return false;
        }

        if (nick_name ==''){
            show_message('用户姓名不能为空！');
            return false;
        }

        if (id_code ==''){
            show_message('用户身份证号不能为空！');
            return false;
        }



        if (!mobile.match(/^(((13[0-9])|(14[57])|(15[0-9])|(16[6])|(17[0-9])|(18[0-9])|(19[8-9]))+\d{8})$/)) {
            show_message('请输入正确的手机号！');
            return false;
        }

        $.ajax({
            type: 'POST',
            url: "{:folder_url('Ajax/single_settlement')}",
            data: {trigger:trigger,trigger1:trigger1,two_name: two_name,two_mobile: two_mobile, mobile:mobile,nick_name:nick_name,id_code:id_code},
            success: function (result) {
                if (result.code !== 1) {
                    show_message(result.msg);
                    if (result.url !== '' && typeof (result.url) !== 'undefined') {
                        window.location.href = result.url;
                        return;
                    }
                    return;
                }
                window.location.href =  encode_url('order_submit');
            }
        });
    });



</script>
<script type="text/javascript">

</script>
{/block}