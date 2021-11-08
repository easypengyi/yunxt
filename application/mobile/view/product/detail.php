{extend name="public/base" /}

{block name="before_scripts"}
<link rel="stylesheet" type="text/css" href="__STATIC__/swiper/dist-4.3.3/css/swiper.min.css"/>
<link rel="stylesheet" type="text/css" href="__STATIC__/minirefresh/dist-2.0.2/minirefresh.min.css"/>
<style>

    .shop_detail p {
        width: 98%;
        font-weight: unset;
    }

    #buy_number{
        width: 0.7rem;
        text-align: center;
        color: #8a8a8a;
        font-size: 0.35rem;
        background: #f4f4f4;
        margin-left: 0.2rem;
        margin-right: 0.2rem;
        line-height: 20px;
        height: 20px;
    }
</style>
{/block}

{block name="main-content"}
<div class="header header-bt" style="position:fixed;z-index:999;top:0;left:0;">
    <a href="{$return_url}">
        <img src="__MODULE_IMG__/ic21.png" alt="">
    </a>
</div>
<div class="center">
    <div class="inform" >
        <div class="inform-bann swiper-container">
            <ul class="swiper-wrapper">
                <?php if (isset($data_info['detail_image']) && !empty($data_info['detail_image'])): ?>
                    <?php foreach ($data_info['detail_image'] as $k => $v): ?>
                        <li class="swiper-slide"><img src="{$v.full_url}" alt=""/></li>
                    <?php endforeach; ?>
                <?php endif; ?>
            </ul>
            <div class="inform-page swiper-pagination" style="background-color: unset;"></div>
        </div>
        <div class="inform-pay">
                <img class="share_btn"  style="float: right;width: 0.5rem;  margin-right: 0.2rem; margin-top: 0.2rem;" src="__MODULE_IMG__/qrcode.png">
            <h3>
                <div>
                    <div>
                        <strong>{$data_info.name}</strong>
                        <p style="line-height: 0.6rem;font-size: 0.25rem; color: #999;">{$data_info.description}</p>
                    </div>
                    <div style="float: left;width: 100%;">
                       <b style="font-size: 0.35rem; font-weight: bold;">￥{$data_info.current_price}</b>
                       <?php if ($data_info['current_price'] != $data_info['original_price']): ?>
                            <b style="font-size: 0.25rem;text-decoration-line: line-through;color: #999;">￥{$data_info.original_price}</b>
                        <?php endif; ?>
                        <p style="line-height: 0.8rem;font-size: 0.25rem;"> <span>库存：{$data_info.stock}</span> <span style="float: right;">已售：{$data_info.sold_number}</span></p>
                    </div>
                </div>
            </h3>
        </div>
        <div class="inform-related" style=" height: 1rem; line-height: 1rem;">
                &nbsp;&nbsp;&nbsp;购买数量：
                    <div style=" float: right;margin-right: 0.5rem;">
                        <b class="buy_ddd"  style="font-size: 0.6rem;">-</b>
                        <input type="number" id="buy_number" value="1">
                        <b class=" buy_add"  style="font-size: 0.45rem;">+</b>
                    </div>
        </div>
        <div class="inform-related">
            <h6>
                <span>
                    商品详情
                </span>
                {:file_get_contents_no_ssl($data_info.detail_url)}
            </h6>
        </div>
    </div>

    <div class="fix-pay" style="width: 80%;right: 0;left: unset;"><h4><a href="javascript:" class="pay" >立即购买</a></h4></div>
    <div class="fix-pay" style="width: 20%">
        <h4>
            <a href="tel:4000000000" class="user" style="display:none">客服</a>
        </h4>
    </div>

</div>

<div class="shopBg_wrap" >
    <div class="shopbg" id="ewmWrap"
         style="background-image: url('{$share_img}'); ">
        <div class="shopbg_ewm" id="qrcode"></div>
        <div class="shopbg_logo"   style="background-image: url('{$img}');"></div>
    </div>
</div>
<div class="shopBg_canvas"></div>
<div class="shopMask"></div>
{/block}


{block name="scripts"}
<script src="__STATIC__/swiper/dist-4.3.3/js/swiper.min.js"></script>
<script src="__STATIC__/minirefresh/dist-2.0.2/minirefresh.min.js"></script>
<script src="__STATIC__/js/html2canvas.js"></script>
<script src="__STATIC__/js/qrcode.min.js"></script>
<script src="__STATIC__/js/canvas2image.js"></script>
<script>
    var product = {
        product_id: parseInt('{$data_info.product_id}'),
        group_id:'{$member.group_id}',
    };


    $(function () {
        var qrcode = new QRCode(document.getElementById("qrcode"), {
            text: '{$share_code}',
            width: 100,
            height: 100,
            correctLevel: QRCode.CorrectLevel.L
        });

        //显示二维码大图
        $('.share_btn').on('click', function (event) {

            show_message('正在生成海报，请稍等......');
            $('.shopBg_wrap').show();
            var width = $(".shopbg").width(); //获取二维码dom的 宽高
            var height = $(".shopbg").height();
            var canvas = document.createElement("canvas"); //新建画布
            //要将 canvas 的宽高设置成容器宽高的 2 倍，处理手机上模糊问题
            canvas.width = width;
            canvas.height = height;
            canvas.getContext("2d").scale(2, 2); //初始化2倍
            var opts = {
                scale: 2,
                canvas: canvas,
                width: width,
                height: height,
                useCORS: true,//允许图片跨域 需要后端配合
            };
            html2canvas(document.getElementById('ewmWrap'), opts)
                .then(
                    function (canvas) {
                        //画图转图片的插件 Canvas2Image，转为base64
                        var img = Canvas2Image.convertToImage(canvas, canvas.width, canvas.height);
                        $('.shopBg_canvas').append(img);
                        $(".shopBg_canvas").find("img").css({
                            "width": canvas.width/4 + "px",
                            "height": canvas.height/4 + "px",
                        })
                    });

            $('.shopMask').show();
        });

        $('.shopMask').on('click', function (event) {
            $('.shopBg_canvas').find('img').remove();
            $('.shopBg_wrap').hide();
            $(this).hide();
        })

        new Swiper('.inform-bann', {
            loop:true,
            autoplay: {
                delay: 3000,
                stopOnLastSlide: false,
                disableOnInteraction: false
            },
            pagination: {
                el: '.inform-page'
            }
        });

        //数量增加+
        $(".buy_add").click(function () {
            var strong = $('#buy_number').val();
            if(strong < 100){
                strong++;
            }
            $('#buy_number').val(strong);

        });
        //数量减少-
        $(".buy_ddd").click(function () {
            var strong = $('#buy_number').val();
            if (strong > 1){
                strong--;
            }
            $('#buy_number').val(strong);
        });

        $(".pay").click(function () {

            var product_num =  $('#buy_number').val();
            if (product_num == '') {
                show_message('购买数量不能为空');
                return false;
            }

            $.ajax({
                type: 'POST',
                url: "{:folder_url('Ajax/single1_settlement')}",
                data: {product_id: product.product_id,product_num:product_num},
                success: function (result) {
                    if (result.code !== 1) {
                        show_message(result.msg);
                        if (result.url !== '' && typeof (result.url) !== 'undefined') {
                            window.location.href = result.url;
                            return;
                        }

                        return;
                    }
                    window.location.href =  encode_url('order1_submit');
                }
            });

        });
    });
</script>
{/block}
