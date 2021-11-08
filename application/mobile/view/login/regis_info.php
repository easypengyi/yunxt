{extend name="public/base" /}

{block name="before_scripts"}
<link rel="stylesheet" type="text/css" href="__STATIC__/LArea/css/LArea.min.css?version={$file_version}"/>
{/block}

{block name="main-content"}
<div class="mobile-wrap center">
    <div class="header" style="position:fixed;z-index:999;top:0;left:0;"><a href="{:controller_url('register')}"><img src="__MODULE_IMG__/ic21.png" alt=""></a>
        <h1>补充信息</h1><a href="{:folder_url('User/index')}"><span>跳过</span></a></div>
    <form class="ajax-form" action="{$full_url}" method="post"  style="position: relative;top:1.01rem;left:0;">
        <div class="user-up">
            <div class="box">
                <div class="pic"><img src="__MODULE_IMG__/ic28.png" id="avarimg" alt=""></div>
                <span>上传头像</span>
                <input type="file" name="file" id="file" accept="image/*">
            </div>
        </div>
        <div class="address-box">
            <ul>
                <li>
                    <span>昵称</span>
                    <input type="text" value="" name="nickname" placeholder=""/>
                </li>
                <li>
                    <span>昵称</span>
                    <label><input type="radio" name="sex" value="1" checked>男</label>
                    <label><input type="radio" name="sex" value="2">女</label>
                </li>
                <li>
                    <span>生日</span>
                    <input type="text" name="birthday" id="birthday" placeholder="">
                </li>
                <li>
                    <span>邮箱</span>
                    <input type="text" value="" name="mail" placeholder=""/>
                </li>
            </ul>
        </div>
        <div class="address-box">
            <ul>
                <li>
                    <span>收货人</span>
                    <input type="text" name="consignee" placeholder="请输入收货人姓名">
                </li>
                <li>
                    <span>联系手机</span>
                    <input type="tel" name="telephone" placeholder="请输入收货人联系手机或电话" maxlength="11">
                </li>
                <li>
                    <span>所在地区</span>
                    <input class="address-input" id="area_text" type="text" readonly="readonly" placeholder="选择地区" value="">
                    <input id="province" name="province" type="hidden" value="{$data_info.province.id|default=0}"/>
                    <input id="city" name="city" type="hidden" value="{$data_info.city.id|default=0}"/>
                    <input id="district" name="district" type="hidden" value="{$data_info.district.id|default=0}"/>
                </li>
                <li>
                    <span>详细地址</span>
                    <input type="text" name="address" placeholder="街道、小区、楼牌号等">
                </li>
            </ul>
        </div>
        <div class="null2"></div>
        <div class="fixed-btn center"><a href="javascript:" id="submit">提交</a></div>
    </form>
</div>
{/block}

{block name="hide-content"}
{/block}

{block name="scripts"}
<script src="__STATIC__/laydate/dist-5.0.9/laydate.js"></script>
<script src="__STATIC__/LArea/js/LArea.min.js?version={$file_version}"></script>
<script>
    $(function () {
        //选择地区
        regionSelection();

        laydate.render({
            elem: '#birthday',
        });

        $('#file').change(function () {
            var objUrl = get_file_url(this.files[0]);
            if (objUrl) {
                $('#avarimg').attr('src', objUrl);
            }
        });

        $('#submit').click(function () {
            $('.ajax-form').submit();
        })
    });

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
