<div class="widget">
    <div class="portlet">
        <div class="portlet-title">
            <div class="caption">
                <i class="icon-bar-chart"></i><?php echo $this->data['name'] ?>
            </div>
            <?php $this->actions() ?>
            <!--            <div class="actions">
                            <div data-toggle="buttons" class="btn-group">
                                <label class="btn btn-default btn-sm active">
                                    <input type="radio" class="toggle" name="options">New </label>
                                <label class="btn btn-default btn-sm">
                                    <input type="radio" class="toggle" name="options">Returning </label>
                            </div>
                        </div>-->
        </div>
        <div class="portlet-body">
            <?php echo $content ?>
        </div>
    </div>
    <input type="hidden" class="widget-id" value="<?php echo $this->id ?>" />
</div>