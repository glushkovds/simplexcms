<?php
if (!isset($isGroup)) {
    $isGroup = false;
}
?>
<div class="form-group" style="<?php echo $isGroup ? 'padding-left: 30px; position: relative' : '' ?>">
    <?php if ($field instanceof SFFBool): ?>
        <?php if ($isGroup): ?>
            <div style="position: absolute; top: 2px; left: 0">
                <input class="group-set" type="checkbox" name="set_param[<?php echo $field->name ?>]" value="" title="Изменить это поле" />
            </div>
        <?php endif ?>
        <div class="checkbox-list">
            <label class="checkbox-inline">
                <?php echo $field->input($field->value) ?>
                <?php echo $field->label ?>
                <?php if ($field->required): ?>
                    <i class="red">*</i>
                <?php endif ?>
            </label>
        </div>
    <?php else: ?>
        <?php if ($isGroup): ?>
            <div style="position: absolute; top: 30px; left: 0">
                <input class="group-set" type="checkbox" name="set_param[<?php echo $field->name ?>]" value="" title="Изменить это поле" />
            </div>
        <?php endif ?>
        <label class="">
            <?php echo $field->label ?>
            <?php if ($field->required): ?>
                <i class="red">*</i>
            <?php endif ?>
        </label>
        <?php echo $field->input($field->value) ?>
    <?php endif ?>

    <?php if (!empty($field->help)): ?>
        <span class="help-block">
            <?php echo $field->help ?>
        </span>
    <?php endif ?>

</div>