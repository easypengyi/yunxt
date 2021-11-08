{extend name="public/base" /}
{block name="main-content"}
<?php isset($data_info) OR $data_info = ['content' => '', 'image' => []]; ?>

<div class="page-content">
    <div class="row">
        <div class="col-xs-12">
            <div class="form-group center">
                <span id="content" style="width: 100%">{$data_info.content|default=''}</span>
            </div>

            <div class="form-group center">
                <?php if (isset($data_info['image']) && !empty($data_info['image'])): ?>
                    <?php foreach ($data_info['image'] as $v) : ?>
                        <div id="image_item" class="col-sm-5">
                            <a href="{$v.full_url}" target="_blank" style="text-decoration:none;">
                                <img src="{$v.full_url}" width="100%" alt=""/>
                            </a>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
{/block}
