<?php isset($album) OR $album = false; ?>
<?php isset($cameras) OR $cameras = false; ?>

<!DOCTYPE html>
<html lang="zh">
<head>
    <meta charset=utf-8/>
    <meta name="renderer" content="webkit"/>
    <!--suppress SqlNoDataSourceInspection -->
    <meta http-equiv="X-UA-Compatible" content="IE=8,9,10">
    <title>文件上传</title>

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

    <!--CSS文件，有点废话-->
    <link rel="stylesheet" type="text/css" href="__STATIC__/shearphoto/css/ShearPhoto.min.css?version={$file_version}"/>
    <!--ShearPhoto的核心文件 截取，拖拽，HTML5切图，数据交互等都是由这个文件处理-->
    <script src="__STATIC__/shearphoto/js/ShearPhoto.min.js?version={$file_version}"></script>
    <!--在线拍照那个FLASH的接口，非技术性文件-->
    <script src="__STATIC__/shearphoto/js/webcam_ShearPhoto.min.js?version={$file_version}"></script>
    <!--图片特效处理,他只负责特效，其他功能与这个文件完全无关，这个JS从腾讯AI文件  如你不要特效的话，顺带删除这个文件，在hendle.js设置关闭特效-->
    <script src="__STATIC__/shearphoto/js/alloyimage.min.js?version={$file_version}"></script>
    <!--设置和处理对象方法的JS文件，要修改设置，请进入这个文件-->
    <script src="__STATIC__/shearphoto/js/handle.min.js?version={$file_version}"></script>
    <!--suppress JSUnusedLocalSymbols -->
    <script type="text/javascript">
        var SHEARPHOTO = {
            PATH_RES: '__STATIC__',
            preview: false,
            album: '{$album}',
            cameras: '{$cameras}',
            file_upload: '{:folder_url("Shearphoto/upload")}',
            url: '{:folder_url("Shearphoto/image_submit")}',
            callback: function (data) {
                window.parent['{$callback}'](data['url'], data['full_url'], data['file_id']);
                parent.layer.closeAll();
            }
        };
    </script>
</head>
<body>
<div>
    <!--主功能部份 主功能部份的标签请勿随意删除，除非你对shearphoto的原理了如指掌，否则JS找不到DOM对象，会给你抱出错误-->
    <div class="shearphoto_loading">程序加载中......</div>
    <!--这是2.2版本加入的缓冲效果，JS方法加载前显示的等待效果-->
    <div class="shearphoto_main">
        <!--效果开始.............如果你不要特效，可以直接删了........-->
        <div class="Effects" id="shearphoto_Effects" autocomplete="off">
            <strong class="EffectsStrong">截图效果</strong>
            <a href="javascript:void(0);" StrEvent="原图" class="Aclick"><img src="__STATIC__/shearphoto/images/Effects/e0.jpg" alt=""/>原图</a>
            <a href="javascript:void(0);" StrEvent="美肤"><img src="__STATIC__/shearphoto/images/Effects/e1.jpg" alt=""/>美肤效果</a>
            <a href="javascript:void(0);" StrEvent="素描"><img src="__STATIC__/shearphoto/images/Effects/e2.jpg" alt=""/>素描效果</a>
            <a href="javascript:void(0);" StrEvent="自然增强"><img src="__STATIC__/shearphoto/images/Effects/e3.jpg" alt=""/>自然增强</a>
            <a href="javascript:void(0);" StrEvent="紫调"><img src="__STATIC__/shearphoto/images/Effects/e4.jpg" alt=""/>紫调效果</a>
            <a href="javascript:void(0);" StrEvent="柔焦"><img src="__STATIC__/shearphoto/images/Effects/e5.jpg" alt=""/>柔焦效果</a>
            <a href="javascript:void(0);" StrEvent="复古"><img src="__STATIC__/shearphoto/images/Effects/e6.jpg" alt=""/>复古效果</a>
            <a href="javascript:void(0);" StrEvent="黑白"><img src="__STATIC__/shearphoto/images/Effects/e7.jpg" alt=""/>黑白效果</a>
            <a href="javascript:void(0);" StrEvent="仿lomo"><img src="__STATIC__/shearphoto/images/Effects/e8.jpg" alt=""/>仿lomo</a>
            <a href="javascript:void(0);" StrEvent="亮白增强"><img src="__STATIC__/shearphoto/images/Effects/e9.jpg" alt=""/>亮白增强</a>
            <a href="javascript:void(0);" StrEvent="灰白"><img src="__STATIC__/shearphoto/images/Effects/e10.jpg" alt=""/>灰白效果</a>
            <a href="javascript:void(0);" StrEvent="灰色"><img src="__STATIC__/shearphoto/images/Effects/e11.jpg" alt=""/>灰色效果</a>
            <a href="javascript:void(0);" StrEvent="暖秋"><img src="__STATIC__/shearphoto/images/Effects/e12.jpg" alt=""/>暖秋效果</a>
            <a href="javascript:void(0);" StrEvent="木雕"><img src="__STATIC__/shearphoto/images/Effects/e13.jpg" alt=""/>木雕效果</a>
            <a href="javascript:void(0);" StrEvent="粗糙"><img src="__STATIC__/shearphoto/images/Effects/e14.jpg" alt=""/>粗糙效果</a>
        </div>
        <!--效果结束...........................如果你不要特效，可以直接删了.....................................................-->
        <!--primary范围开始-->
        <div class="primary">
            <!--main范围开始-->
            <div id="main">
                <div class="point">
                </div>
                <!--选择加载图片方式开始-->
                <div id="SelectBox">
                    <form id="ShearPhotoForm" enctype="multipart/form-data" method="post" target="POSTiframe">
                        <input name="shearphoto" type="hidden" value="我要传参数" autocomplete="off"/>
                        <!--示例传参数到服务端，后端文件UPLOAD.php用$_POST['shearphoto']接收,注意：HTML5切图时，这个参数是不会传的-->
                        <a href="javascript:void(0);" id="selectImage">
                            <input type="file" name="UpFile" autocomplete="off"/>
                        </a>
                        <?php if ($album): ?>
                            <a href="javascript:void(0);" id="PhotoLoading"></a>
                        <?php endif; ?>
                        <?php if ($cameras): ?>
                            <a href="javascript:void(0);" id="camerasImage"></a>
                        <?php endif; ?>
                    </form>
                </div>
                <!--选择加载图片方式结束--->
                <div id="relat">
                    <div id="black">
                    </div>
                    <div id="movebox">
                        <div id="smallbox">
                            <img src="__STATIC__/shearphoto/images/default.gif" class="MoveImg" alt=""/>
                            <!--截框上的小图-->
                        </div>
                        <!--动态边框开始-->
                        <i id="borderTop"></i>

                        <i id="borderLeft"></i>

                        <i id="borderRight"></i>

                        <i id="borderBottom"></i>
                        <!--动态边框结束-->
                        <i id="BottomRight"></i>
                        <i id="TopRight"></i>
                        <i id="Bottomleft"></i>
                        <i id="Topleft"></i>
                        <i id="Topmiddle"></i>
                        <i id="leftmiddle"></i>
                        <i id="Rightmiddle"></i>
                        <i id="Bottommiddle"></i>
                    </div>
                    <img src="__STATIC__/shearphoto/images/default.gif" class="BigImg" alt=""/>
                    <!--MAIN上的大图-->
                </div>
            </div>
            <!--main范围结束-->
            <div style="clear: both"></div>
            <!--工具条开始-->
            <div id="Shearbar">
                <a id="LeftRotate" href="javascript:void(0);">
                    <em></em> 向左旋转
                </a>
                <em class="hint L"></em>
                <div class="ZoomDist" id="ZoomDist">
                    <div id="Zoomcentre"></div>
                    <div id="ZoomBar"></div>
                    <span class="progress"></span>
                </div>
                <em class="hint R"></em>
                <a id="RightRotate" href="javascript:void(0);">
                    向右旋转 <em></em>
                </a>
                <p class="Psava">
                    <a id="againIMG" href="javascript:void(0);">重新选择</a>
                    <a id="saveShear" href="javascript:void(0);">保存截图</a>
                </p>
            </div>
            <!--工具条结束-->
        </div>
        <!--primary范围结束-->
        <div style="clear: both"></div>
    </div>
    <!--shearphoto_main范围结束-->

    <!--主功能部份 主功能部份的标签请勿随意删除，除非你对shearphoto的原理了如指掌，否则JS找不到DOM对象，会给你抱出错误-->
    <!--相册-->
    <div id="photoalbum">
        <!--假如你不要这个相册功能。把相册标签删除了，JS会抱出一个console.log()给你，注意查收，console.log的内容是告诉，某个DOM对象找不到-->
        <h1>相册</h1>
        <i id="close"></i>
        <ul>
            <li><img src="__STATIC__/shearphoto/file/photo/1.jpg" serveUrl="file/photo/1.jpg" alt=""/></li>
            <!--serveUrl 是对于服务器路径而言，一般不需要改动，如果index.html位置改变时，你只需要改动 src就可以-->
        </ul>
    </div>
    <!--相册-->
    <!--拍照-->
    <div id="CamBox">
        <!--假如你不要这个拍照功能。把拍照标签删除了，JS会抱出一个console.log()给你，注意查收，console.log的内容是告诉，某个DOM对象找不到-->
        <p class="lens"></p>
        <div id="CamFlash"></div>
        <p class="cambar">
            <a href="javascript:void(0);" id="CamOk">拍照</a>
            <a href="javascript:void(0);" id="setCam">设置</a>
            <a href="javascript:void(0);" id="camClose">关闭</a>
            <span style="clear:both;"></span>
        </p>
        <div id="timing"></div>
    </div>
    <!--拍照-->
</div>
<script>
    $(function () {
        var buttons = $('#ShearPhotoForm').find('a');
        var top = [];
        switch (buttons.length) {
            case 1:
                top = ['50%'];
                break;
            case 2:
                top = ['30%', '60%'];
                break;
            case 3:
                top = ['25%', '50%', '75%'];
                break;
            default:
                break;
        }

        for (var i = 0; i < buttons.length; i++) {
            $(buttons[i]).css('top', top[i]);
        }
    });
</script>
</body>
</html>
