{extend name="public/base" /}
{block name="main-content"}
<?php isset($choose_all) OR $choose_all = false; ?>
<?php isset($order) OR $order = ['field' => '', 'dir' => false]; ?>
<?php isset($data_list) OR $data_list = []; ?>

<div class="page-content">
    <div class="row maintop">
        <div class="col-xs-12 col-sm-2 col-md-1 margintop5"></div>
        <?php if (isset($search) && $search): ?>
            <form class="form-search" method="get" action="{$current_url}">
                <div class="col-xs-12 col-sm-12 col-md-6 margintop5">
                </div>
                <div class="col-xs-12 col-sm-10 col-md-5 margintop5">
                    <div class="input-group">
                        <span class="input-group-addon"><i class="ace-icon fa fa-check"></i></span>
                        <input type="text" name="keyword" id="keyword" class="form-control" value="{$search.keyword}" placeholder="{$search.description}"/>

                        <a class="input-group-addon list-search" title="搜索">
                            <span class="ace-icon fa fa-search icon-on-right bigger-110"></span>
                        </a>
                        <a href="{$current_url}" class="input-group-addon" title="显示全部">
                            <span class="ace-icon fa fa-globe icon-on-right bigger-110"></span>
                        </a>
                        <?php if (isset($export) && $export): ?>
                            <a class="input-group-addon list-export" title="导出">
                                <span class="ace-icon fa fa-download icon-on-right bigger-110"></span>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </form>
        <?php endif; ?>
    </div>

    <div class="row">
        <div class="col-xs-12">
            <form class="ajax-form" method="post" action="">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered table-hover" data-href="{$full_url}" data-field="{$order.field}" data-dir="{$order.dir? 1 : 0}">
                        <thead>
                            <tr>
                                <th>内容</th>
                                <th>时间</th>
                                <th class="th-operate">操作</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php foreach ($data_list as $v) : ?>
                                <tr>
                                    <td>{$v.message}</td>
                                    <td>{:date('Y-m-d H:i:s', $v.show_time)}</td>
                                    <td>
                                        <?php if (!$v['readed']): ?>
                                        <a href="{:folder_url('Ajax/head_message_read',['message_id'=>$v.message_id])}" class="check" data-toggle="tooltip" title="已读">
                                            <span class="green"><i class="ace-icon fa fa-check bigger-130"></i></span>
                                        </a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            <tr>
                                <td colspan="14" align="right">{$page}</td>
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
<script>
    $(function () {
        $(".check").click(function () {
            var href = $(this).attr('href');
            $.post(href, {}, function (data) {
                if (data.code === 1) {
                    alert_success(data.msg, function () {
                        window.location.reload();
                    });
                } else {
                    alert_error(data.msg);
                }
            }, 'json');
            return false;
        })
    })
</script>
{/block}
