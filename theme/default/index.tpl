<!DOCTYPE html>
<html lang="ru">
    <head>
        <?php
        PlugJQuery::jquery();
        SFPage::css('/theme/default/css/default.css');
        SFPage::js('/theme/default/js/default.js');
        SFPage::meta();
        ?>
    </head>
    <body>
        <?php SFPage::position('content-before'); ?>
        <?php SFPage::content(); ?>
        <?php SFPage::position('content-after'); ?>

        <?php SFPage::position('absolute'); ?>
    </body>
</html>
