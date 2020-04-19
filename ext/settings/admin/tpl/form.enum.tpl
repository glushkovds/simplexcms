<select class="form-control" name="<?= $field->name ?>">
    <?php if ($field->isnull): ?>
        <option value="">&nbsp;</option>
    <?php endif ?>
    <?php foreach ($values as $key => $value): ?>
        <option value="<?= htmlspecialchars($key) ?>"<?= $row[$field->name] == $key ? ' selected' : '' ?>><?= $value ?></option>
    <?php endforeach ?>
</select>