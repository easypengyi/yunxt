<?php isset($title) OR $title = ''; ?>
<?php isset($emoji_open) OR $emoji_open = true; ?>
<?php isset($geomap_ak) OR $geomap_ak = ''; ?>
<?php isset($longitude) OR $longitude = 0; ?>
<?php isset($latitude) OR $latitude = 0; ?>
<?php isset($address) OR $address = ''; ?>

<!DOCTYPE html>
<html lang="zh">

<head>
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

    <!-- 此页相关css -->
    <link rel="stylesheet" href="__CSS__/admin_base.min.css?version={$file_version}"/>

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
        window.SITE_URL = '{:request()->domain()}';
    </script>
</head>

<body class="no-skin">
<!-- 整个页面内容开始 -->
<div class="main-container" id="main-container">
    <!-- 主要内容开始 -->
    <div class="main-content">
        <div class="main-content-inner">
            <div class="page-content">
                <div style="width: 100%;height: 710px;position: relative">
                    <div id="allmap" style="font-family: '微软雅黑', serif;width: 100%;height: 100%"></div>
                </div>
            </div>
        </div>
    </div>
    <!-- 主要内容结束 -->

    <!-- 返回顶端开始 -->
    <a href="#" id="btn-scroll-up" class="btn-scroll-up btn btn-sm btn-inverse">
        <i class="ace-icon fa fa-angle-double-up icon-only bigger-110"></i>
    </a>
    <!-- 返回顶端结束 -->
</div>
<div id="dialog" style="padding:0;z-index: 999999999;"></div>

<script src="__JS__/jquery-maxlength.min.js?version={$file_version}"></script>
<!-- ace的js,可以通过打包生成,避免引入文件数多 -->
<script src="__STATIC__/ace/js/ace.min.js?version={$file_version}"></script>
<script src="__STATIC__/jquery-form/dist-4.2.2/jquery.form.min.js"></script>
<script src="__STATIC__/layer/dist-3.1.1/layer.js"></script>
<script src="http://webapi.amap.com/maps?v=1.3&key=<?php echo $geomap_ak; ?>"></script>
<!--suppress JSUnresolvedFunction, JSCheckFunctionSignatures, JSUnresolvedVariable -->
<script type="text/javascript">
    var longitude = '<?php echo $longitude; ?>';
    var latitude = '<?php echo $latitude; ?>';

    var map = new AMap.Map('allmap', {zoom: 14, center: [longitude, latitude]});

    set_info(longitude, latitude, '<?php echo $address; ?>');

    AMap.plugin(['AMap.ToolBar', 'AMap.Autocomplete'], function () {
        map.addControl(new AMap.ToolBar({position: 'RT'}));
    });

    // 设置经纬度
    function set_info(longitude, latitude, address) {
        var infoWindow = new AMap.InfoWindow();
        infoWindow.setContent(address);
        infoWindow.open(map, [longitude, latitude]);
    }
</script>
</body>
</html>
