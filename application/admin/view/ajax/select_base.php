<?php isset($data_list) OR $data_list = []; ?>
<?php isset($select_id) OR $select_id = []; ?>
<?php is_array($select_id) OR $select_id = [$select_id]; ?>

<?php foreach ($data_list as $k => $v): ?>
    <option value="{$k}" <?php echo in_array($k, $select_id) ? 'selected' : '' ?>>{$v}</option>
<?php endforeach; ?>
