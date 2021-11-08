<?php isset($navigation_bar) OR $navigation_bar = true; ?>

<?php if ($navigation_bar): ?>
    <!-- 导航栏开始 -->
    <div id="navbar" class="navbar navbar-default navbar-fixed-top">
        <div class="navbar-container" id="navbar-container">
            <!-- 导航左侧按钮手机样式开始 -->
            <button type="button" class="navbar-toggle menu-toggler pull-left" id="menu-toggler" data-target="#sidebar">
                <span class="sr-only">Toggle sidebar</span>

                <span class="icon-bar"></span>

                <span class="icon-bar"></span>

                <span class="icon-bar"></span>
            </button>
            <!-- 导航左侧按钮手机样式结束 -->
            <button data-target="#sidebar2" data-toggle="collapse" type="button" class="pull-left navbar-toggle collapsed">
                <span class="sr-only">Toggle sidebar</span>
                <i class="ace-icon fa fa-dashboard white bigger-125"></i>
            </button>

            <!-- 导航左侧正常样式结束 -->

            <!-- 导航栏开始 -->
            <div class="navbar-buttons navbar-header pull-right" role="navigation">
                <ul class="nav ace-nav">
                    <li class="green">
                        <a href="{:folder_url()}">
                            后台首页
                        </a>
                    </li>
                    <li class="grey dropdown-modal" id="message_head"></li>
                    <!-- 用户菜单开始 -->
                    <li class="light-blue dropdown-modal">
                        <a data-toggle="dropdown" href="#" class="dropdown-toggle">
                            <img class="nav-user-photo current-headpic" src="https://ydn-product.oss-cn-hangzhou.aliyuncs.com/upload/image/admin_img.jpg" alt="{$user.admin_username}"/>
                            <span class="user-info">
                                <small>欢迎,</small>{$user.admin_username}
                            </span>

                            <i class="ace-icon fa fa-caret-down"></i>
                        </a>

                        <ul class="user-menu dropdown-menu-right dropdown-menu dropdown-yellow dropdown-caret dropdown-close">
                            <li>
                                <a href="{:folder_url('Admin/profile')}">
                                    <i class="ace-icon fa fa-user"></i>
                                    个人中心
                                </a>
                            </li>

                            <li class="divider"></li>

                            <li>
                                <a href="{:folder_url('Login/logout')}" data-info="你确定要退出吗？" class="confirm-btn">
                                    <i class="ace-icon fa fa-power-off"></i>
                                    注销
                                </a>
                            </li>
                        </ul>
                    </li>
                    <!-- 用户菜单结束 -->
                </ul>
            </div>
            <!-- 导航栏结束 -->
        </div>
        <!-- 导航栏容器结束 -->
    </div>
    <!-- 导航栏结束 -->
    <script>
        $(function () {
            if ($('#message_head').length !== 0) {
                message_load();
            }
        });

        // 消息读取
        function message_load() {
            var url = "{:folder_url('Ajax/message_head_list', ['pagesize'=>5])}";
            $.post(url, {}, function (data) {
                $('#message_head').html(data);
            }, 'json');
        }
    </script>
<?php endif; ?>
