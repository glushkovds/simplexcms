<div class="portlet col-md-4">
    <div class="portlet-title">
        <div class="caption">
            <i class="fa fa-reorder"></i> Результаты загрузки
        </div>
    </div>
    <div class="portlet-body form">
        <form class="form-horizontal" method="get">
            <div class="form-body">

                <p>Архив успешно распознан, запустить установку?</p>
                <input type="hidden" name="installdir" value="<?php echo urlencode($this->installDir) ?>" />
                <input type="hidden" name="confirm" value="1" />

            </div>
            <div class="form-actions">
                <div class="row">
                    <div class="col-md-offset-3 col-md-9">
                        <button class="btn btn-success" type="submit">Подтвердить</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
