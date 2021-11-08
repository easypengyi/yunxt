{extend name="public/base" /}
{block name="before_scripts"}
{/block}
{block name="main-content"}
<?php isset($edit) OR $edit = false; ?>
<?php isset($data_info) OR $data_info = []; ?>

<div class="page-content">
    <div class="row top20">
        <div class="col-xs-12">
            <form class="form-horizontal ajax-form" method="post" action="{$full_url}">
                <input type="hidden" name="return_url" value="{$return_url}"/>

                <div class="form-group">
                    <label class="col-sm-2 control-label no-padding-right" for="form-field-1"> 全部会员 </label>
                    <div class="col-sm-10" style="padding-top:5px;">
                        <input type="checkbox" name="member_all" id="member_all" value="1" placeholder="" class="ace ace-switch ace-switch-6" checked/>
                        <span class="lbl"></span>
                    </div>
                </div>

                <div class="space-4"></div>

                <div id="member_div" class="none">
                    <div class="form-group">
                        <label class="col-sm-2 control-label no-padding-right" for="form-field-1"> 赠送会员 </label>
                        <div class="col-sm-10">
                            <div class="col-xs-10 col-sm-5">
                                <table class="table table-striped table-bordered table-hover">
                                    <thead>
                                        <tr>
                                            <th class="center">
                                            <span data-param="member_group,member_id[]" data-callback="member_choose" data-href="{:folder_url('Common/choose_member',['choose_all'=>true])}" class="purple new-row choose" title="添加限制商品">
                                                <i class="ace-icon fa fa-plus-circle bigger-130"></i>
                                            </span>
                                            </th>
                                            <th>会员昵称</th>
                                            <th>手机号码</th>
                                            <th class="th-operate">操作</th>
                                        </tr>
                                    </thead>
                                    <tbody id="fields-data">
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="space-4"></div>
                </div>

                <div class="clearfix form-actions">
                    <div class="col-md-offset-3 col-md-9">
                        <button class="btn btn-info" type="submit">
                            <i class="ace-icon fa fa-check bigger-110"></i> 赠送
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
{/block}

{block name="hide-content"}
<table id="data_table">
    <tr>
        <td><input type="hidden" name="member_id[]" value=""/></td>
        <td class="member_nickname"></td>
        <td class="member_telephone"></td>
        <td class="th-operate">
            <div class="action-buttons">
                <a class="red field-delete" data-toggle="tooltip" title="删除">
                    <i class="ace-icon fa fa-trash-o bigger-130"></i>
                </a>
            </div>
        </td>
    </tr>
</table>
{/block}

{block name="scripts"}
<!--suppress JSUnusedLocalSymbols -->
<script>
    $(function () {
        $('#member_all').change(function () {
            var member_div = $('#member_div');
            if ($(this).is(':checked')) {
                if (!member_div.is(':hidden')) {
                    member_div.hide(400);
                }
            }
            else {
                if (member_div.is(':hidden')) {
                    member_div.show(400);
                }
            }
        });

        $('body').on('click', '.field-delete', function () {
            var index = $('#fields-data tr').index($(this).closest('tr'));
            $('#fields-data').find('tr').eq(index).remove();
        });
    });

    // 会员选择回调
    function member_choose(member) {
        var data_table = $('#data_table');
        var html = data_table.find('tr').eq(0).clone();
        html.find('.member_nickname').html(member['member_nickname']);
        html.find('.member_telephone').html(member['member_tel']);
        html.find('[name="member_id[]"]').val(member['member_id']);
        $("#fields-data").append(html);
    }
</script>
{/block}
