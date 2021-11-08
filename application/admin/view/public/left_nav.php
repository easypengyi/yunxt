<?php isset($left_nav) OR $left_nav = true; ?>
<?php isset($menu_list_html) OR $menu_list_html = ''; ?>

<?php if ($left_nav): ?>
    <div id="sidebar" class="sidebar responsive sidebar-fixed ace-save-state">
        <script type="text/javascript">
            try {
                ace.settings.loadState('sidebar')
            } catch (e) {
            }
        </script>
        <!-- 菜单列表开始 -->
        <ul class="nav nav-list">
            {$menu_list_html}
        </ul>
        <!-- 菜单列表结束 -->
    </div>
<?php endif; ?>
