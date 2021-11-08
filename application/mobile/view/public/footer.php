
<style>
    .fixed-menu a {
        width: 50%;
    }
</style>

<div class="fixed-menu center">
    <a href="{:folder_url('Index/index')}" id="menu-1">
        <i><img src="__IMG__/mobile/menu1.png" alt=""><img src="__IMG__/mobile/menu1-1.png" class="activate" alt=""></i>首页
    </a>
<!--    <a href="{:folder_url('Help/index')}" id="menu-3">-->
<!--        <i><img src="__IMG__/mobile/menu2.png" alt=""><img src="__IMG__/mobile/menu2-1.png" class="activate" alt=""></i>联创时代-->
<!--    </a>-->
    <a href="{:folder_url('User/index')}" id="menu-5">
        <i><img src="__IMG__/mobile/menu5.png" alt=""><img src="__IMG__/mobile/menu5-1.png" class="activate" alt=""></i>我的
    </a>
</div>
<script>
    $(function () {
        var menu = $('#menu-[activate]');
        menu.find('img').hide();
        menu.find('.activate').show();
        menu.addClass('acti');
    })
</script>