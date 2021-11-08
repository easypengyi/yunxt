<?php isset($data_list) OR $data_list = []; ?>
<?php isset($select_id) OR $select_id = []; ?>
<?php is_array($select_id) OR $select_id = [$select_id]; ?>

<option value="0" data-level="0" <?php echo in_array(0, $select_id) ? 'selected' : '' ?>>默认顶级</option>
<?php foreach ($data_list as $k => $v): ?>
    <option value="{$v.category_id}" data-level="{$v.level}" <?php echo in_array($v['category_id'], $select_id) ? 'selected' : '' ?>>
        {$v.lefthtml}{$v.name}
    </option>
<?php endforeach; ?>
