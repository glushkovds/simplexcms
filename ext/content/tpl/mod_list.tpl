<ul>
    <?php foreach ($rows as $row) : ?>
        <li>
            <?php if (!empty($this->params['date'])) : ?>
                <span class="date"><?php echo PlugFunc::dateFormat($row['date']) ?></span>
            <?php endif ?>
            <h3><a href="<?php echo $row['path'] ?>"><?php echo $row['title'] ?></a></h3>
            <?php if (!empty($this->params['short'])) : ?>
                <div class="short"><?php echo $row['short'] ?></div>
            <?php endif ?>
            <?php if (!empty($this->params['more'])) : ?>
                <div class="more">
                    <a href="<?php echo $row['path'] ?>"><?php echo $this->params['more_text'] ? : 'Подробнее' ?></a>
                </div>
            <?php endif ?>
        </li>
    <?php endforeach; ?>
</ul>
