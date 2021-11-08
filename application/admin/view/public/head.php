<?php isset($title) OR $title = '后台管理'; ?>
<?php isset($emoji_open) OR $emoji_open = true; ?>

<!--suppress SqlNoDataSourceInspection -->
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"/>
<meta charset="utf-8"/>
<title>{$title}</title>

<meta name="description" content=""/>
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0"/>
<link rel="Bookmark" href="__ROOT__/favicon.ico?version={$file_version}"/>
<link rel="Shortcut Icon" href="__ROOT__/favicon.ico?version={$file_version}"/>

<!-- bootstrap & fontawesome必须的css -->
<link rel="stylesheet" href="__STATIC__/ace/css/bootstrap.min.css?version={$file_version}"/>
<link rel="stylesheet" href="__STATIC__/font-awesome/dist-4.7.0/css/font-awesome.min.css"/>
<!-- 此页插件css -->

<!-- ace的css -->
<link rel="stylesheet" href="__STATIC__/ace/css/ace.min.css?version={$file_version}" class="ace-main-stylesheet" id="main-ace-style"/>
<!-- IE版本小于9的ace的css -->
<!--[if lte IE 9]>
<link rel="stylesheet" href="__STATIC__/ace/css/ace-part2.min.css?version={$file_version}" class="ace-main-stylesheet"/>
<![endif]-->

<!--[if lte IE 9]>
<link rel="stylesheet" href="__STATIC__/ace/css/ace-ie.min.css?version={$file_version}"/>
<![endif]-->

<!-- emoji相关css -->
<link rel="stylesheet" href="__STATIC__/emoji/emoji.min.css?version={$file_version}"/>

<!-- 此页相关css -->
<link rel="stylesheet" href="__STATIC__/bootstrap-select/dist-1.12.4/css/bootstrap-select.min.css"/>
<link rel="stylesheet" href="__CSS__/admin_base.min.css?version={$file_version}"/>
<link rel="stylesheet" href="__MODULE_CSS__/index.min.css?version={$file_version}"/>

<!-- ace设置处理的js -->
<script src="__STATIC__/ace/js/ace-extra.min.js?version={$file_version}"></script>

<!-- HTML5shiv and Respond.js for IE8 to support HTML5 elements and media queries -->
<!--[if lte IE 8]>
<script src="__JS__/html5shiv.min.js?version={$file_version}"></script>
<script src="__JS__/respond.min.js?version={$file_version}"></script>
<![endif]-->

<!-- 引入基本的js -->
<!--[if !IE]> -->
<script src="__STATIC__/jquery/dist-2.2.4/jquery.min.js"></script>
<!-- <![endif]-->

<!-- 如果为IE,则引入jq1.12.4 -->
<!--[if IE]>
<script src="__STATIC__/jquery/dist-1.12.4/jquery.min.js"></script>
<![endif]-->

<!-- 如果为触屏,则引入jquery.mobile -->
<script type="text/javascript">
    if ('ontouchstart' in document.documentElement) document.write("<script src='__JS__/jquery.mobile.custom.min.js?version={$file_version}'>" + "<" + "/script>");
</script>

<script src="__JS__/bootstrap.min.js?version={$file_version}"></script>

<!--suppress JSUnusedLocalSymbols -->
<script type="text/javascript">
    window.EMOJI_OPEN = "{$emoji_open}";
    window.SITE_URL = '{:request()->domain()}';
    window.WANG_EDITOR = {
        update: undefined,
        file_data: {},
        article_id: {},
        load_url: '{:url("tool/WangEditor/load")}',
        save_url: '{:url("tool/WangEditor/save")}',
        upload_url: '{:url("tool/WangEditor/upload")}',
        uploadImgMaxSize: 0,
        uploadImgMaxLength: 0
    };
</script>
