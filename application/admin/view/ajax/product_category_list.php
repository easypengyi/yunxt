<?php isset($data_list) OR $data_list = []; ?>
<?php isset($level) OR $level = 1; ?>
<?php isset($tag) OR $tag = ''; ?>

<?php foreach ($data_list as $v) : ?>
    <tr tag="{$tag}-{$v.category_id}">
<!--        <td>-->
<!--            <a data-pid="{$v.category_id}" data-level="{$v.level}" style="cursor:pointer;" class="rule-list">-->
<!--                <span class="fa fa-plus blue"></span>-->
<!--            </a>-->
<!--        </td>-->
        <td style='padding-left:{$v.leftpin}px'>
            {$v.lefthtml}{$v.name}
        </td>
        <td>{$v.sort}</td>
        <td>{$v.level}级</td>
        <td>
            <a class="red open-btn" href="{:folder_url('ProductCategory/change_enable')}" data-id="{$v.category_id}" data-status="{$v.enable}">
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
        <td>{:date('Y-m-d',$v.create_time)}</td>
        <td>
            <div class="hidden-sm hidden-xs action-buttons action-buttons-list">
                <a href="{:folder_url('ProductCategory/edit',['id'=>$v.category_id])}" title="修改">
                    <span class="green"><i class="ace-icon fa fa-pencil bigger-130"></i></span>
                </a>
                <a href="{:folder_url('ProductCategory/del',['id'=>$v.category_id])}" class="confirm-rst-url-btn" title="删除" data-info="你确定要删除吗？">
                    <span class="red"><i class="ace-icon fa fa-trash-o bigger-130"></i></span>
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
