<?php isset($access) OR $access = ''; ?>

{$access}

<script>
    /* 权限配置 */
    $(function () {
        //动态选择框，上下级选中状态变化
        $('input.checkbox-parent').change(function () {
            var dataid = $(this).attr('dataid');
            $('input[dataid^=' + dataid + '-]').prop('checked', $(this).is(':checked'));
            check_inspect(this);
        });
        $('input.checkbox-child').change(function () {
            var dataid = $(this).attr('dataid');
            if ($(this).is(':checked')) {
                while (dataid.lastIndexOf('-') !== 2) {
                    dataid = dataid.substring(0, dataid.lastIndexOf('-'));
                    $('input[dataid=' + dataid + ']').prop('checked', true);
                }
            }
            else {
                while (dataid.lastIndexOf('-') !== 2) {
                    dataid = dataid.substring(0, dataid.lastIndexOf('-'));
                    if ($('input[dataid^=' + dataid + '-]:checked').length === 0) {
                        $('input[dataid=' + dataid + ']').prop('checked', false);
                    }
                }
            }
            check_inspect(this);
        });


        $('form').each(function() {
            check_inspect(this);
        });
    });
</script>
