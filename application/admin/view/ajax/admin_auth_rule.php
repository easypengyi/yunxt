<?php isset($data_list) OR $data_list = []; ?>
<?php isset($level) OR $level = 1; ?>
<?php isset($tag) OR $tag = ''; ?>

<?php foreach ($data_list as $v) : ?>
    <tr tag="{$tag}-{$v.id}">
        <td>
            <a data-pid="{$v.id}" data-level="{$v.level}" style="cursor:pointer;" class="rule-list">
                <span class="fa fa-plus blue"></span>
            </a>
        </td>
        <td style='padding-left:{$v.leftpin}px'>
            {$v.lefthtml}{$v.title}
        </td>
        <td>{$v.name}</td>
        <td>
            <a class="red open-btn" href="{:folder_url('AdminRule/change_notcheck')}" data-id="{$v.id}" data-status="{$v.notcheck}">
                <div>
                    <button class="btn btn-minier btn-yellow none" title="不检测">
                        不检测
                    </button>
                    <button class="btn btn-minier btn-danger none" title="检测">
                        检测
                    </button>
                </div>
            </a>
        </td>
        <td>
            <a class="red open-btn" href="{:folder_url('AdminRule/change_unassign')}" data-id="{$v.id}" data-status="{$v.unassign}">
                <div>
                    <button class="btn btn-minier btn-yellow none" title="分配">
                        分配
                    </button>
                    <button class="btn btn-minier btn-danger none" title="不分配">
                        不分配
                    </button>
                </div>
            </a>
        </td>
        <td>
            <a class="red open-btn" href="{:folder_url('AdminRule/change_display')}" data-id="{$v.id}" data-status="{$v.display}">
                <div>
                    <button class="btn btn-minier btn-yellow none" title="已显示">
                        显示
                    </button>
                    <button class="btn btn-minier btn-danger none" title="已隐藏">
                        隐藏
                    </button>
                </div>
            </a>
        </td>
        <td>{$v.level}级</td>
        <td>{$v.sort}</td>
        <td>
            <a class="red open-btn" href="{:folder_url('AdminRule/change_enable')}" data-id="{$v.id}" data-status="{$v.enable}">
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
                <a href="{:folder_url('AdminRule/add',['pid'=>$v.id])}" title="添加子类">
                    <span class="blue"><i class="ace-icon fa fa-plus-circle bigger-130"></i></span>
                </a>
                <a href="{:folder_url('AdminRule/edit',['id'=>$v.id])}" title="修改">
                    <span class="green"><i class="ace-icon fa fa-pencil bigger-130"></i></span>
                </a>
                <a href="{:folder_url('AdminRule/copy',['id'=>$v.id])}" title="复制">
                    <span class="green"><i class="ace-icon fa fa-exchange bigger-130"></i></span>
                </a>
                <a href="{:folder_url('AdminRule/del',['id'=>$v.id])}" class="confirm-rst-url-btn" title="删除" data-info="你确定要删除吗？">
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
