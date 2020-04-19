<?php
if (count($this->errors)) {
    echo '<div class="sf-warning">';
    echo '<div>При сохранении возникли ошибки:</div>';
    echo '<ul>';
    foreach ($this->errors as $error) {
        if (is_array($error)) {
            foreach ($error as $e) {
                echo '<li>—&nbsp; ', $e, '</li>';
            }
        } else {
            echo '<li>—&nbsp; ', $error, '</li>';
        }
    }
    echo '</ul>';
    echo '</div>';
}

$showRightCol = (bool) count($this->params['right']);
ob_start();
$this->portlets('right');
$rightPortletsHTML = ob_get_clean();
$showRightCol |= (bool) $rightPortletsHTML;
?>


<form class="sf-form" method="post" action="?action=save" enctype="multipart/form-data">
    <input id="sf-form-submit" type="hidden" name="submit_save" value="" />
    <input type="hidden" name="request_uri" value="<?= $_SERVER['REQUEST_URI'] ?>" />
    <input type="hidden" id="info-table" value="<?= $this->table ?>" />
    <input type="hidden" id="info-key-name" value="<?= @$this->pk->name ?>" />
    <input type="hidden" id="info-key-value" value="<?= (int) @$row[$this->pk->name] ?>" />
    <input type="hidden" id="group-ids" name="group_ids" value="<?= @$ids ?>" />
    
    <?php
    foreach ($this->fields as $field) {
        if ($field->hidden) {
            echo $field->inputHidden($row[$field->name]);
        }
    }
    ?>

    <div class="row row-content">
        <div class="col-md-<?= $showRightCol ? 8 : 12 ?> col-main">
            <div class="portlet ">
                <div class="portlet-title">
                    <div class="caption">
                        <?php if (SFAdminCore::menuCurItem('icon')): ?>
                            <i class="icon-<?= SFAdminCore::menuCurItem('icon') ?>"></i>
                        <?php endif ?>
                        <?= SFAdminCore::menuCurItem('name') ?> &mdash; <?= $title ?>
                    </div>
                </div>
                <div class="portlet-body form">
                    <div class="form-responsitive form-horizontal">
                        <div class="form-body">

                            <?php foreach ($this->fields as $field): ?>
                                <?php if (!$field->hidden): ?>
                                    <div class="form-group">
                                        <label class="col-md-3 control-label">
                                            <?= $field->label ?>
                                            <?php if ($field->required): ?>
                                                <i class="red">*</i>
                                            <?php endif ?>
                                            <?php if ($isGroup): ?>
                                            <?php endif ?>
                                        </label>
                                        <div class="col-md-9" style="<?= $isGroup ? 'padding-left: 30px; position: relative' : '' ?>">
                                            <?php if ($isGroup): ?>
                                                <div style="position: absolute; top: 10px; left: 0">
                                                    <input class="group-set" type="checkbox" name="set[<?= $field->name ?>]" value="" title="Изменить это поле" />
                                                </div>
                                            <?php endif ?>
                                            <?= $this->formFieldInput($field, $row) ?>
                                            <span id="help-error-<?= $field->name ?>" class="help-block help-errors" style="display: none"></span>
                                            <?php if (!empty($field->help)): ?>
                                                <span class="help-block">
                                                    <?= $field->help ?>
                                                </span>
                                            <?php endif ?>
                                        </div>
                                    </div>
                                <?php endif ?>
                            <?php endforeach ?>

                            <div id="params-left">
                                <?php if (count($this->params['left'])) : ?>
                                    <?php
                                    $hasWithoutGroup = false;
                                    foreach ($this->params['left'] as $param) {
                                        if (isset($param['field'])) {
                                            $hasWithoutGroup = true;
                                            break;
                                        }
                                    }
                                    ?>
                                    <?php if ($hasWithoutGroup): ?>
                                        <div class="portlet " rel="left">
                                            <div class="portlet-title">
                                                <div class="caption">
                                                    <i class="fa fa-reorder"></i> Параметры
                                                </div>
                                            </div>
                                            <div class="portlet-body form">
                                                <div class="">
                                                    <div class="form-body">
                                                        <?php foreach ($this->params['left'] as $param): ?>
                                                            <?php if (isset($param['field'])): ?>
                                                                <?php $field = $param['field'] ?>
                                                                <?php SFField::setFieldValue($field, $group, $params, $row) ?>
                                                                <?php include 'form.field.tpl' ?>
                                                            <?php endif ?>
                                                        <?php endforeach ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endif ?>
                                    <?php foreach ($this->params['left'] as $group): ?>
                                        <?php if (count($group['fields'])): ?>
                                            <div class="portlet " rel="left">
                                                <div class="portlet-title">
                                                    <div class="caption">
                                                        <i class="fa fa-reorder"></i> <?= $group['label'] ?>
                                                    </div>
                                                </div>
                                                <div class="portlet-body form">
                                                    <div class="">
                                                        <div class="form-body">
                                                            <?php foreach ($group['fields'] as $field): ?>
                                                                <?php SFField::setFieldValue($field, $group, $params, $row) ?>
                                                                <?php include 'form.field.tpl' ?>
                                                            <?php endforeach ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endif ?>
                                    <?php endforeach ?>
                                <?php endif; ?>
                                
                            </div>

                        </div>

                    </div>
                </div>
            </div>
            
            <?php $this->portlets('left') ?>
            
        </div>

        <?php if ($showRightCol): ?>
            <div class="col-md-4">
                <?php if (count($this->params['right'])): ?>
                    <?php
                    $hasWithoutGroup = false;
                    foreach ($this->params['right'] as $param) {
                        if (isset($param['field'])) {
                            $hasWithoutGroup = true;
                            break;
                        }
                    }
                    ?>
                    <?php if ($hasWithoutGroup): ?>
                        <div class="portlet " rel="right">
                            <div class="portlet-title">
                                <div class="caption">
                                    <i class="fa fa-reorder"></i> Параметры
                                </div>
                            </div>
                            <div class="portlet-body form">
                                <div class="">
                                    <div class="form-body">
                                        <?php foreach ($this->params['right'] as $param): ?>
                                            <?php if (isset($param['field'])): ?>
                                                <?php $field = $param['field'] ?>
                                                <?php @SFField::setFieldValue($field, $group, $params, $row) ?>
                                                <?php include 'form.field.tpl' ?>
                                            <?php endif ?>
                                        <?php endforeach ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endif ?>
                    <?php foreach ($this->params['right'] as $group): ?>
                        <?php if (count($group['fields'])): ?>
                            <div class="portlet " rel="right">
                                <div class="portlet-title">
                                    <div class="caption">
                                        <i class="fa fa-reorder"></i> <?= $group['label'] ?>
                                    </div>
                                </div>
                                <div class="portlet-body form">
                                    <div class="">
                                        <div class="form-body">
                                            <?php foreach ($group['fields'] as $field): ?>
                                                <?php SFField::setFieldValue($field, $group, $params, $row) ?>
                                                <?php include 'form.field.tpl' ?>
                                            <?php endforeach ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endif ?>
                    <?php endforeach ?>
                    <div class="ajax-params">
                    </div>
                <?php endif ?>
                <?= $rightPortletsHTML ?>
            </div>
        <?php endif ?>
    </div>

    <div class="form-actions nobg">
        <div class="row">
            <div class="col-md-12">
                <button class="btn btn-success" name="submit_save" onclick="return modelFormCheck(this);"><i class="fa fa-check"></i> Сохранить</button>
                <?php if ($isAdd): ?>
                    <button class="btn btn-success" name="submit_save_add" onclick="return modelFormCheck(this);"><i class="fa fa-plus"></i> Сохранить и добавить</button>
                <?php endif ?>
                <?php if (!$isGroup && $this->canEdit): ?>
                    <button class="btn btn-primary" name="submit_apply" onclick="return modelFormCheck(this);"><i class="fa fa-check"></i> Применить</button>
                <?php endif ?>
                <a class="btn btn-default" href="./"><i class="fa fa-reply"></i> Отмена</a>
            </div>
        </div>
    </div>
    
</form>

<div aria-hidden="true" role="basic" tabindex="-1" id="modal-ajax" class="modal fade">								
    <div class="modal-dialog">
        <div class="modal-content">
            <div style="text-align: center; padding: 50px 0">
                <img class="loading" alt="" src="/admin/theme/img/ajax-modal-loading.gif">
            </div>
        </div>
    </div>
</div>

