<ul class="page-breadcrumb module-<?php echo $this->name ?>" id="module-id-<?php echo $this->id ?>">
    <li>
        <i class="fa fa-home"></i>
        <a href="/admin/">Главная</a>
        <i class="fa fa-angle-right"></i>
    </li>

    <li>
        <?php echo join(' <i class="fa fa-angle-right"></i> </li><li> ', $links); ?>
    </li>
</ul>

