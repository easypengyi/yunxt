{extend name="public/base" /}
{block name="main-content"}
<?php isset($order) OR $order = ['field' => '', 'dir' => false]; ?>
<?php isset($data_list) OR $data_list = []; ?>

<div class="page-content">
    <div class="row maintop">


    </div>


    <div class="row">
        <div class="col-xs-12">
            <form class="ajax-form" method="post" action="">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered table-hover" data-href="{$full_url}" data-field="{$order.field}" data-dir="{$order.dir? 1 : 0}">
                        <thead>
                        <tr>
                            <th>名称</th>
                            <th>奖金池</th>
                            <th class="th-operate">操作</th>
                        </tr>
                        </thead>

                        <tbody>
                        <?php foreach ($data_list as $v) : ?>
                            <tr>
                                <td>{$v.name}</td>
                                <td>{$v.configure_value}</td>
                                <td>
                                    <div class="hidden-sm hidden-xs action-buttons action-buttons-list">
                                        <a href="{:controller_url('recharge',['name'=>$v.configure_name])}" data-toggle="tooltip" title="增加">
                                            <span class="red"><i class="ace-icon fa fa-plus-square bigger-130"></i></span>
                                        </a>
                                        <a href="{:controller_url('reduce',['name'=>$v.configure_name])}" data-toggle="tooltip" title="缩减">
                                            <span class="red"><i class="ace-icon fa fa-minus-square bigger-130"></i></span>
                                        </a>
                                        <a href="{:controller_url('stock',['name'=>$v.configure_name])}" data-toggle="tooltip" title="明细">
                                            <span class="dark"><i class="ace-icon fa fa-bars bigger-130"></i></span>
                                        </a>

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
