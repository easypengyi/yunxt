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
                                订单号：&nbsp;{$data_info.order_sn}
                            </label>
                        </td>
                    </tr>

                    <tr>
                        <td height="40" style="padding-left:10px;" colspan="2">
                            <label>
                                订单支付金额：&nbsp;{$data_info.amount}
                            </label>
                        </td>
                    </tr>
                    <tr>
                        <td height="40" style="padding-left:10px;" colspan="2">
                            <label>
                                下单时间：&nbsp;{$data_info.order_time ? date('Y-m-d H:i:s', $data_info.order_time) : ''}
                            </label>
                        </td>
                    </tr>
                    <tr>
                        <td height="40" style="padding-left:10px;" colspan="2">
                            <label>
                                支付时间：&nbsp;{$data_info.payment_time ? date('Y-m-d H:i:s', $data_info.payment_time) : ''}
                            </label>
                        </td>
                    </tr>
                    <tr>
                        <td height="40" style="padding-left:10px;" colspan="2">
                            <label>
                                完成时间：&nbsp;{$data_info.finish_time ? date('Y-m-d H:i:s', $data_info.finish_time) : ''}
                            </label>
                        </td>
                    </tr>



                    <tr>
                        <td height="40" style="padding-left:10px;width: 90px">
                            <label>
                                报单信息：
                            </label>
                        </td>
                        <td>
                            <table>
                                <tr>
                                    <td height="20" style="color:#666666">
                                        <label>
                                            <span class="lbl" style="margin-right:20px;"> 名称：{$data_info.product_name}</span>
                                        </label>
                                        <label>
                                            <span class="lbl" style="margin-right:20px;"> 单价：{$data_info.unit_price}</span>
                                        </label>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <tr>
                        <td height="40" style="padding-left:10px;" colspan="2">
                            <label>
                               地址：&nbsp;{$data_info.address}
                            </label>
                        </td>
                    </tr>
                    <tr>
                        <td height="40" style="padding-left:10px;" colspan="2">
                            <label>
                                身份证号码：&nbsp;{$data_info.uid}
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
