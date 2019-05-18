<li class="dropdown" id="header_notification_bar">
    <a href="#" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
        <i class="icon-bell"></i>
        <?php if ($cnt): ?>
            <span class="badge badge-success"> <?php echo $cnt ?> </span>
        <?php endif ?>
    </a>
    <?php if ($cnt): ?>
        <ul class="dropdown-menu extended notification">
            <li>
                <p>
                    <?php echo 'У тебя ' . $cnt . ' ' . PlugDeclension::byCount($cnt, 'новое уведомление', 'новых уведомлений', 'новых уведомлений') ?>
                </p>
            </li>
            <li>
                <ul class="dropdown-menu-list scroller">
                    <?php foreach ($this->items as $item): ?>
                        <?php $item->html() ?>
                    <?php endforeach ?>
                    <!--                <li>
                                        <a href="#">
                                            <span class="label label-sm label-icon label-danger">
                                                <i class="fa fa-bolt"></i>
                                            </span>
                                            Server #12 overloaded. <span class="time">
                                                15 mins </span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="#">
                                            <span class="label label-sm label-icon label-warning">
                                                <i class="fa fa-bell"></i>
                                            </span>
                                            Server #2 not responding. <span class="time">
                                                22 mins </span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="#">
                                            <span class="label label-sm label-icon label-info">
                                                <i class="fa fa-bullhorn"></i>
                                            </span>
                                            Application error. <span class="time">
                                                40 mins </span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="#">
                                            <span class="label label-sm label-icon label-danger">
                                                <i class="fa fa-bolt"></i>
                                            </span>
                                            Database overloaded 68%. <span class="time">
                                                2 hrs </span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="#">
                                            <span class="label label-sm label-icon label-danger">
                                                <i class="fa fa-bolt"></i>
                                            </span>
                                            2 user IP blocked. <span class="time">
                                                5 hrs </span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="#">
                                            <span class="label label-sm label-icon label-warning">
                                                <i class="fa fa-bell"></i>
                                            </span>
                                            Storage Server #4 not responding. <span class="time">
                                                45 mins </span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="#">
                                            <span class="label label-sm label-icon label-info">
                                                <i class="fa fa-bullhorn"></i>
                                            </span>
                                            System Error. <span class="time">
                                                55 mins </span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="#">
                                            <span class="label label-sm label-icon label-danger">
                                                <i class="fa fa-bolt"></i>
                                            </span>
                                            Database overloaded 68%. <span class="time">
                                                2 hrs </span>
                                        </a>
                                    </li>-->
                </ul>
            </li>
            <!--        <li class="external">
                        <a href="#">See all notifications <i class="fa fa-angle-right"></i></a>
                    </li>-->
        </ul>
    <?php endif ?>
</li>