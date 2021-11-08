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
                </div>
            </div>
            <div class="statusBar" style="display:none;">
                <div class="progress">
                    <span class="text">0%</span>
                    <span class="percentage"></span>
                </div>
                <div class="info"></div>
                <div class="btns">
                    <div class="uploadBtn" style="display: none;">开始上传</div>
                    <div class="confirmBtn" style="display: none;background: #ffffff;border: 1px solid #cfcfcf;color: #565656;padding: 0 18px;border-radius: 3px; margin-left: 10px;cursor: pointer;font-size: 14px;float: left;">确认完成
                    </div>
                </div>
            </div>
            <div id="log" style="padding: 0 20px;font-size: 14px;color: #666;">
            </div>
        </div>
    </div>
</div>

<script src="__STATIC__/layer/dist-3.1.1/layer.js"></script>
<script src="__STATIC__/webuploader/dist-0.15/webuploader.nolog.min.js"></script>
<script src="__MODULE_JS__/video_upload.min.js?version={$file_version}"></script>
<!--suppress JSUnusedLocalSymbols -->
<script>
    var file_md5 = '';   // 用于MD5校验文件
    var block_info = [];   // 用于跳过已有上传分片

    $(function () {
        Manager.customConfig.uploadSuccess = function (file, data) {
            $('.uploadBtn').hide();
            if (data.code === 1) {
                if (data.data['trunk']) {
                    log("上传分片完成。");
                    log("正在整理分片...");
                    $.post("{:controller_url('file_merge')}", {
                        file_md5: file.wholeMd5,
                        file_ext: file.ext
                    }, function (data) {
                        if (data.code === 1) {
                            window.parent['{$callback}'](data.data);
                            $('.confirmBtn').show();
                            log("上传成功");
                        }
                    });
                } else {
                    window.parent['{$callback}'](data.data.data);
                    $('.confirmBtn').show();
                    log("上传成功");
                }
            } else {
                layer.alert(data.msg, function (index) {
                    layer.close(index);
                });
            }
        };
        Manager.customConfig.fileQueued = function (file) {
            log("正在计算MD5值...");
            var index = layer.load();
            uploader.md5File(file).then(function (fileMd5) {
                file.wholeMd5 = fileMd5;
                file_md5 = fileMd5;
                log("MD5计算完成。");
                layer.close(index);

                // 检查是否有已经上传成功的分片文件
                $.post("{:controller_url('file_check')}", {file_md5: file_md5, file_ext: file.ext}, function (data) {
                    var uploadBtn = $('.uploadBtn');
                    if (data.data.status) {
                        window.parent['{$callback}'](data.data.data);
                        uploadBtn.hide();
                        $('.confirmBtn').show();
                        log("上传成功");
                        return false;
                    }
                    // 如果有对应的分片，推入数组
                    if (data.data.data) {
                        for (var i in data.data.data) {
                            if (data.data.data.hasOwnProperty(i)) {
                                block_info.push(data.data.data[i]);
                            }
                        }
                        log("有断点...");
                    }
                    uploadBtn.show();
                });
            });
        };

        // 发送前检查分块,并附加MD5数据
        Manager.customConfig.uploadBeforeSend = function (block, data) {
            var file = block.file;
            var deferred = WebUploader.Deferred();

            data.md5value = file.wholeMd5;
            data.status = file.status;

            if ($.inArray(block.chunk.toString(), block_info) >= 0) {
                log("已有分片.正在跳过分片" + block.chunk.toString());
                deferred.reject();
                deferred.resolve();
                return deferred.promise();
            }
        };

        Manager.customConfig.uploadError = function (file, data) {
            uploader.retry();
        };
        Manager.customConfig.customAlert = function (msg) {
        };
        Manager.init({
            'auto': false,
            'server': "{:controller_url('file_upload',['_ajax'=>true, 'upload_type'=>$upload_type])}",
            'swf': '__STATIC__/webuploader/dist-0.15/Uploader.swf'
        });

        $('.confirmBtn').click(function () {
            var index = parent.layer.getFrameIndex(window.name);
            parent.layer.close(index);
        })
    });

    function log(html) {
        $("#log").append("<div>" + html + "</div>");
    }
</script>
</body>
</html>
