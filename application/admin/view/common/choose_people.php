{extend name="public/base" /}
{block name="main-content"}
<?php isset($choose_all) OR $choose_all = false; ?>
<?php isset($order) OR $order = ['field' => '', 'dir' => false]; ?>
<?php isset($data_list) OR $data_list = []; ?>
<?php $choose = $choose_all; ?>

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
                                <?php if ($choose): ?>
                                    <th class="hidden-xs center">
                                        <label class="pos-rel">
                                            <input type="checkbox" class="ace check-all" value="全选"/>
                                            <span class="lbl"></span>
                                        </label>
                                    </th>
                                <?php endif; ?>
                                <th>姓名</th>
                                <th>称号</th>
                                <th class="th-operate">操作</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php foreach ($data_list as $v) : ?>
                                <tr>
                                    <?php if ($choose): ?>
                                        <td class="hidden-xs" align="center">
                                            <label class="pos-rel">
                                                <input name='id[]' class="ace" type='checkbox' value='{$v.id}'/>
                                                <span class="lbl"></span>
                                            </label>
                                        </td>
                                    <?php endif; ?>
                                    <td>{$v.name}</td>
                                    <td>{$v.title}</td>
                                    <td>
                                        <div class="hidden-sm hidden-xs action-buttons action-buttons-list">
                                            <a href="javascript:" class="btn-choose" value="{:base64_encode(json_encode($v))}" data-toggle="tooltip" title="选择">
                                                <span class="green"><i class="ace-icon fa fa-check bigger-130"></i></span>
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
                            <tr>
                                <?php if ($choose): ?>
                                    <td align="left" class="hidden-xs">
                                        <?php if ($choose_all): ?>
                                            <a class="btn btn-white btn-yellow btn-sm hidden-xs choose-all">选</a>
                                        <?php endif; ?>
                                    </td>
                                <?php endif; ?>
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
        $('.btn-choose').click(function () {
            set_data($(this).attr('value'));

            close_frame();
            return false;
        });
        $('.choose-all').click(function () {
            if ($('input[name="id[]"]:checked').length === 0) {
                alert_error('请选择！');
                return;
            }

            $('tr').each(function () {
                var view = $(this);
                if (view.find('input[name="id[]"]:checked').length === 0) {
                    return;
                }
                set_data(view.find('.btn-choose').attr('value'));
            });

            close_frame();
            return false;
        });
    });

    // 数据设置
    function set_data(value) {
        var dataObj = $.parseJSON(window.atob(value));
        window.parent['{$callback}'](dataObj);
    }

    // 关闭页面
    function close_frame() {
        parent.layer.closeAll();
    }
</script>
{/block}
