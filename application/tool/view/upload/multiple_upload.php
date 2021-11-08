<?php isset($callback) OR $callback = ''; ?>

<!DOCTYPE html>
<html lang="zh">

<head>
    <!--suppress SqlNoDataSourceInspection -->
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"/>
    <meta charset="utf-8"/>
    <title>文件上传</title>

    <meta name="description" content=""/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0"/>
    <link rel="Bookmark" href="__ROOT__/favicon.ico?version={$file_version}"/>
    <link rel="Shortcut Icon" href="__ROOT__/favicon.ico?version={$file_version}"/>

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

    <link rel="stylesheet" type="text/css" href="__STATIC__/webuploader/dist-0.15/webuploader.min.css">
    <link rel="stylesheet" type="text/css" href="__MODULE_CSS__/multiple_upload.min.css?version={$file_version}">
</head>

<body>

<div class="upload-box">
    <div class="container">
        <div id="uploader">
            <div class="queueList">
                <div id="dndArea" class="placeholder">
                    <div id="filePicker"></div>
                    <p>或将照片拖到这里，单次最多可选300张</p>
                </div>
            </div>
            <div class="statusBar" style="display:none;">
                <div class="progress">
                    <span class="text">0%</span>
                    <span class="percentage"></span>
                </div><div class="info"></div>
                <div class="btns">
                    <div id="filePicker2"></div><div class="uploadBtn" style="display: none;">开始上传</div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="__STATIC__/layer/dist-3.1.1/layer.js"></script>
<script src="__STATIC__/webuploader/dist-0.15/webuploader.nolog.min.js"></script>
<script src="__MODULE_JS__/multiple_upload.min.js?version={$file_version}"></script>
<script>
    $(function () {
        Manager.customConfig.uploadSuccess = function (file, data) {
            if (data.code === 1) {
                window.parent['{$callback}'](data.data);
            }
        };
        Manager.customConfig.uploadError = function (file, data) {
        };
        Manager.customConfig.customAlert = function (msg) {
            layer.alert(msg, function (index) {
                layer.close(index);
            });
        };
        Manager.init({
            'auto': true,
            'server': "{:controller_url('file_upload',['_ajax'=>true])}",
            'swf': '__STATIC__/webuploader/dist-0.15/Uploader.swf'
        });
    })
</script>
</body>
</html>
