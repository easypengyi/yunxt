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
                    <label class="col-sm-2 control-label no-padding-right" for="form-field-1"> 会员 </label>
                    <div class="col-sm-10">
                        <div class="input-group col-xs-10 col-sm-5">
                            <span data-href="{:folder_url('Common/choose_member')}" data-param="" data-callback="choose_member" class="input-group-addon choose">选择会员</span>
                            <input type="text" name="member_nickname" id="member_nickname" value="" placeholder="" class="form-control" readonly/>
                            <input type="hidden" id="member_id" name="member_id" value=""/>
                            <span class="input-group-addon choose-clear" data-input="member_nickname,member_id">清空</span>
                        </div>
                    </div>
                </div>

                <div class="space-4"></div>

                <div class="form-group">
                    <label class="col-sm-2 control-label no-padding-right" for="form-field-1"> 评分 </label>
                    <div class="col-sm-10">
                        <input type="number" min="0" max="5" step="0.01" name="score" id="score" value="{$data_info.score|default=''}" placeholder="评分" class="col-xs-10 col-sm-5"/>
                        <span class="lbl col-xs-12 col-sm-7"><span class="red">*</span></span>
                    </div>
                </div>

                <div class="space-4"></div>

                <div class="form-group">
                    <label class="col-sm-2 control-label no-padding-right" for="form-field-1"> 内容 </label>
                    <div class="col-sm-10">
                        <textarea name="content" id="content" cols="20" rows="5" placeholder="" class="col-xs-10 col-sm-5">{$data_info.content|default=''}</textarea>
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
<script>
    // 会员选择回调
    function choose_member(member) {
        $('#member_id').val(member.member_id);
        $('#member_nickname').val(member.member_nickname);
    }
</script>
{/block}
