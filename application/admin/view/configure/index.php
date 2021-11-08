{extend name="public/base" /}
{block name="main-content"}
<?php isset($order) OR $order = ['field' => '', 'dir' => false]; ?>
<?php isset($configure_list) OR $configure_list = []; ?>

<div class="page-content">
    <div class="row maintop">
        <div class="col-xs-4 col-sm-2 margintop5"></div>
    </div>

    <div class="row">
        <div class="col-xs-12">
            <form class="ajax-form" method="post" action="">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered table-hover" data-href="{$full_url}" data-field="{$order.field}" data-dir="{$order.dir? 1 : 0}">
                        <thead>
                            <tr>
                                <th>名称</th>
                                <th class="th-operate">操作</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php foreach ($configure_list as $k => $v) : ?>
                                <tr>
                                    <td>{$v}</td>
                                    <td>
                                        <div class="hidden-sm hidden-xs action-buttons action-buttons-list">
                                            <a href="{:controller_url('config',['name'=>$k])}" data-toggle="tooltip" title="配置">
                                                <span class="blue"><i class="ace-icon fa fa-cog bigger-130"></i></span>
                                            </a>
                                        </div>
                                        <div class="hidden-md hidden-lg">
                                            <div class="inline position-relative">
                                                <button class="btn btn-minier btn-primary dropdown-toggle" data-toggle="dropdown" data-position="auto">
                                                    <i class="ace-icon fa fa-cog icon-only bigger-110"></i>
                                                </button>
                                                <ul class="dropdown-menu dropdown-only-icon dropdown-yellow dropdown-menu-right dropdown-caret dropdown-close"></ul>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </form>
        </div>
    </div>
</div>
{/block}

{block name="scripts"}
{/block}
