{extend name="public/base" /}
{block name="main-content"}
<?php isset($data_info) OR $data_info = []; ?>

<div class="page-content">
    <div class="row top20">
        <div class="col-xs-12">
            <form class="form-horizontal ajax-form" method="post" action="{$full_url}">
                <input type="hidden" name="return_url" value="{$return_url}"/>

                <div class="form-group">
                    <label class="col-sm-2 control-label no-padding-right" for="form-field-1"> 用户组名 </label>
                    <div class="col-sm-10">
                        <input type="text" name="group_name" id="group_name" value="{$data_info.group_name|default=''}" placeholder="输入用户组名" class="col-xs-10 col-sm-5" readonly/>
                        <span class="lbl col-xs-12 col-sm-7"></span>
                    </div>
                </div>

                <div class="space-4"></div>

                <table class="access-table" width="90%" border="0" align="center" cellpadding="0" cellspacing="0">
                    <tr>
                        <td height="30" style="padding-left:10px;border-bottom:#CCCCCC solid 1px; line-height:25px; background-color:#F4F8FB">
                            <label class="pos-rel">
                                <input type="checkbox" class="ace ace-checkbox-2 check-all" value="全选"/>
                                <span class="lbl"> 权限全选</span>
                            </label>
                        </td>
                    </tr>
                </table>

                <div class="space-4"></div>

                <div class="clearfix form-actions">
                    <div class="col-md-offset-3 col-md-9">
                        <button class="btn btn-info" type="submit">
                            <i class="ace-icon fa fa-check bigger-110"></i> 保存
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
{/block}

{block name="scripts"}
<script>
    $(function () {
        var url = "{:folder_url('Ajax/admin_auth_group_access',['group_id'=>$data_info.group_id])}";
        $.get(url, function (html) {
            $('.access-table').append(html);
        });
    });
</script>
{/block}
