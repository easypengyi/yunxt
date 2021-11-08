{extend name="public/base" /}
{block name="before_scripts"}
<link rel="stylesheet" type="text/css" href="__STATIC__/swiper/dist-4.3.3/css/swiper.min.css"/>
<link rel="stylesheet" type="text/css" href="__STATIC__/minirefresh/dist-2.0.2/minirefresh.min.css"/>
<style>


.list_name{
height: 0.7rem;
line-height: 0.7rem;
font-weight: bold;
text-align: center;
color: #737373;
font-size: 13px;
}
.order-list {
    top: 0.71rem;
}

.video_div{
    width: 92%;
    background: white;
    margin-top: 0.4rem;
    border-radius:15px;
    border-bottom: 1px solid #ccc;
}
.minirefresh-scroll {

     background: unset;
}
.video1{
    border-top-left-radius: 15px;
    border-top-right-radius: 15px;
}

</style>
{/block}
{/block}
{block name="main-content"}
<div class="center">
    <div class="header header-bt" style="position:fixed;z-index:999;top:0;left:0;">
        <a href="{$return_url}">
            <img src="__MODULE_IMG__/ic21.png" alt="">
        </a>
    </div>

    <div class="order-list" style="background: #f4f4f4;">
        <ul id="comprehensive" class="minirefresh-wrap">
            <div id="comprehensive-data" class="minirefresh-scroll">
            </div>
            <div class="null"></div>
        </ul>

    </div>


</div>

<!--底部菜单栏结束-->
{/block}

{block name="hide-content"}

<div id="order_data">
    <div class="video_div"  data-id="">
        <video class="video1" height="100%" width="100%"  poster='' src="" controls="controls">
        </video>
        <p class="list_name" ></p>
    </div>
</div>




{/block}

{block name="scripts"}
<script src="__STATIC__/swiper/dist-4.3.3/js/swiper.min.js"></script>
<script src="__STATIC__/minirefresh/dist-2.0.2/minirefresh.min.js"></script>

<script>

    var order_data = {
        page: 1,
        category: 1
    };

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

        var comprehensive = new MiniRefresh({
            container: '#comprehensive',
            down: {
                isLock: true
            },
            up: {
                callback: function () {
                    $.ajax({
                        type: 'POST',
                        url: "{:folder_url('Ajax/video_list')}",
                        data: {page: order_data.page, category: order_data.category},
                        success: function (result) {
                            if (result.code !== 1) {
                                show_message(result.msg);
                                return;
                            }

                            var data = result.data;
                            if (data['page'] === 1) {
                                $('#comprehensive-data').html('');
                            }

                            replaceUrl('category', order_data.category);
                            data['list'].forEach(function (val) {
                                    var html = $('#order_data').find('div').clone();
                                    html.data('id', val['id']);
                                    html.find('.video1').attr('src', val['url']);
                                    html.find('.video1').attr('poster', val['image']['full_url']);
                                    html.find('.list_name').text(val['name']);
                                $('#comprehensive-data').append(html);
                            });
                            if (data['page'] === data['total_page']) {
                                comprehensive.endUpLoading(true);
                                $('.minirefresh-upwrap').hide();
                            } else {
                                order_data.page = data['page'] + 1;
                                comprehensive.endUpLoading();
                                $('.minirefresh-upwrap').hide();
                            }
                        }
                    });
                    $('.minirefresh-upwrap').hide();
                }
            }
        });


    });
    /*
    * 替换当前url 并不导致浏览器页面刷新
    * name 参数名
    * value 参数值
    */
    function replaceUrl(name, value) {
        history.replaceState({name: value}, '', '?' + name + '=' + value);
    }

</script>
{/block}