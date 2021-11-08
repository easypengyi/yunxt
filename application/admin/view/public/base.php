<!DOCTYPE html>
<html lang="zh">

<!--suppress HtmlRequiredTitleElement -->
<head>
    {include file='public/head' /}
    {block name="before_scripts"}{/block}
    {block name="styles"}{/block}
</head>

<body class="no-skin">
{include file='public/navigation_bar' /}

<!-- 整个页面内容开始 -->
<div class="main-container" id="main-container">
    <!-- 菜单栏开始 -->
    {include file='public/left_nav' /}
    <!-- 菜单栏结束 -->

    <!-- 主要内容开始 -->
    <div class="main-content">
        <div class="main-content-inner">
            <!-- 右侧主要内容页顶部标题栏开始 -->
            {include file='public/head_nav' /}
            <!-- 右侧主要内容页顶部标题栏结束 -->

            <!-- 右侧下主要内容开始 -->
            {block name="main-content"}{/block}
            <!-- 右侧下主要内容结束 -->
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

<!-- 此页模态框 -->
{block name="modal"}{/block}
<!-- 此页模态框 -->

<!-- 页面隐藏内容开始 -->
<div class="none">
    {block name="hide-content"}{/block}
</div>
<!-- 页面隐藏内容结束 -->

<!-- 整个页面内结束 -->

<script src="__JS__/jquery-maxlength.min.js?version={$file_version}"></script>
<!-- ace的js,可以通过打包生成,避免引入文件数多 -->
<script src="__STATIC__/ace/js/ace.min.js?version={$file_version}"></script>
<script src="__STATIC__/jquery-form/dist-4.2.2/jquery.form.min.js"></script>
<script src="__STATIC__/layer/dist-3.1.1/layer.js"></script>
<script src="__STATIC__/bootstrap-select/dist-1.12.4/js/bootstrap-select.min.js"></script>
<script src="__JS__/admin_base.min.js?version={$file_version}"></script>
<!-- 此页相关插件js -->
{block name="scripts"}{/block}
<!-- 与此页相关的js -->
</body>
</html>
