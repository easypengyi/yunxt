{extend name="public/base" /}
{block name="before_scripts"}
<link rel="stylesheet" type="text/css" href="__STATIC__/swiper/dist-4.3.3/css/swiper.min.css"/>
<link rel="stylesheet" type="text/css" href="__STATIC__/minirefresh/dist-2.0.2/minirefresh.min.css"/>
<style>
  .order-tag ul li {
        width: 50%;
        font-weight: bold;
        font-size: 15px;
    }
    .order-list {
        top: 1.31rem;
        width: 92%;
        right: 0;
        left: 0;
    }

    .order-list1{
        top: 1.31rem;
        width: 100%;
        right: 0;
        left: 0;
        position: absolute;
        bottom: 0;
    }
    .order_data_div{
        background: #fefefe;
        border-top-left-radius: 15px;
        border-top-right-radius: 15px;
    }

  .user-form ul {
       background: unset;
      padding: unset;
  }

  .order_data_div1 img {
      width: 100%;
      height: unset;
  }

  .title_content {
      line-height: 0.5rem;
      height: 0.5rem;
      padding-left: 0.1rem;
      margin-bottom: 0.1rem;
      font-size: 12px;
      color: #737373;
      font-weight: bold;
  }

  .time_content {
      float: right;
      color: #737373;
      display: inline-block;
      height: 0.5rem;
      line-height: 0.5rem;
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
    <div class="tabs dt" style="position:fixed;z-index:999;top:0.71rem;left:0;">
        <span class="category acti" data-cate="1"><i>文案中心</i></span>
        <span class="category" data-cate="2"><i>文章中心</i></span>
    </div>


    <div class="user-form">
        <div class="box">
            <div class="order-list" style="background: #f4f4f4;">
                <ul id="comprehensive" class="minirefresh-wrap" >
                    <div id="comprehensive-data" class="minirefresh-scroll" style="background: #f4f4f4;">
                    </div>
                </ul>
            </div>
        </div>
        <div class="box">
            <div class="order-list1" style="background: #f4f4f4;">
                <ul id="comprehensive1" class="minirefresh-wrap" >
                    <div id="comprehensive-data1" class="minirefresh-scroll" style="background: #f4f4f4;">
                    </div>
                </ul>
            </div>
        </div>
    </div>



</div>

<!--底部菜单栏开始-->
<!--底部菜单栏结束-->
{/block}

{block name="hide-content"}
<div id="order_data">
    <div class="big-div" style="margin-top: 0.4rem; border-radius: 20px;">
        <div class="order_data_div">
            <img src="https://ydn-product.oss-cn-hangzhou.aliyuncs.com/upload/image/head-img.jpg">
            <p class="title_content">雅典娜平衡学院</p>
            <p class="time_content" ></p>
            <span class="copy">复制文案</span>
        </div>
        <div class="order_data_div1">
            <p class="content" style="background: white;"></p>
            <img class="content_img" src="">
        </div>
    </div>
</div>


<div id="order_data1">
    <div class="big-div" style="background: white; margin-top: 0.4rem; width: 92%; border-radius: 15px;    border-bottom: 1px solid #ccc;"  data-id="0"  >
        <div class="order_data_div1">
            <img class="content_img" data-id="0"  src="" style="border-top-left-radius: 15px;border-top-right-radius: 15px;">
            <p style="margin-top: 0.2rem; padding-left: 0.2rem; padding-right: 0.2rem; height: 0.7rem;" >
                <span  class="title_content"></span>
                <span  class="time_content"></span>
            </p>

        </div>
    </div>
</div>




{/block}

{block name="scripts"}
<script src="__STATIC__/swiper/dist-4.3.3/js/swiper.min.js"></script>
<script src="__STATIC__/minirefresh/dist-2.0.2/minirefresh.min.js"></script>

<script>

    var order_data = {
        page: 1,
    };

    var order_data1 = {
        page: 1,
    };

    $(function () {

        tabs_cg('.tabs span', '.user-form .box', 'click', 'acti', '', 0);

        var comprehensive = new MiniRefresh({
            container: '#comprehensive',
            down: {
                isLock: true
            },
            up: {
                callback: function () {
                    $.ajax({
                        type: 'POST',
                        url: "{:folder_url('Ajax/source_list')}",
                        data: {page: order_data.page},
                        success: function (result) {
                            if (result.code !== 1) {
                                show_message(result.msg);
                                return;
                            }

                            var data = result.data;
                            if (data['page'] === 1) {
                                $('#comprehensive-data').html('');
                            }

                            data['list'].forEach(function (val) {
                                    var html = $('#order_data').find('.big-div').clone();
                                    html.find('.time_content').text(format_time('yyyy-MM-dd hh:mm', val['create_time'] * 1000));
                                    html.find('.content').text(val['content']);
                                    html.find('.content_img').attr('src', val['image']['full_url']);
                                    html.find('.copy').show();
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

        $('body').on('click', '.copy', function () {
            var div = $(this).parents('.big-div');
            var message = div.find('.content').text();
            var input = document.createElement("input");
            input.value = message;
            document.body.appendChild(input);
            input.select();
            input.setSelectionRange(0, input.value.length), document.execCommand('Copy');
            document.body.removeChild(input);
            show_message('内容已复制');
        });


        var comprehensive1 = new MiniRefresh({
            container: '#comprehensive1',
            down: {
                isLock: true
            },
            up: {
                callback: function () {
                    $.ajax({
                        type: 'POST',
                        url: "{:folder_url('Ajax/article_list')}",
                        data: {page: order_data.page},
                        success: function (result) {
                            if (result.code !== 1) {
                                show_message(result.msg);
                                return;
                            }

                            var data = result.data;
                            if (data['page'] === 1) {
                                $('#comprehensive-data1').html('');
                            }

                            data['list'].forEach(function (val) {
                                var html = $('#order_data1').find('.big-div').clone();
                                html.find('.content_img').data('id', val['article_id']);
                                html.find('.title_content').text(val['title']);
                                html.find('.time_content').text(format_time('yyyy-MM-dd hh:mm', val['create_time'] * 1000));
                                html.find('.content').text(val['content']);
                                html.find('.content_img').attr('src', val['image']['full_url']);
                                $('#comprehensive-data1').append(html);
                            });
                            if (data['page'] === data['total_page']) {
                                comprehensive1.endUpLoading(true);
                                $('.minirefresh-upwrap').hide();
                            } else {
                                order_data1.page = data['page'] + 1;
                                comprehensive1.endUpLoading();
                                $('.minirefresh-upwrap').hide();
                            }
                        }
                    });
                    $('.minirefresh-upwrap').hide();
                }
            }
        });

        // $('body').on('click', '.content_img', function () {
        //     var id = $(this).data('id');
        //     window.location.href = encode_url('article_detail', {article_id: id});
        //     return false;
        // });






    });


</script>
{/block}