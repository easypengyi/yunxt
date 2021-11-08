{extend name="public/base" /}
{block name="main-content"}

<div class="page-content">
    <div class="page-header">
        <h1>
            您当前操作
            <small>
                <i class="ace-icon fa fa-angle-double-right"></i> 配置{$configure_desc}
            </small>
        </h1>
    </div>

    <div class="row">
        <div class="col-xs-12">
            <form class="form-horizontal ajax-form" method="post" action="{$full_url}">
                <input type="hidden" name="return_url" value="{$return_url}"/>
 
                <div class="form-group">
                    <label class="col-sm-2 control-label no-padding-right" for="form-field-1">
                        {$configure_desc}
                    </label>
                    <div class="col-sm-10">
                        <input type="text" name="configure_value" id="configure_value" value="{$configure_value}" placeholder="" class="col-xs-10 col-sm-5"/>
                        <span class="lbl col-xs-12 col-sm-7"></span>
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
{/block}
