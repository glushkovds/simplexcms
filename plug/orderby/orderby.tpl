<div class="orderby">
    <?php foreach(self::$data['fields'] as $field): ?>
    <a href="javascript:;" onclick="OrderBy.exec(this,'<?php echo $field['sysname'] ?>')" next="<?php echo $field['next_direction'] ?>" class="<?php echo $field['sysname'] == self::$data['curf'] ? self::$data['curd'] : '' ?>"><?php echo $field['name'] ?></a>
    <?php endforeach ?>
</div>