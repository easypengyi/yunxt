{extend name="public/base" /}

{block name="before_scripts"}
<link rel="stylesheet" type="text/css" href="__STATIC__/swiper/dist-4.3.3/css/swiper.min.css"/>
<style>
    .item{
        height: 0.7rem;
        line-height: 0.7rem;
        font-size: 14px;
        color: white;
    }
    .inform-bann .swiper-pagination span.swiper-pagination-bullet-active {
        background-color: #E8EEEC;
        border: .01rem solid #E8EEEC;
    }


    .bg {
        background: #095E3B !important;
    }

</style>
{/block}

{block name="main-content"}
<div class=" center ohei">

    <div class="inform-bann swiper-container" style="width: 94%;border-radius: 10px;margin-top: 0.2rem;">
        <ul class="swiper-wrapper">
            <?php if (isset($banner) && !empty($banner)): ?>
                <?php foreach ($banner as $k => $v): ?>
                    <li class="swiper-slide">
                        <a href="{$v.url}" data-id="{$k}">
                        <img style="width: 100%;" src="{$v.image.full_url}" alt=""/>
                        </a>
                    </li>
                <?php endforeach; ?>
            <?php endif; ?>
        </ul>
        <div class="inform-page swiper-pagination" style="background-color: unset;"></div>
    </div>

    <div class="once1">
        <ul>
            <li>美加原瓶原裝入口</li>
            <li>修正药业品牌赋能</li>
            <li>精准流量免费送</li>
        </ul>
    </div>


    <div class="once">
        <ul>
            <li>
                <a href="{:folder_url('Help/contact')}">
                    <img src="__MODULE_IMG__/ptjs.png" alt="">
                    <p>平台介绍</p>
                </a>
            </li>
            <li>
                <a href="{:folder_url('Help/source')}">
                    <img src="__MODULE_IMG__/sczx.png" alt="">
                    <p>素材中心</p>
                </a>
            </li>
            <li>
                    <a href="{:folder_url('Help/video')}">
                    <img src="__MODULE_IMG__/spzx.png" alt="">
                    <p>视频中心</p>
                </a>
            </li>
            <li>
                <a href="{:folder_url('Help/article')}">
                    <img src="__MODULE_IMG__/sxy.png" alt="">
                    <p>商学院</p>
                </a>
            </li>

        </ul>
    </div>

    <?php if (isset($announcement) && !empty($announcement)): ?>
        <?php foreach ($announcement as $k => $v): ?>
            <div style="height: 0.7rem;overflow: hidden; background: #4f78b8;">
                <img style="height: 0.4rem; margin-top: 0.15rem;margin-left: 0.25rem; float: left;margin-right: 0.2rem;" src="__MODULE_IMG__/tongzhi.png">
                <div class="inform-bannn  swiper-container">
                    <div class="swiper-wrapper">
                        <div class="swiper-slide item">{$v.name}</div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>


    <div style="background: #F5F5F5;border-radius: 10px;">
        <p style="    line-height: 1rem;font-weight: bold; padding-left: 0.4rem;font-size: 15px;color: #3E3E3E;">
            金牌推荐
        </p>
        <?php if (isset($banner1) && !empty($banner1)): ?>
            <?php foreach ($banner1 as $k => $v): ?>
                <li style="margin-bottom: 0.1rem; text-align: center;">
                    <a href="{$v.url}" data-id="{$k}">
                        <img style="width: 100%;border-radius: 10px; " src="{$v.image.full_url}" alt=""/>
                    </a>
                </li>
            <?php endforeach; ?>
        <?php endif; ?>
        <div class="null"></div>
    </div>
</div>

<!--底部菜单栏开始-->
{include file='public/footer' activate='1'/}
<!--底部菜单栏结束-->
{/block}


{block name="scripts"}
<script src="__STATIC__/swiper/dist-4.3.3/js/swiper.min.js"></script>
<script>
    $(function () {
        new Swiper('.inform-bann', {
            loop:true,
            autoplay: {
                delay: 2000,
                stopOnLastSlide: false,
                disableOnInteraction: false
            },
            pagination: {
                el: '.inform-page'
            }
        });

        new Swiper('.inform-bannn', {
            loop:true,
            direction:'vertical',
            autoplay: {
                delay: 3000,
                stopOnLastSlide: false,
                disableOnInteraction: false
            },
        });


    });
</script>
{/block}