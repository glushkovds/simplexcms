
<div class="portlet portlet-rights" rel="right">
    <div class="portlet-title">
        <div class="caption">
            <i class="fa fa-reorder"></i> Права
        </div>
    </div>
    <div class="portlet-body form form-horizontal">
        <div class="form-body">
            <div class="form-horizontal">
                <div class="form-body">
                    <div class="form-group">
                        <label class="col-md-3 control-label">Добавление</label>
                        <div class="col-md-9">
                            <select name="" class="form-control">
                                <option value="">---</option>
                                <?php foreach ($privs as $priv): ?>
                                    <option value="<?php echo $priv['priv_id'] ?>"><?php echo $priv['name'] ?></option>
                                <?php endforeach ?>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <div>&nbsp;</div>
            <div class="btn-group btn-group-sm">
                <a href="javascript:;" onclick="StructTable.clearRights()" class="btn btn-danger">Очистить права</a>
            </div>
            <div>&nbsp;</div>
        </div>
    </div>
</div>