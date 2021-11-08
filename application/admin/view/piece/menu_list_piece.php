<?php isset($data_info) OR $data_info = []; ?>
<?php isset($check_id) OR $check_id = 0; ?>
<?php isset($child_html) OR $child_html = ''; ?>

<li class="<?php echo ($check_id == $data_info['id']) ? (empty($child_html) ? 'active' : 'open') : ''; ?>">
    <?php if (empty($child_html)): ?>
        <a href="{:url($data_info.name)}">
            <i class="menu-icon fa fa-caret-right"></i>
            {$data_info.title}
        </a>
        <b class="arrow"></b>
    <?php else: ?>
        <a href="javascript:void(0);" class="dropdown-toggle">
            <i class="menu-icon fa <?php echo empty($level) ? $data_info['css'] : 'fa-caret-right'; ?>"></i>
            <span class="menu-text">{$data_info.title}</span>
            <b class="arrow fa fa-angle-down"></b>
        </a>
        <ul class="submenu">
            {$child_html}
        </ul>
    <?php endif; ?>
</li>
