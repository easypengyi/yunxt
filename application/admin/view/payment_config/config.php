{extend name="public/base" /}
{block name="main-content"}
<?php isset($data_info) OR $data_info = []; ?>

<div class="page-content">
    <div class="row top20">
        <div class="col-xs-12">
            <form class="form-horizontal ajax-form" method="post" action="{$full_url}">
                <input type="hidden" name="return_url" value="{$return_url}"/>

                <div class="form-group">
                    <label class="col-sm-2 control-label no-padding-right" for="form-field-1"> 支付类型 </label>
                    <div class="col-sm-10">
                        <input type="text" value="{$data_info.payment_name|default=''}" placeholder="" class="col-xs-10 col-sm-5" readonly/>
                        <span class="lbl col-xs-12 col-sm-7"></span>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-2 control-label no-padding-right" for="form-field-1"> 支付种类 </label>
                    <div class="col-sm-10">
                        <input type="text" value="{$data_info.pay_type_name|default=''}" placeholder="" class="col-xs-10 col-sm-5" readonly/>
                        <span class="lbl col-xs-12 col-sm-7"></span>
                    </div>
                </div>

                <div class="space-4"></div>

                <?php isset($data_info['config']) OR $data_info['config'] = []; ?>
                <?php is_array($data_info['config']) OR $data_info['config'] = []; ?>
                <?php $config = $data_info['config']; ?>

                <?php if ($data_info['payment_id'] == tool\PaymentTool::ALIPAY): ?>
                    <div class="form-group">
                        <label class="col-sm-2 control-label no-padding-right" for="form-field-1"> 应用ID </label>
                        <div class="col-sm-10">
                            <input type="text" name="app_id" value="{$config.app_id|default=''}" placeholder="" class="col-xs-10 col-sm-5"/>
                            <span class="lbl col-xs-12 col-sm-7"></span>
                        </div>
                    </div>

                    <div class="space-4"></div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label no-padding-right" for="form-field-1"> 签名方式 </label>
                        <div class="col-sm-10">
                            <input type="text" name="sign_type" value="{$config.sign_type|default='RSA2'}" placeholder="" class="col-xs-10 col-sm-5"/>
                            <span class="lbl col-xs-12 col-sm-7"></span>
                        </div>
                    </div>

                    <div class="space-4"></div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label no-padding-right" for="form-field-1"> RSA私钥路径 </label>
                        <div class="col-sm-10">
                            <input type="text" name="rsa_private_key" value="{$config.rsa_private_key|default=''}" placeholder="" class="col-xs-10 col-sm-5"/>
                            <span class="lbl col-xs-12 col-sm-7">从项目根目录起文件路径</span>
                        </div>
                    </div>

                    <div class="space-4"></div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label no-padding-right" for="form-field-1"> 支付宝公钥路径 </label>
                        <div class="col-sm-10">
                            <input type="text" name="ali_public_key" value="{$config.ali_public_key|default=''}" placeholder="" class="col-xs-10 col-sm-5"/>
                            <span class="lbl col-xs-12 col-sm-7">从项目根目录起文件路径</span>
                        </div>
                    </div>
                <?php elseif ($data_info['payment_id'] == tool\PaymentTool::WXPAY): ?>
                    <div class="form-group">
                        <label class="col-sm-2 control-label no-padding-right" for="form-field-1"> 应用ID </label>
                        <div class="col-sm-10">
                            <input type="text" name="app_id" value="{$config.app_id|default=''}" placeholder="" class="col-xs-10 col-sm-5"/>
                            <span class="lbl col-xs-12 col-sm-7"></span>
                        </div>
                    </div>

                    <div class="space-4"></div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label no-padding-right" for="form-field-1"> 应用密钥 </label>
                        <div class="col-sm-10">
                            <input type="text" name="app_secret" value="{$config.app_secret|default=''}" placeholder="" class="col-xs-10 col-sm-5"/>
                            <span class="lbl col-xs-12 col-sm-7"></span>
                        </div>
                    </div>

                    <div class="space-4"></div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label no-padding-right" for="form-field-1"> 商户ID： </label>
                        <div class="col-sm-10">
                            <input type="text" name="mch_id" value="{$config.mch_id|default=''}" placeholder="" class="col-xs-10 col-sm-5"/>
                            <span class="lbl col-xs-12 col-sm-7"></span>
                        </div>
                    </div>

                    <div class="space-4"></div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label no-padding-right" for="form-field-1"> 签名方式： </label>
                        <div class="col-sm-10">
                            <input type="text" name="sign_type" value="{$config.sign_type|default='md5'}" placeholder="" class="col-xs-10 col-sm-5"/>
                            <span class="lbl col-xs-12 col-sm-7"></span>
                        </div>
                    </div>

                    <div class="space-4"></div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label no-padding-right" for="form-field-1"> MD5密钥： </label>
                        <div class="col-sm-10">
                            <input type="text" name="md5_key" value="{$config.md5_key|default=''}" placeholder="" class="col-xs-10 col-sm-5"/>
                            <span class="lbl col-xs-12 col-sm-7"></span>
                        </div>
                    </div>

                    <div class="space-4"></div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label no-padding-right" for="form-field-1"> cert证书路径： </label>
                        <div class="col-sm-10">
                            <input type="text" name="app_cert_pem" value="{$config.app_cert_pem|default=''}" placeholder="" class="col-xs-10 col-sm-5"/>
                            <span class="lbl col-xs-12 col-sm-7">从项目根目录起文件路径</span>
                        </div>
                    </div>

                    <div class="space-4"></div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label no-padding-right" for="form-field-1"> key文件路径： </label>
                        <div class="col-sm-10">
                            <input type="text" name="app_key_pem" value="{$config.app_key_pem|default=''}" placeholder="" class="col-xs-10 col-sm-5"/>
                            <span class="lbl col-xs-12 col-sm-7">从项目根目录起文件路径</span>
                        </div>
                    </div>
                <?php elseif ($data_info['payment_id'] == tool\PaymentTool::UPACPAY): ?>
                    <div class="form-group">
                        <label class="col-sm-2 control-label no-padding-right" for="form-field-1"> 商户ID： </label>
                        <div class="col-sm-10">
                            <input type="text" name="mer_id" value="{$config.mer_id|default=''}" placeholder="" class="col-xs-10 col-sm-5"/>
                            <span class="lbl col-xs-12 col-sm-7"></span>
                        </div>
                    </div>

                    <div class="space-4"></div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label no-padding-right" for="form-field-1"> 证书密码： </label>
                        <div class="col-sm-10">
                            <input type="text" name="sign_cert_pwd" value="{$config.sign_cert_pwd|default=''}" placeholder="" class="col-xs-10 col-sm-5"/>
                            <span class="lbl col-xs-12 col-sm-7"></span>
                        </div>
                    </div>

                    <div class="space-4"></div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label no-padding-right" for="form-field-1"> 证书路径： </label>
                        <div class="col-sm-10">
                            <input type="text" name="sign_cert_path" value="{$config.sign_cert_path|default=''}" placeholder="" class="col-xs-10 col-sm-5"/>
                            <span class="lbl col-xs-12 col-sm-7">从项目根目录起文件路径</span>
                        </div>
                    </div>
                <?php elseif ($data_info['payment_id'] == tool\PaymentTool::UPAY): ?>
                <?php endif; ?>

                <div class="space-4"></div>

                <div class="form-group">
                    <label class="col-sm-2 control-label no-padding-right" for="form-field-1"> 测试模式： </label>
                    <div class="col-sm-10" style="padding-top:5px;">
                        <input type="checkbox" name="use_sandbox" id="use_sandbox" <?php echo ($data_info['use_sandbox'] ?? false) ? 'checked' : ''; ?> value="1" placeholder="" class="ace ace-switch ace-switch-6"/>
                        <span class="lbl">&nbsp;&nbsp;默认关闭</span>
                    </div>
                </div>

                <div class="space-4"></div>

                <div class="clearfix form-actions">
                    <div class="col-md-offset-3 col-md-9">
                        <button class="btn btn-info ajax-submit" type="submit">
                            <i class="ace-icon fa fa-check bigger-110"></i> 保存
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
{/block}

{block name="scripts"}
{/block}
