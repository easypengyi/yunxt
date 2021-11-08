{extend name="public/base" /}

{block name="main-content"}
<div class="mobile-wrap center">
    <div class="header" style="position:fixed;z-index:999;top:0;left:0;">
        <a href="{:folder_url('User/personal')}"><img src="__MODULE_IMG__/ic21.png" alt=""></a>
        <span class="save">保存</span>
    </div>
    <form class="ajax-form" action="{$full_url}" method="post" style="position: relative;top:0.71rem;left:0;">
        <div class="user-up">
            <div class="box">
                <div class="pic">
                    <img id="avarimg" src="{$member.member_headpic.full_url|default='__MODULE_IMG__/ic28.png'}" alt="">
                </div>
                <span>上传头像</span>
                <input type="file" name="file" id="file" accept="image/*">
            </div>
        </div>
        <div class="pers-inform">
            <div class="form">
                <div>
                    <label>姓名</label>
                    <p>
                        <em>{$member.member_realname|default=$member.member_nickname}</em>
                    </p>
                </div>

                <div>
                    <label>注册时间</label>
                    <p>
                        <em>{:date('Y-m-d', $member.create_time)}</em>
                    </p>
                </div>
                <div>
                    <label>手机</label>
                    <p>
                        <em>{$member.member_tel}</em>
                    </p>
                </div>
                <div>
                    <label>银行卡号：</label>
                    <input type="text" placeholder="请输入银行卡号" value="{$member.account}"  name="account"/>
                </div>
                <div>
                    <label>开户行：</label>
                    <input type="text" placeholder="请输入开户行/分行/支行"   value="{$member.blank}"  name="blank"/>
                </div>
                <div>
                    <label>持卡人：</label>
                    <input type="text" placeholder="请输入持卡人姓名"  value="{$member.real_name}"   name="real_name"/>
                </div>
            </div>
        </div>
    </form>
</div>
{/block}

{block name="hide-content"}
{/block}

{block name="scripts"}
<script src="__STATIC__/laydate/dist-5.0.9/laydate.js"></script>
<script>
    $(function () {
        laydate.render({elem: '#birthday'});

        $('#file').change(function () {
            var objUrl = get_file_url(this.files[0]);
            if (objUrl) {
                $('#avarimg').attr('src', objUrl);
            }
        });

        $('.pers-inform div h6 i').click(function () {
            $(this).parents('h6').find('i').removeClass('acti');
            $(this).addClass('acti');
        });

        $('.save').click(function () {
            $('.ajax-form').submit();
        })
    });
</script>
{/block}
