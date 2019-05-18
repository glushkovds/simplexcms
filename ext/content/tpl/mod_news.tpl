<div class="module-contentnews">
    <div class="module-body">
        <?php foreach ($rows as $row) : ?>
            <div class="item">
                <div class="date"><span><?php echo PlugTime::convert($row['date'], '<b>d</b> K') ?></span></div>
                <a href="<?php echo $row['path'] ?>" class="title"><?php echo $row['title'] ?></a>
            </div>
        <?php endforeach ?>
    </div>
</div>