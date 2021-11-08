{extend name="public/base" /}
{block name="before_scripts"}
{/block}
{block name="main-content"}

<div class="page-content">
    <h1 class="center col-xs-11">
        配置{$configure_desc}
    </h1>

    <div class="row top20">
        <div class="col-xs-12">
            <form class="form-horizontal ajax-form" method="post" action="{$full_url}">
                <input type="hidden" name="return_url" value="{$return_url}"/>

                <div class="form-group">
                    <label class="col-sm-2 control-label no-padding-right" for="form-field-1">  </label>
                    <div class="col-sm-10">
                        <div id="editor" class="col-xs-10 col-sm-9"></div>
                        <textarea id="configure_value" class="editor-content" placeholder="" hidden></textarea>
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
<script src="__STATIC__/wangEditor/dist-3.1.0/wangEditor.min.js"></script>
<script>
    $(function () {
        init_editor('editor', 'configure_value', '{$configure_value|default=0}');
    });
</script>
{/block}
