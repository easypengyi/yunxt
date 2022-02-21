{extend name="public/base" /}
{block name="main-content"}
<?php isset($order) OR $order = ['field' => '', 'dir' => false]; ?>
<?php isset($data_list) OR $data_list = []; ?>
<?php isset($sex) OR $sex = []; ?>

<div class="page-content">
    <div class="row maintop">
        <?php if (isset($search) && $search): ?>
            <form class="form-search" method="get" action="{$current_url}">
                <div class="col-xs-12 col-sm-12 col-md-6 margintop5">
                    <div class="col-xs-12 col-sm-12 col-md-9">
                        <div class="input-group">
                            <span class="input-group-addon"><i class="ace-icon fa fa-list-alt"></i></span>
                            <input type="text" name="date" id="date" value="{$search.date}" placeholder="时间" class="form-control"/>
                            <a class="input-group-addon list-search" title="搜索">
                                <span class="ace-icon fa fa-search icon-on-right bigger-110"></span>
                            </a>
                            <input type="hidden" name="id" value="{$member_id}" />
                            <?php if (isset($export) && $export): ?>
                                <a class="input-group-addon list-export" title="导出">
                                    <span class="ace-icon fa fa-download icon-on-right bigger-110"></span>
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>

                </div>
            </form>
        <?php endif; ?>
    </div>

    <div class="row">
        <div class="col-xs-12">
            <form class="ajax-form" method="post" action="">
                <div class="table-responsive">
                    <table  class="table table-striped table-bordered table-hover" data-href="{$full_url}" data-field="{$order.field}" data-dir="{$order.dir? 1 : 0}">
                        <thead>
                        <tr>
                            <th>金额</th>
<!--                            <th>类型</th>-->
                            <th>用户名</th>
                            <th>描述</th>
                            <th>时间</th>
                        </tr>
                        </thead>

                        <tbody>
                        <?php foreach ($data_list as $v) : ?>
                            <tr>
                                <td>+{$v.value}</td>
<!--                                <td>{$v.type_name}</td>-->
                                <td>{$v['member']['member_realname']}</td>
                                <td>({$v.group_name}){$v.description}</td>
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
<script src="__STATIC__/laydate/dist-5.0.9/laydate.js"></script>
<script>
    $(function () {
        laydate.render({
            elem: '#date',
            range: true
        });

        $('.selectpicker').selectpicker({
            width: 120
        });
    });
</script>
{/block}
