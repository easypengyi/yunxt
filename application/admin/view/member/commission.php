{extend name="public/base" /}
{block name="main-content"}
<?php isset($order) OR $order = ['field' => '', 'dir' => false]; ?>
<?php isset($data_list) OR $data_list = []; ?>
<?php isset($sex) OR $sex = []; ?>

<div class="page-content">

    <div class="row">
        <div class="col-xs-12">
            <form class="ajax-form" method="post" action="">
                <div class="table-responsive">
                    <table  class="table table-striped table-bordered table-hover" data-href="{$full_url}" data-field="{$order.field}" data-dir="{$order.dir? 1 : 0}">
                        <thead>
                        <tr>
                            <th>金额</th>
<!--                            <th>类型</th>-->
                            <th>描述</th>
                            <th>时间</th>
                        </tr>
                        </thead>

                        <tbody>
                        <?php foreach ($data_list as $v) : ?>
                            <tr>
                                <td>{$v.value}</td>
                                <td>{$v.description}</td>
                                <td>{:date('Y-m-d H:i:s', $v.create_time)}</td>
                            </tr>
                        <?php endforeach; ?>
                            <tr>
                                <td colspan="100" align="left">{$page}</td>
                            </tr>
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
