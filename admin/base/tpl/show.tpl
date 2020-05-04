<?php
$colRowActionsWidth = 14 + 32 * $this->rowActionsCnt;
?>

<?php AdminPlugAlert::output() ?>

<div class="portlet">
    <div class="portlet-title">
        <div class="caption">
            <?php if (SFAdminCore::menuCurItem('icon')): ?>
                <i class="icon-<?php echo SFAdminCore::menuCurItem('icon') ?>"></i>
            <?php endif ?>
            <?php echo $this->title ?>
        </div>
        <div class="actions">

            <?php $this->showActions() ?>

            <div aria-hidden="true" role="basic" tabindex="-1" id="delete-dialog" class="modal fade"
                 style="display: none;">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button aria-hidden="true" data-dismiss="modal" class="close" type="button"></button>
                            <h4 class="modal-title">Удаление записей</h4>
                        </div>
                        <div class="modal-body">
                            <i>Вы уверены, что хотите удалить выдленные элементы без возможности востановления?</i>
                        </div>
                        <div class="modal-footer">
                            <button data-dismiss="modal" class="btn btn-default" type="button">Отмена</button>
                            <button class="btn btn-danger" onclick="deleteRowsForce()" type="button">Удалить</button>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
    <div class="portlet-body">

        <div class="filter-extra">
            <?php $this->filterExtra() ?>
        </div>
        <div class="clearfix"></div>

        <div class="table-container" style="">

            <div id="datatable_ajax_wrapper" class="dataTables_wrapper dataTables_extended_wrapper no-footer">
                <div class="table-scrollable">
                    <form id="model-show-form" method="post" action="./?p=0">
                        <div class="table-data">

                            <!-- Заголовки -->
                            <div class="table-data-head">
                                <table cellspacing="0" class="table">
                                    <col style="width:50px"/>
                                    <?php foreach ($this->fields as $field): ?>
                                        <?php if ($field->isVisible): ?>
                                            <col class="<?php echo @$field->params['screen_width'] ? 'screen-width-' . $field->params['screen_width'] : '' ?>"
                                                 style="<?php echo implode('; ', $field->styleCol) ?>"/>
                                        <?php endif ?>
                                    <?php endforeach ?>
                                    <col class="col-row-actions" style="width: <?php echo $colRowActionsWidth ?>px"/>
                                    <tr class="heading">
                                        <td style="text-align:center; padding: 8px">
                                            <input type="checkbox"
                                                   id="model-check-all"
                                                   onclick="modelCheckAll(this)"/>
                                        </td>
                                        <?php foreach ($this->fields as $field): ?>
                                            <?php if ($field->isVisible): ?>
                                                <td class="<?php echo @$field->params['screen_width'] ? 'screen-width-' . $field->params['screen_width'] : '' ?>">
                                                    <a class="a-title" href="?o=<?php echo $field->name ?>">
                                                        <span<?php echo $field->name == $this->order ? ' class="order' . $this->desc . '"' : '' ?>>
                                                            <?php echo $field->label ?>
                                                        </span>
                                                    </a>
                                                </td>
                                            <?php endif ?>
                                        <?php endforeach ?>
                                        <td>&nbsp;</td>
                                    </tr>
                                </table>
                            </div>

                            <!-- Фильтры -->
                            <div class="table-data-head table-data-head-filter">
                                <table cellspacing="0" class="table">
                                    <col style="width:50px"/>
                                    <?php foreach ($this->fields as $field): ?>
                                        <?php if ($field->isVisible): ?>
                                            <col class="<?php echo @$field->params['screen_width'] ? 'screen-width-' . $field->params['screen_width'] : '' ?>"
                                                 style="<?php echo implode('; ', $field->styleCol) ?>"/>
                                        <?php endif ?>
                                    <?php endforeach ?>
                                    <col class="col-row-actions" style="width: <?php echo $colRowActionsWidth ?>px"/>
                                    <tr class="filter">
                                        <td>&nbsp;</td>
                                        <?php foreach ($this->fields as $field): ?>
                                            <?php if ($field->isVisible): ?>
                                                <td class="<?php echo @$field->params['screen_width'] ? 'screen-width-' . $field->params['screen_width'] : '' ?>">
                                                    <?= $this->filterField($field) ?>
                                                </td>
                                            <?php endif ?>
                                        <?php endforeach ?>
                                        <td>
                                            <a href="javascript:;" onclick="resetFilter()"
                                               class="btn btn-xs btn-danger">Сброс</a>
                                        </td>
                                    </tr>
                                </table>
                            </div>

                            <!-- Данные -->
                            <div id="table-data-body" class="table-data-body" style="overflow:auto;overflow-y:scroll">
                                <table cellspacing="0" class="table">
                                    <col style="width:50px"/>
                                    <?php foreach ($this->fields as $field): ?>
                                        <?php if ($field->isVisible): ?>
                                            <col class="<?php echo @$field->params['screen_width'] ? 'screen-width-' . $field->params['screen_width'] : '' ?>"
                                                 style="<?php echo implode('; ', $field->styleCol) ?>"/>
                                        <?php endif ?>
                                    <?php endforeach ?>
                                    <col class="col-row-actions" style="width: <?php echo $colRowActionsWidth ?>px"/>
                                    <?php foreach ($rows as $row): ?>
                                        <tr id="row-<?php echo $row[$this->pk->name] ?>"
                                            class="<?php echo $saveId === (int)$row[$this->pk->name] ? 'row-save' : '' ?>">
                                            <td style="text-align:center">
                                                <input class="model-row-check" type="checkbox" name="row[]"
                                                       value="<?php echo $row[$this->pk->name] ?>"
                                                       onchange="modelCheck(this)"/>
                                            </td>
                                            <?php foreach ($this->fields as $field): ?>
                                                <?php if ($field->isVisible): ?>
                                                    <td class="<?php echo @$field->params['screen_width'] ? 'screen-width-' . $field->params['screen_width'] : '' ?>"
                                                        id="field-<?php echo $field->name, '-', $row[$this->pk->name] ?>"
                                                        style="<?php echo $field->styleCell ?>"><?php echo $this->showCell($field, $row) ?></td>
                                                <?php endif ?>
                                            <?php endforeach ?>
                                            <td class="tac">
                                                <div class="row-actions">
                                                    <?php if ($this->canEdit): ?>
                                                        <?php $this->rowActions($row) ?>
                                                    <?php endif ?>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach ?>
                                </table>
                            </div>


                        </div>

                    </form>

                </div>

                <div class="">
                    <?php echo $pagecontrol->content(); ?>
                </div>


            </div>
        </div>
    </div>
</div>

<div aria-hidden="true" role="basic" tabindex="-1" id="modal-ajax" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <div style="text-align: center; padding: 50px 0">
                <img class="loading" alt="" src="/admin/theme/img/ajax-modal-loading.gif">
            </div>
        </div>
    </div>
</div>

<input type="hidden" id="save-id" value="<?php echo $saveId ?>"/>

<style>
    <?php
    foreach ($this->fields as $field) {
        if ($sw = (string) @$field->params['screen_width']) {
            echo "@media screen and (max-width: {$sw}px) {.screen-width-$sw{display: none}}\n";
        }
    }
    ?>
</style>
