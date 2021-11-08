{extend name="public/base" /}
{block name="main-content"}
<?php isset($edit) OR $edit = false; ?>
<?php isset($data_info) OR $data_info = []; ?>

<div class="page-content">
    <div class="row top20">
        <div class="col-xs-12">
            <form class="form-horizontal ajax-form" method="post" action="{$full_url}">
                <input type="hidden" name="return_url" value="{$return_url}"/>

                <div class="form-group">
                    <label class="col-sm-2 control-label no-padding-right" for="form-field-1"> 位置 </label>
                    <div class="col-sm-10 radio">
                        <?php isset($position) OR $position = []; ?>
                        <?php isset($data_info['position']) OR $data_info['position'] = key($position); ?>
                        <?php foreach ($position as $k => $v): ?>
                            <label>
                                <input name="position" <?php echo $data_info['position'] === $k ? 'checked' : '' ?> type="radio" value="{$k}" class="ace"/>
                                <span class="lbl"> {$v} </span>
                            </label>
                            &nbsp;&nbsp;
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="space-4"></div>

                <div class="form-group" id="type_div">
                    <label class="col-sm-2 control-label no-padding-right" for="type"> 类型 </label>
                    <div class="col-sm-10">
                        <select name="type" id="type" class="col-xs-10 col-sm-5 selectpicker" title="请选择" required>
                            <option value="">请选择</option>
                            <?php isset($type) OR $type = []; ?>
                            <?php isset($data_info['type']) OR $data_info['type'] = ''; ?>
                            <?php foreach ($type as $k => $v): ?>
                                <option value="<?php echo $k; ?>" <?php echo $data_info['type'] == $k ? 'selected' : ''; ?>><?php echo $v ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="space-4"></div>

                <div class="form-group">
                    <label class="col-sm-2 control-label no-padding-right" for="form-field-1"> 名称 </label>
                    <div class="col-sm-10">
                        <input type="text" name="name" id="name" value="{$data_info.name|default=''}" placeholder="名称" class="col-xs-10 col-sm-5"/>
                        <span class="lbl col-xs-12 col-sm-7"></span>
                    </div>
                </div>

                <div class="space-4"></div>

                <div class="form-group">
                    <label class="col-sm-2 control-label no-padding-right" for="form-field-1"> 排序 </label>
                    <div class="col-sm-10">
                        <input type="text" name="sort" id="sort" value="{$data_info.sort|default='0'}" placeholder="输入排序" class="col-xs-10 col-sm-5"/>
                        <span class="lbl col-xs-12 col-sm-7"><span class="red">*</span>从小到大排序</span>
                    </div>
                </div>

                <div class="space-4"></div>

                <div class="form-group">
                    <label class="col-sm-2 control-label no-padding-right" for="form-field-1"> 开启 </label>
                    <div class="col-sm-10" style="padding-top:5px;">
                        <input type="checkbox" name="enable" id="enable" <?php echo ($data_info['enable'] ?? false) ? 'checked' : ''; ?> value="1" placeholder="" class="ace ace-switch ace-switch-6"/>
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
<!--suppress JSUnusedLocalSymbols, JSCheckFunctionSignatures -->
{/block}
