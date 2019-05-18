<ul class="body-block body-block-rows media-list<?php echo $rows ? '' : ' hide' ?>">
    <?php foreach ($rows as $row): ?>
        <?php include 'index.item.tpl' ?>
    <?php endforeach ?>
</ul>
<p class="body-block body-block-empty-files<?php echo $rows ? ' hide' : '' ?>">
    Пока нет ни одного файла
</p>
<div class="body-block body-block-loading hide" style="text-align: center; padding: 60px 0">
    <img src="/admin/theme/img/loader-windows.64.gif" />
    <h4>Пожалуйста, подождите...</h4>
</div>
<div class="files-buttons">
    <hr>
    <a class="btn btn-warning import-btn" href="javascript:;">
        <i class="fa fa-plus"></i>
        <span class="hidden-480"> Загрузить файл </span>
    </a>
    <div class="import-btn-form">
        <form method="post" enctype="multipart/form-data" action="?sf_module_id=<?php echo $this->id ?>&action=upload" onsubmit="return SFFile.onSubmit(this)">
            <input type="file" name="file" onchange="SFFile.onAttach(this)" />
            <input type="hidden" name="rel_name" value="<?php echo htmlspecialchars($relName) ?>" />
            <input type="hidden" name="rel_id" value="<?php echo $relId ?>" />
        </form>
    </div>
</div>



<input type="hidden" id="file-module-id" value="<?php echo $this->id ?>"/>
<input type="hidden" id="file-max-size" value="<?php echo $maxFileSize ?>" value-user="<?php echo $maxFileSizeUser ?>" />

<style>
    .import-btn-form{display: inline-block; position: relative}
    .import-btn-form input{position: absolute; left: -135px; top: -25px; width: 135px; height: 35px; z-index: 1; opacity: 0; cursor: pointer}
    .media-body .btn{visibility: hidden}
    .media-body:hover .btn{visibility: visible}
</style>