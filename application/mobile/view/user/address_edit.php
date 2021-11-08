{extend name="public/base" /}

{block name="before_scripts"}
<link rel="stylesheet" type="text/css" href="__STATIC__/LArea/css/LArea.min.css?version={$file_version}"/>
<style>
    .address-input {
        background-size: 0.25rem !important;
    }
</style>
{/block}

{block name="main-content"}
<div class="mobile-wrap center">
    <div class="header" style="position:fixed;z-index:999;top:0;left:0;">
        <a href="{$return_url}"><img src="__MODULE_IMG__/ic21.png" alt=""></a>
    </div>
    <div class="address-box" style="position: relative;top:0.71rem;left:0;">
        <form class="ajax-form" action="{$full_url}" method="post">
            <input type="hidden" name="return_url" value="{$return_url}"/>
            <ul>
                <li>
                    <span>收货人</span>
                    <input type="text" id="consignee" name="consignee" placeholder="姓名" value="{$data_info.consignee|default=''}"/>
                </li>
                <li>
                    <span>联系手机</span>
                    <input type="tel" id="mobile" name="mobile" maxlength="11" placeholder="手机号" value="{$data_info.mobile|default=''}"/>
                </li>
                <li>
                    <span>所在地区</span>
                    <input class="address-input" id="area_text" type="text" readonly="readonly" placeholder="选择地区">
                    <input id="province" name="province" type="hidden" value="{$data_info.province.id|default=0}"/>
                    <input id="city" name="city" type="hidden" value="{$data_info.city.id|default=0}"/>
                    <input id="district" name="district" type="hidden" value="{$data_info.district.id|default=0}"/>
                </li>
                <li>
                    <span>详细地址</span>
                    <input type="text" id="address" name="address" placeholder="街道、小区、门牌号" value="{$data_info.address|default=''}"/>
                </li>
            </ul>
        </form>
    </div>
    <div class="null"></div>
    <div class="fixed-btn center"><a class="submit" href="javascript:">保存地址</a></div>
</div>
{/block}

{block name="hide-content"}
{/block}

{block name="scripts"}
<script src="__STATIC__/LArea/js/LArea.min.js?version={$file_version}"></script>
<script>
    $(function () {
        //选择地区
        regionSelection();

        $('.submit').click(function () {
            $('.ajax-form').submit();
        })
    });

    // 提交验证
    function check_form() {
        if (!$('#mobile').val().match(/^(((13[0-9])|(14[57])|(15[0-9])|(16[6])|(17[0-9])|(18[0-9])|(19[8-9]))+\d{8})$/)) {
            show_message('请输入正确手机格式！');
            return false;
        }

        if ($('#consignee').val() === '') {
            show_message('请输入收货人！');
            return false;
        }

        if ($('#address').val() === '') {
            show_message('请输入详细地址！');
            return false;
        }

        return true;
    }

    //选择地区
    function regionSelection() {
        var larea = new LArea();
        larea.init({
            //触发选择控件的文本框，同时选择完毕后name属性输出到该位置
            'trigger': '#area_text',
            //选择完毕后id属性输出到该位置
            'valueTo': '#area',
            //绑定数据源相关字段 id对应valueTo的value属性输出 name对应trigger的value属性输出
            'keys': {
                id: 'id',
                name: 'name',
                list: 'list'
            },
            //数据源(可设置成动态数据,类型为字符串)
            'data': "{:folder_url('Region/area')}",
            //控制初始位置,默认地区选择(填入数据库存储值)
            'default_value': [$('#province').val(), $('#city').val(), $('#district').val()],
            //地区name属性，间隔符号设置
            'text_space': '-',
            //地区id属性，间隔符号设置
            'value_space': ',',
            //回调函数，选择地址后触发其他方法,a为地址id值数组，b为地址name值数组
            'change': function (id, name) {
                $('#province').val(id[0]);
                $('#city').val(id[1]);
                $('#district').val(id[2]);
            }
        });
    }
</script>
{/block}
