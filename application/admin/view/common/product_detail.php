{extend name="public/base" /}
{block name="main-content"}
<?php isset($data_info) OR $data_info = []; ?>

<div class="page-content">
    <div class="row">
        <div class="col-xs-12">
            <div class="form-group">
                <table width="90%" border="0" align="center" cellpadding="0" cellspacing="0">
                    <tr>
                        <td height="40" style="padding-left:10px;" colspan="2">
                            <label>
                                分类：&nbsp;{$data_info.category.name}
                            </label>
                        </td>
                    </tr>
                    <tr>
                        <td height="40" style="padding-left:10px;" colspan="2">
                            <label>
                                名称：&nbsp;{$data_info.name}
                            </label>
                        </td>
                    </tr>
                    <tr>
                        <td height="40" style="padding-left:10px;" colspan="2">
                            <label>
                                描述：&nbsp;{$data_info.description}
                            </label>
                        </td>
                    </tr>
                    <tr>
                        <td height="40" style="padding-left:10px;" colspan="2">
                            <label>
                                图片：&nbsp;<a href="{$data_info.image.full_url}" target="_blank"><img src="{$data_info.image.full_url}" width="50" height="50" alt=""/></a>
                            </label>
                        </td>
                    </tr>
                    <tr>
                        <td height="40" style="padding-left:10px;" colspan="2">
                            <label>
                                现价：&nbsp;{$data_info.current_price}
                            </label>
                        </td>
                    </tr>
                    <tr>
                        <td height="40" style="padding-left:10px;" colspan="2">
                            <label>
                                机构价：&nbsp;{$data_info.organization_price}
                            </label>
                        </td>
                    </tr>
                    <tr>
                        <td height="40" style="padding-left:10px;" colspan="2">
                            <label>
                                原价：&nbsp;{$data_info.original_price}
                            </label>
                        </td>
                    </tr>
                    <tr>
                        <td height="40" style="padding-left:10px;" colspan="2">
                            <label>
                                库存：&nbsp;{$data_info.stock}
                            </label>
                        </td>
                    </tr>
                    <tr>
                        <td height="40" style="padding-left:10px;" colspan="2">
                            <label>
                                销售数量：&nbsp;{$data_info.sold_number}
                            </label>
                        </td>
                    </tr>
                    <tr>
                        <td height="40" style="padding-left:10px;width: 90px">
                            <label>
                                门票信息：
                            </label>
                        </td>
                        <td>
                            <table>
                                <?php foreach ($data_info['ticket'] as $k => $v): ?>
                                    <tr>
                                        <td height="20" style="color:#666666">
                                            <label>
                                                <span class="lbl" style="margin-right:20px;"> 名称：{$v.ticket.name}</span>
                                            </label>
                                            <label>
                                                <span class="lbl" style="margin-right:20px;"> 票种：{$species[$v.ticket.species]}</span>
                                            </label>
                                            <label>
                                                <span class="lbl" style="margin-right:20px;"> 有效天数：{$v.ticket.delay_days ?: '无期限'}</span>
                                            </label>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td height="40" style="padding-left:10px;" colspan="2">
                            <label>
                                详情图：&nbsp;
                            </label>
                            <?php if (isset($data_info['detail_image']) && !empty($data_info['detail_image'])): ?>
                                <?php foreach ($data_info['detail_image'] as $v) : ?>
                                    <a href="{$v.full_url}" target="_blank" style="text-decoration:none;">
                                        <img src="{$v.full_url}" width="50" height="50" alt=""/>
                                    </a>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <td height="40" style="padding-left:10px;" colspan="2">
                            <label>
                                图文详情：&nbsp;{:file_get_contents_no_ssl($data_info.detail_url)}
                            </label>
                        </td>
                    </tr>
                </table>
            </div>

            <div class="hr hr-16 hr-dotted"></div>
        </div>
    </div>
</div>
{/block}
