<?php foreach ($this->fields as $field): ?>
    <?php if ($field->isVisible && !$field->width && $field->filter): ?>
        <div class="filter-extra-field">
            <?= $this->filterField($field) ?>
        </div>
    <?php endif ?>
<?php endforeach ?>

<?php if ($addClearfix): ?>
    <div class="clearfix"></div>
<?php endif ?>
