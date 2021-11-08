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

                <?php $field_name = 'image'; ?>
                <?php $thumb = $configure_value ?? []; ?>
                <?php $field_desc = $configure_desc ?? ''; ?>
                {include file="piece/thumb_upload_piece" field_desc="" image_size=""/}

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
