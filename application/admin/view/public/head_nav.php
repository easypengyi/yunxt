<?php isset($head_nav) OR $head_nav = true; ?>
<?php isset($return_url) OR $return_url = ''; ?>

<?php if ($head_nav): ?>
    <div class="breadcrumbs ace-save-state">
        <ul class="breadcrumb">
            <li>
                <h4>
                    <i class="ace-icon fa fa-home home-icon"></i>
                    <b>{$page_title}</b>
                </h4>
            </li>
        </ul>

        <?php if (!empty($return_url)): ?>
            <div class="breadcrumb pull-right">
                <h4>
                    <a href="{$return_url}">
                        <i class="ace-icon fa fa-arrow-left bigger-110"></i> <b>返回</b>
                    </a>
                </h4>
            </div>
        <?php endif; ?>
    </div>
<?php endif ?>
