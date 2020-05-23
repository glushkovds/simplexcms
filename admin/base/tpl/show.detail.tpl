<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
    <h2 class="modal-title">ID <?php echo $row[$this->pk->name] ?></h2>
</div>
<div class="modal-body">

    <div class="table-responsive">
        <table class="table table-hover">
            <tbody>
            <?php foreach (array_keys($row) as $key): ?>
                <?php
                try {
                    $value = $this->showDetailPrepareValue($row, $key);
                } catch (Exception $e) {
                    if ($e->getCode() == 999) {
                        continue;
                    }
                }
                ?>
                <tr>
                    <td><?= $this->fields[$key]->label ?></td>
                    <td><?= $value ?></td>
                </tr>
            <?php endforeach ?>
            </tbody>
        </table>
    </div>

</div>
<div class="modal-footer">
    <button type="button" data-dismiss="modal" class="btn btn-info">Закрыть</button>
</div>
