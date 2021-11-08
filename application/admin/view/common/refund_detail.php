{extend name="public/base" /}
{block name="main-content"}
<?php isset($data_info) OR $data_info = []; ?>

<div class="page-content">
    <div class="row">
        <div class="col-xs-12">
            <div class="form-group">
                <table width="90%" border="0" align="center" cellpadding="0" cellspacing="0">
                    <?php foreach ($data_info as $k => $v): ?>
                        <tr>
                            <td height="40">
                                <label>
                                    <span class="lbl" style="margin-right:20px;"> 分类：{$v.category.name}</span>
                                </label>
                                <label>
                                    <span class="lbl" style="margin-right:20px;"> 名称：{$v.ticket.name}</span>
                                </label>
                                <label>
                                    <span class="lbl" style="margin-right:20px;"> 票码：{$v.code}</span>
                                </label>
                                <label>
                                    <span class="lbl" style="margin-right:20px;"> 金额：{$v.money}</span>
                                </label>
                                <label>
                                    <span class="lbl" style="margin-right:20px;"> 状态：{$status[$v.status]}</span>
                                </label>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            </div>
        </div>
    </div>
</div>
{/block}
