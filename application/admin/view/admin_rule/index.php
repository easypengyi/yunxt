{extend name="public/base" /}
{block name="main-content"}
<?php isset($data_html) OR $data_html = ''; ?>

<div class="page-content">
    <div class="row maintop">
        <div class="col-xs-12 col-sm-2 col-md-1 margintop5">
            <a href="{:controller_url('add')}" class="btn btn-sm btn-danger">添加</a>
        </div>
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
                    <table class="table table-striped table-bordered table-hover">
                        <thead>
                            <tr>
                                <th></th>
                                <th>权限名称</th>
                                <th>模块/控制器/方法</th>
                                <th>不检测</th>
                                <th>不分配</th>
                                <th>显示</th>
                                <th>级别</th>
                                <th>排序</th>
                                <th>启用</th>
                                <th>添加时间</th>
                                <th class="th-operate">操作</th>
                            </tr>
                        </thead>

                        <tbody>
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
        $('body').on('click', '.rule-list', function () {
            var view = $(this);
            var pid = view.data('pid');
            var level = view.data('level');
            var tr = view.parents('tr');
            var tag = tr.attr('tag');
            var child_tr = $('tr[tag^=' + tag + '-]');

            if (view.find('span').hasClass('fa-minus')) {
                child_tr.addClass('none');
                view.find('span').removeClass('fa-minus').addClass('fa-plus');
            } else {
                if (child_tr.length > 0) {
                    child_tr.removeClass('none');
                    view.find('span').removeClass('fa-plus').addClass('fa-minus');
                } else {
                    load_data(pid, level, tag, function (data) {
                        if (data) {
                            tr.after(data);
                            $('body').change();
                        }
                        view.find('span').removeClass('fa-plus').addClass('fa-minus');
                    });
                }
            }
            return false;
        });
        load_data(0, 0, 'pid', function (html) {
            $('tbody').prepend(html);
            $('body').change();
        });
    });

    function load_data(pid, level, tag, callback) {
        var url = '{:folder_url("Ajax/admin_auth_rule")}';
        $.post(url, {tag: tag, level: level, pid: pid}, function (data) {
            callback(data);
        }, 'json');
    }
</script>
{/block}
