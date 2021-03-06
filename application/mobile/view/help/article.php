{extend name="public/base" /}

{block name="before_scripts"}
<link rel="stylesheet prefetch" href="__STATIC__/PhotoSwipe/dist/photoswipe.css">
<link rel="stylesheet prefetch" href="__STATIC__/PhotoSwipe/dist/default-skin/default-skin.css">
<style>
    * {margin: 0;padding: 0;}
    .my-gallery {width:100%;margin: 0 auto;}
    .my-gallery .img-dv {width:100%;}
    .my-gallery .img-dv a {display:block;width:100%;text-align: center}
    .my-gallery .img-dv a img {width:100%; margin-top: -0.05rem;}

</style>
{/block}

{block name="main-content"}
<div class="center">
    <div class="header"><a href="{$return_url}"><img src="__MODULE_IMG__/ic21.png" alt=""></a>
    </div>

    <div class="my-gallery" data-pswp-uid="1">
        <figure>
            <div class="img-dv"><a href="https://ydn-product.oss-cn-hangzhou.aliyuncs.com/upload/image/ds1.jpg" data-size="1032x2322">
                    <img src="https://ydn-product.oss-cn-hangzhou.aliyuncs.com/upload/image/ds1.jpg"></a>
            </div>
        </figure>
        <figure>
            <div class="img-dv"><a href="https://ydn-product.oss-cn-hangzhou.aliyuncs.com/upload/image/ds2.jpg" data-size="1032x2322">
                    <img src="https://ydn-product.oss-cn-hangzhou.aliyuncs.com/upload/image/ds2.jpg"></a>
            </div>
        </figure>
        <figure>
            <div class="img-dv"><a href="https://ydn-product.oss-cn-hangzhou.aliyuncs.com/upload/image/ds3.jpg" data-size="1032x2322">
                    <img src="https://ydn-product.oss-cn-hangzhou.aliyuncs.com/upload/image/ds3.jpg"></a>
            </div>
        </figure>
        <figure>
            <div class="img-dv"><a href="https://ydn-product.oss-cn-hangzhou.aliyuncs.com/upload/image/ds4.jpg" data-size="1032x2322">
                    <img src="https://ydn-product.oss-cn-hangzhou.aliyuncs.com/upload/image/ds4.jpg"></a>
            </div>
        </figure>
        <figure>
            <div class="img-dv"><a href="https://ydn-product.oss-cn-hangzhou.aliyuncs.com/upload/image/ds5.jpg" data-size="1032x2322">
                    <img src="https://ydn-product.oss-cn-hangzhou.aliyuncs.com/upload/image/ds5.jpg"></a>
            </div>
        </figure>
        <figure>
            <div class="img-dv"><a href="https://ydn-product.oss-cn-hangzhou.aliyuncs.com/upload/image/ds6.jpg" data-size="1032x2322">
                    <img src="https://ydn-product.oss-cn-hangzhou.aliyuncs.com/upload/image/ds6.jpg"></a>
            </div>
        </figure>

    </div>

    <div class="pswp" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="pswp__bg"></div>
        <div class="pswp__scroll-wrap">
            <div class="pswp__container">
                <div class="pswp__item"></div>
                <div class="pswp__item"></div>
                <div class="pswp__item"></div>
            </div>
            <div class="pswp__ui pswp__ui--hidden">
                <div class="pswp__top-bar">
                    <div class="pswp__counter"></div>
                    <button class="pswp__button pswp__button--close" title="Close (Esc)"></button>
                    <!--<button class="pswp__button pswp__button--share" title="Share"></button>-->
                    <button class="pswp__button pswp__button--fs" title="Toggle fullscreen"></button>
                    <button class="pswp__button pswp__button--zoom" title="Zoom in/out"></button>
                    <div class="pswp__preloader">
                        <div class="pswp__preloader__icn">
                            <div class="pswp__preloader__cut">
                                <div class="pswp__preloader__donut"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="pswp__share-modal pswp__share-modal--hidden pswp__single-tap">
                    <div class="pswp__share-tooltip"></div>
                </div>
                <button class="pswp__button pswp__button--arrow--left" title="Previous (arrow left)">
                </button>
                <button class="pswp__button pswp__button--arrow--right" title="Next (arrow right)">
                </button>
                <div class="pswp__caption">
                    <div class="pswp__caption__center"></div>
                </div>
            </div>
        </div>
    </div>
</div>
{/block}


{block name="scripts"}
<script src="__STATIC__/PhotoSwipe/dist/photoswipe.js"></script>
<script src="__STATIC__/PhotoSwipe/dist/photoswipe-ui-default.min.js"></script>
<script>
    var initPhotoSwipeFromDOM = function(gallerySelector) {
        // ????????????DOM????????????????????????URL??????????????????...???
        var parseThumbnailElements = function(el) {
            var thumbElements = el.childNodes,
                numNodes = thumbElements.length,
                items = [],
                figureEl,
                linkEl,
                size,
                item,
                divEl;
            for(var i = 0; i < numNodes; i++) {
                figureEl = thumbElements[i]; // <figure> element
                // ?????????????????????
                if(figureEl.nodeType !== 1) {
                    continue;
                }
                divEl = figureEl.children[0];
                linkEl = divEl.children[0]; // <a> element
                size = linkEl.getAttribute('data-size').split('x');
                // ?????????????????????
                item = {
                    src: linkEl.getAttribute('href'),
                    w: parseInt(size[0], 10),
                    h: parseInt(size[1], 10)
                };
                if(figureEl.children.length > 1) {
                    item.title = figureEl.children[1].innerHTML;
                }
                if(linkEl.children.length > 0) {
                    // <img> ???????????????, ?????????????????????
                    item.msrc = linkEl.children[0].getAttribute('src');
                }
                item.el = figureEl; // ?????????????????? for getThumbBoundsFn
                items.push(item);
            }
            return items;
        };

        // ????????????????????????
        var closest = function closest(el, fn) {
            return el && ( fn(el) ? el : closest(el.parentNode, fn) );
        };

        // ??????????????????????????????
        var onThumbnailsClick = function(e) {
            e = e || window.event;
            e.preventDefault ? e.preventDefault() : e.returnValue = false;
            var eTarget = e.target || e.srcElement;
            var clickedListItem = closest(eTarget, function(el) {
                return (el.tagName && el.tagName.toUpperCase() === 'FIGURE');
            });
            if(!clickedListItem) {
                return;
            }
            var clickedGallery = clickedListItem.parentNode,
                childNodes = clickedListItem.parentNode.childNodes,
                numChildNodes = childNodes.length,
                nodeIndex = 0,
                index;
            for (var i = 0; i < numChildNodes; i++) {
                if(childNodes[i].nodeType !== 1) {
                    continue;
                }
                if(childNodes[i] === clickedListItem) {
                    index = nodeIndex;
                    break;
                }
                nodeIndex++;
            }
            if(index >= 0) {
                openPhotoSwipe( index, clickedGallery );
            }
            return false;
        };

        var photoswipeParseHash = function() {
            var hash = window.location.hash.substring(1),
                params = {};
            if(hash.length < 5) {
                return params;
            }
            var vars = hash.split('&');
            for (var i = 0; i < vars.length; i++) {
                if(!vars[i]) {
                    continue;
                }
                var pair = vars[i].split('=');
                if(pair.length < 2) {
                    continue;
                }
                params[pair[0]] = pair[1];
            }
            if(params.gid) {
                params.gid = parseInt(params.gid, 10);
            }
            return params;
        };

        var openPhotoSwipe = function(index, galleryElement, disableAnimation, fromURL) {
            var pswpElement = document.querySelectorAll('.pswp')[0],
                gallery,
                options,
                items;
            items = parseThumbnailElements(galleryElement);
            // ????????????????????????
            options = {
                galleryUID: galleryElement.getAttribute('data-pswp-uid'),
                getThumbBoundsFn: function(index) {
                    var thumbnail = items[index].el.getElementsByTagName('img')[0], // find thumbnail
                        pageYScroll = window.pageYOffset || document.documentElement.scrollTop,
                        rect = thumbnail.getBoundingClientRect();
                    return {x:rect.left, y:rect.top + pageYScroll, w:rect.width};
                }
            };
            if(fromURL) {
                if(options.galleryPIDs) {
                    for(var j = 0; j < items.length; j++) {
                        if(items[j].pid == index) {
                            options.index = j;
                            break;
                        }
                    }
                } else {
                    options.index = parseInt(index, 10) - 1;
                }
            } else {
                options.index = parseInt(index, 10);
            }
            if( isNaN(options.index) ) {
                return;
            }
            if(disableAnimation) {
                options.showAnimationDuration = 0;
            }
            gallery = new PhotoSwipe( pswpElement, PhotoSwipeUI_Default, items, options);
            gallery.init();
        };

        var galleryElements = document.querySelectorAll( gallerySelector );
        for(var i = 0, l = galleryElements.length; i < l; i++) {
            galleryElements[i].setAttribute('data-pswp-uid', i+1);
            galleryElements[i].onclick = onThumbnailsClick;
        }
        var hashData = photoswipeParseHash();
        if(hashData.pid && hashData.gid) {
            openPhotoSwipe( hashData.pid ,  galleryElements[ hashData.gid - 1 ], true, true );
        }
    };

    initPhotoSwipeFromDOM('.my-gallery');
</script>
{/block}
