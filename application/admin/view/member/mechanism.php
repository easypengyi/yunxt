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
                            <th>机构名称</th>
                            <th>头像</th>
                            <th>姓名</th>
                            <th>职位</th>
                            <th>手机号</th>
                            <th>报单币</th>
                            <th>奖金币</th>
                            <th>启用</th>
                        </tr>
                        </thead>

                        <tbody>
                        <?php foreach ($data_list as $v) : ?>
                            <tr>
                                <td>{$v.mechanism_name}</td>
                                <td>
                                    <a href="{$v.member_headpic.full_url}" target="_blank"><img src="{$v.member_headpic.full_url}" width="50" height="50" alt=""/></a>
                                </td>
                                <td>{$v.member_realname}</td>
                                <td>{$v.group_name}</td>
                                <td>{$v.member_tel}</td>
                                <td>{$v.balance}</td>
                                <td>{$v.commission}</td>
                                <td>
                                    <a class="red open-btn" href="{:controller_url('change_enable')}" data-id="{$v.member_id}" data-status="{$v.enable}">
                                        <div>
                                            <button class="btn btn-minier btn-yellow none" title="已开启">
                                                开启
                                            </button>
                                            <button class="btn btn-minier btn-danger none" title="已禁用">
                                                禁用
                                            </button>
                                        </div>
                                    </a>
                                </td>

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
