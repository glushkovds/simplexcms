<?php if ($this->canAdd): ?>
    <a class="btn btn-primary" href="?action=form">
        <i class="fa fa-plus"></i>
        <span class="hidden-480"> Добавить </span>
    </a>
<?php endif ?>
<?php if ($this->canEdit && $this->canEditGroup): ?>
    <button class="btn btn-success action-with-select" href="javascript:;" onclick="editRows()" disabled="">
        <i class="fa fa-edit"></i> Редактировать
    </button>
<?php endif ?>
<?php if ($this->canAdd && $this->canCopy): ?>
    <button class="btn btn-success action-with-select" href="javascript:;" onclick="copyRows()" disabled="">
        <i class="fa fa-copy"></i> Копировать
    </button>
<?php endif ?>
<?php if ($this->canDelete): ?>
    <button class="btn btn-danger action-with-select" href="#delete-dialog" data-toggle="modal" disabled="">
        <i class="fa fa-times"></i> Удалить
    </button>
<?php endif ?>
