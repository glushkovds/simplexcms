<div class="download-block">
    <?php foreach ($rows as $doc): ?>
        <div class="download-item download-item-<?php echo self::fileExtension($doc['file']) ?>">
            <span>
                <span>
                    <a class="<?php echo self::isImage("$dir{$doc['file']}") ? 'fancybox' : '' ?>" title="<?php echo htmlspecialchars($doc['title']) ?>" href="<?php echo self::getDownloadURL("$dir{$doc['file']}", $doc['title']) ?>" target="_blank"><?php echo $doc['title'] ?></a>
                    <span>(<?php echo self::fileSize("$dir{$doc['file']}") ?>)</span>
                </span>
            </span>
        </div>
    <?php endforeach ?>
</div>