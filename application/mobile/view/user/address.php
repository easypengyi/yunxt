{extend name="public/base" /}

{block name="main-content"}
<div class="mobile-wrap center">
    <div class="header" style="position:fixed;z-index:999;top:0;left:0;">
        <a href="{:controller_url('index')}"><img src="__MODULE_IMG__/ic21.png" alt=""></a>
        <h1></h1>
    </div>
    <?php if (isset($data_list) && !empty($data_list)): ?>
        <div class="receipt-list" style="position: relative;top:0.71rem;left:0;">
            <ul>
                <?php foreach ($data_list as $v): ?>
                    <a href="javascript:" data-href="{$return_url}" class="choose-address">
                        <li>
                            <input type="hidden" class='address-id' value="{$v.address_id}"/>
                            <h3><span>{$v.consignee}</span><var>{$v.mobile}</var></h3>
                            <p>{$v.province.name}{$v.city.name}{$v.district.name}{$v.address}</p>
                            <div class="bottom">
                                <label class="address-tolerant" data-value="{$v.tolerant}"><input type="radio" name="1" id="" <?php echo $v['tolerant'] ? 'checked' : '' ?>>设为默认收货地址</label>
                                <span class="address-delete s2">删除</span>
                                <a data-href="{:controller_url('address_edit',['address_id'=>$v.address_id,'choose'=>$choose])}" style="float: right"><span class="s1">编辑</span></a>
                            </div>
                        </li>
                    </a>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>
    <div class="null"></div>
    <div class="fixed-btn center"><a href="{:controller_url('address_add',['choose'=>$choose])}">添加地址</a></div>
</div>
{/block}

{block name="hide-content"}
{/block}

{block name="scripts"}
<!--suppress JSUnusedLocalSymbols -->
<script>
    $(function () {

        $('body').on('click', '.address-tolerant', function () {
            // 默认收货地址变更
            var view = $(this);
            var address_id = view.parents('li').find('.address-id').val();
            var tolerant = !view.data('value') ? 1 : 0;

            $.ajax({
                type: 'POST',
                url: "{:folder_url('Ajax/address_default_change')}",
                data: {tolerant: tolerant, address_id: address_id},
                success: function (data) {
                    if (data.code !== 1) {
                        show_message(data.msg);
                        return;
                    }
                    $('.address-tolerant').data('value', 0);
                    view.data('value', tolerant);
                    var radio = view.find('input[type="radio"]');
                    if (radio.prop("checked")) {
                        radio.prop("checked", false);
                    } else {
                        radio.prop("checked", true);
                    }
                }
            });
            return false;
        }).on('click', '.address-delete', function () {
            var view = $(this);
            var address_id = view.parents('li').find('.address-id').val();
            show_confirm_dialog('是否删除该收货地址？', function () {
                $.ajax({
                    type: 'POST',
                    url: "{:folder_url('Ajax/address_delete')}",
                    data: {address_id: address_id},
                    success: function (data) {
                        if (data.code !== 1) {
                            show_message(data.msg);
                            return;
                        }
                        show_message(data.msg);
                        view.parents('li').remove();
                    }
                });
            });
            return false;
        }).on('click', '.choose-address', function () {
            if (!parseInt('{$choose}')) {
                return false;
            }
            var view = $(this);
            var address_id = view.find('.address-id').val();
            window.location.href = encode_url(view.data('href'), {address_id: address_id});
            return false;
        });

        $('.s1').click(function () {
            window.location.href = $(this).parents('a').data('href');
        });

        $('.bottom-btns span').click(function (event) {
            $('.pup-box').fadeOut();
        });
    });
</script>
{/block}
