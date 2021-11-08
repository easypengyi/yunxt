{extend name="public/base" /}
{block name="main-content"}
<?php isset($edit) OR $edit = false; ?>
<?php isset($data_info) OR $data_info = []; ?>
<?php isset($pid) OR $pid = 0; ?>

<div class="page-content">
    <div class="row top20">
        <div class="col-xs-12">
            <form class="form-horizontal ajax-form" method="post" action="{$full_url}">
                <input type="hidden" name="return_url" value="{$return_url}"/>

                <div class="form-group">
                    <label class="col-sm-2 control-label no-padding-right" for="pid"> 父级权限 </label>
                    <div class="col-sm-10">
                        <select name="pid" id="pid" data-href="{:folder_url('Ajax/select_admin_rule_tree',['id'=>$pid])}" class="col-xs-10 col-sm-5 selectpicker select-ajax" title="请选择" required></select>
                    </div>
                </div>

                <div class="space-4"></div>

                <div class="form-group">
                    <label class="col-sm-2 control-label no-padding-right" for="form-field-1"> 导航标题 </label>
                    <div class="col-sm-10">
                        <input type="text" name="title" id="title" value="{$data_info.title|default=''}" placeholder="" class="col-xs-10 col-sm-5" required/>
                        <span class="lbl col-xs-12 col-sm-7"></span>
                    </div>
                </div>

                <div class="space-4"></div>

                <div id="name_div" class="<?php echo $pid == 0 ? 'none' : ''; ?>">
                    <div class="form-group">
                        <label class="col-sm-2 control-label no-padding-right" for="form-field-1"> 模块/控制器/方法 </label>
                        <div class="col-sm-10">
                            <input type="text" name="name" id="name" value="{$data_info.name|default=''}" placeholder="" class="col-xs-10 col-sm-5" required/>
                            <span class="lbl col-xs-12 col-sm-7"></span>
                        </div>
                    </div>

                    <div class="space-4"></div>
                </div>

                <div id="css_div" class="<?php echo $pid == 0 ? '' : 'none'; ?>">
                    <div class="form-group">
                        <label class="col-sm-2 control-label no-padding-right" for="form-field-1"> 样式名称 </label>
                        <div class="col-sm-10">
                            <input type="text" name="css" id="css" value="{$data_info.css|default=''}" placeholder="" class="col-xs-10 col-sm-5"/>
                            <span class="lbl col-xs-12 col-sm-7">填写font-awesome图标，只针对顶级栏目有效</span>
                        </div>
                    </div>

                    <div class="space-4"></div>
                </div>

                <div class="form-group">
                    <label class="col-sm-2 control-label no-padding-right" for="form-field-1"> 排序 </label>
                    <div class="col-sm-10">
                        <input type="text" name="sort" id="sort" value="{$data_info.sort|default='60'}" placeholder="" class="col-xs-10 col-sm-5" required/>
                        <span class="lbl col-xs-12 col-sm-7">从小到大排序</span>
                    </div>
                </div>

                <div class="space-4"></div>

                <div class="form-group">
                    <label class="col-sm-2 control-label no-padding-right" for="form-field-1"> 显示 </label>
                    <div class="col-sm-10" style="padding-top:5px;">
                        <input type="checkbox" name="display" id="display" <?php echo ($data_info['display'] ?? true) ? 'checked' : ''; ?> value="1" placeholder="" class="ace ace-switch ace-switch-6"/>
                        <span class="lbl"></span>
                    </div>
                </div>

                <div class="space-4"></div>

                <div class="form-group">
                    <label class="col-sm-2 control-label no-padding-right" for="form-field-1"> 权限不检测 </label>
                    <div class="col-sm-10" style="padding-top:5px;">
                        <input type="checkbox" name="notcheck" id="notcheck" <?php echo ($data_info['notcheck'] ?? false) ? 'checked' : ''; ?> value="1" placeholder="" class="ace ace-switch ace-switch-6"/>
                        <span class="lbl"></span>
                    </div>
                </div>

                <div class="space-4"></div>

                <div class="form-group">
                    <label class="col-sm-2 control-label no-padding-right" for="form-field-1"> 权限分配 </label>
                    <div class="col-sm-10" style="padding-top:5px;">
                        <input type="checkbox" name="unassign" id="unassign" value="1" <?php echo ($data_info['unassign'] ?? true) ? 'checked' : ''; ?> placeholder="" class="ace ace-switch ace-switch-6"/>
                        <span class="lbl"></span>
                    </div>
                </div>

                <div class="space-4"></div>

                <div class="form-group">
                    <label class="col-sm-2 control-label no-padding-right" for="form-field-1"> 开启 </label>
                    <div class="col-sm-10" style="padding-top:5px;">
                        <input type="checkbox" name="enable" id="enable" value="1" <?php echo ($data_info['enable'] ?? true) ? 'checked' : ''; ?> placeholder="" class="ace ace-switch ace-switch-6"/>
                        <span class="lbl"></span>
                    </div>
                </div>

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
        $('#pid').change(function () {
            var level = $(this).find(':selected').data('level');
            var css_div = $('#css_div');
            var name_div = $('#name_div');
            if (level) {
                if (!css_div.is(':hidden')) {
                    css_div.hide(400);
                }

                if (name_div.is(':hidden')) {
                    name_div.show(400);
                }
                $('#name').removeAttr('disabled');
            }
            else {
                if (css_div.is(':hidden')) {
                    css_div.show(400);
                }
                if (!name_div.is(':hidden')) {
                    name_div.hide(400);
                }
                $('#name').attr('disabled', 'disabled');
            }
        });
    });
</script>
{/block}
