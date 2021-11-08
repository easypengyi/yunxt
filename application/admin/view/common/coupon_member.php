{extend name="public/base" /}
{block name="main-content"}
<?php isset($choose_all) OR $choose_all = false; ?>
<?php isset($order) OR $order = ['field' => '', 'dir' => false]; ?>
<?php isset($data_list) OR $data_list = []; ?>
<?php $choose = $choose_all; ?>

<div class="page-content">


    <div class="row">
        <div class="col-xs-12">
            <form class="ajax-form" method="post" action="">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered table-hover" data-href="{$full_url}" data-field="{$order.field}" data-dir="{$order.dir? 1 : 0}">
                        <thead>
                            <tr>
                                <th>会员昵称</th>
                                <th>会员手机号</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php foreach ($data_list as $v) : ?>
                                <tr>
                                    <td>{$v.member_nickname}</td>
                                    <td>{$v.member_tel}</td>
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
<script>
    // 关闭页面
    function close_frame() {
        parent.layer.closeAll();
    }
</script>
{/block}
