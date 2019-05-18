<?php
$mapping = [
    'xls' => 'file-excel-o',
    'xlsx' => 'file-excel-o',
    'csv' => 'file-excel-o',
    'doc' => 'file-word-o',
    'docx' => 'file-word-o',
    'pdf' => 'file-pdf-o',
    'zip' => 'file-archive-o',
    'rar' => 'file-archive-o',
    'gz' => 'file-archive-o',
    'mov' => 'file-video-o',
    'avi' => 'file-video-o',
    'mp4' => 'file-video-o',
    'mkv' => 'file-video-o',
    'mp3' => 'file-audio-o',
    'mp3' => 'file-audio-o',
    'ppt' => 'file-powerpoint-o',
    'pptx' => 'file-powerpoint-o',
    'ico' => 'file-image-o',
    'png' => 'file-image-o',
    'jpg' => 'file-image-o',
    'jpeg' => 'file-image-o',
    'bmp' => 'file-image-o',
    'txt' => 'file-text-o',
    'exe' => 'file-code-o',
];
$icon = @$mapping[$row['ext']] ?: 'file-o';
?>
<li class="media" file-id="<?php echo $row['file_id'] ?>">
    <a class="pull-left" href="<?php echo $row['url'] ?>">
        <i class="fa fa-<?php echo $icon ?>" style="font-size: 32px; margin-top: 15px"></i>
    </a>
    <div class="media-body">
        <h4 class="media-heading"><a href="<?php echo $row['url'] ?>"><?php echo $row['name'] ?></a></h4>
        <p>
            Размер: <?php echo $row['size_user'] ?>
            &nbsp; <button type="button" class="btn btn-danger btn-xs" onclick="SFFile.delete(<?php echo $row['file_id'] ?>)">Удалить</button>
        </p>
    </div>
</li>