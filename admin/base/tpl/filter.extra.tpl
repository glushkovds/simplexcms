<?php foreach ($this->fields as $field): ?>
    <?php if ($field->isVisible && !$field->width && $field->filter): ?>
        <div class="filter-extra-field">
            <?php $field->filter(@$_SESSION[$this->table]['filter'][$field->name]) ?>
        </div>
    <?php endif ?>
<?php endforeach ?>

<?php if ($addClearfix): ?>
    <div class="clearfix"></div>
<?php endif ?>
