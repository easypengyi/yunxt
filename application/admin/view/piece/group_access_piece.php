<?php isset($data_info) OR $data_info = []; ?>
<?php isset($level) OR $level = 1; ?>
<?php isset($left) OR $left = 0; ?>
<?php isset($checked) OR $checked = false; ?>
<?php isset($dataid) OR $dataid = ''; ?>

<tr>
    <?php if ($level == 1): ?>
        <td height="40" style="border-bottom:#CCCCCC solid 1px;padding-left:10px;">
            <label>
                <input name="rules[]" id="{$data_info.id}" value="{$data_info.id}" class="ace ace-checkbox-2 checkbox-parent" type="checkbox" dataid="{$dataid}" <?php echo $checked ? 'checked' : ''; ?>/>
                <span class="lbl"><strong>{$data_info.title}</strong></span>
            </label>
        </td>
    <?php else: ?>
        <td height="30" style="padding-left:{$left};border-bottom:#E7EBF8 dashed 1px; color:#333333;">
            <label>
                <input name="rules[]" id="{$data_info.id}" value="{$data_info.id}" class="ace ace-checkbox-2 checkbox-parent checkbox-child" type="checkbox" dataid="{$dataid}" <?php echo $checked ? 'checked' : ''; ?>/>
                <span class="lbl">{$data_info.title}</span>
            </label>
        </td>
    <?php endif; ?>
</tr>
