<div class="portlet <?php echo @$portletClass ?>" rel="right">
    <div class="portlet-title">
        <div class="caption">
            <i class="fa fa-reorder"></i> <?php echo $group['label'] ?>
        </div>
    </div>
    <div class="portlet-body form">

        <div class="form-body">
            <?php foreach ($group['fields'] as $field): ?>
                <?php include 'form.field.tpl' ?>
            <?php endforeach ?>
        </div>
    </div>
</div>