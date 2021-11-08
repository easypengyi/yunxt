<!DOCTYPE html>

<html lang="zh">

<!--suppress HtmlRequiredTitleElement -->
<head design-width="750">
    {include file='public/head' /}
    {block name="before_scripts"}{/block}
    {block name="styles"}{/block}
</head>

<body class="bg" style="background: white">
<!-- 整个页面内容开始 -->
{block name="main-content"}{/block}
<!-- 整个页面内结束 -->

<!-- 页面隐藏内容开始 -->
<div class="none">
    {block name="hide-content"}{/block}
</div>
<!-- 页面隐藏内容结束 -->


<!--提示弹窗开始-->
<div class="qntc">
    <span class="qntc-c"></span>
</div>
<!--提示弹窗结束-->

<!--确认弹窗开始-->
<div class="grzx-tc">
    <div class="grzx-tcc animated">
        <div class="grzx-tct"></div>
        <div class="grzx-tcd">
            <input class="grzx-tcdp grzx-tcde1" type="button" value="取消"/>
            <input class="grzx-tcdp grzx-tcde2" type="button" value="确认"/>
        </div>
    </div>
</div>
<!--确认弹窗结束-->

<script src="__STATIC__/jquery-form/dist-4.2.2/jquery.form.min.js"></script>
<script src="__JS__/wap_base.min.js?version={$file_version}"></script>
<script src="__JS__/wow.min.js?version={$file_version}"></script>
<script src="__MODULE_JS__/auto-size.js?version={$file_version}"></script>
<script src="__MODULE_JS__/version-3.2.8.js?version={$file_version}"></script>
<script src="https://res.wx.qq.com/open/js/jweixin-1.6.0.js" type="text/javascript"></script>
<script>

    var title = '{$title|default=""}';
    var imgUrl ='{$imgUrl|default=""}';
    var link = '{$url|default=""}';
    var desc = '{$desc|default=""}';
    wx.config({
        debug: false,
        appId: '{$appId|default=""}',
        timestamp: '{$timestamp|default=""}',
        nonceStr: '{$nonceStr|default=""}',
        signature: '{$signature|default=""}',
        jsApiList: ['updateAppMessageShareData','updateTimelineShareData']
    });

    wx.ready(function () {
        wx.updateAppMessageShareData({
            title:'赋活NMN',
            desc: desc,
            link: link,
            imgUrl: imgUrl,
            success: function () {
            }
        });
        wx.updateTimelineShareData({
            title: '赋活NMN',
            link: link,
            imgUrl: imgUrl,
            desc: desc,
            success: function () {
            }
        });
    });
</script>
{block name="scripts"}{/block}
</body>
</html>
