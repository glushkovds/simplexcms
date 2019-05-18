
<div class="page-sidebar-wrapper">
    <div class="page-sidebar navbar-collapse collapse">

        <ul class="page-sidebar-menu">
            <li class="sidebar-toggler-wrapper">
                <!-- BEGIN SIDEBAR TOGGLER BUTTON -->
                <div class="sidebar-toggler">
                </div>
                <div class="clearfix">
                </div>
                <!-- BEGIN SIDEBAR TOGGLER BUTTON -->
            </li>
            <li class="sidebar-search-wrapper">
                <form class="search-form" role="form" action="index.html" method="get">
                    <div class="input-icon right">
                        <i class="fa fa-search"></i>
                        <input type="text" class="form-control input-sm" name="query" placeholder="Search...">
                    </div>
                </form>
            </li>

            <?php
            foreach ($menu[0] as $id => $item) {
                if ($item['hidden']) {
                    continue;
                }
                $c = array();
                $hasChildren = isset($menu[$id]);
                $hasChildren ? $c[] = 'has-children' : '';
                substr(SFAdminCore::path(), 0, strlen($item['link'])) === $item['link'] ? $c[] = 'active' : '';
//                if ($hasChildren) {
//                    $item['link'] = 'javascript:;';
//                }
                echo '<li', count($c) ? ' class="' . join(' ', $c) . '"' : '', '>';
                echo '<a href="', $item['link'], '">';
                if ($item['icon']) {
                    echo '<i class="icon-' . $item['icon'] . '"></i> ';
                }
                echo '<span class="title">' . $item['name'] . '</span>';
                if ($hasChildren) {
                    echo ' <span class="arrow"></span>';
                }
                echo '</a>';

                if (isset($menu[$id])) {
                    echo '<ul class="sub-menu">';
                    foreach ($menu[$id] as $id2 => $item) {
                        if ($item['hidden']) {
                            continue;
                        }
                        $c = array();
                        $hasChildren2 = isset($menu[$id2]);
                        isset($menu[$id2]) ? $c[] = 'has-children' : '';
                        substr(SFAdminCore::path(), 0, strlen($item['link'])) === $item['link'] ? $c[] = 'active' : '';
                        echo '<li', count($c) ? ' class="' . join(' ', $c) . '"' : '', '>';

                        echo '<a href="', $item['link'], '">';
                        if ($item['icon']) {
                            echo '<i class="icon-' . $item['icon'] . '"></i> ';
                        }
                        if ($hasChildren2) {
                            echo ' <span class="arrow"></span>';
                        }
                        echo '<span class="title">' . $item['name'] . '</span>';
                        echo '</a>';

                        if (isset($menu[$id2])) {
                            echo '<ul class="sub-menu">';
                            foreach ($menu[$id2] as $id3 => $item) {
                                if ($item['hidden']) {
                                    continue;
                                }
                                $c = array();
                                //isset($menu[$id3]) ? $c[] = 'has-children' : '';
                                $item['link'] == SFAdminCore::path() ? $c[] = 'active' : '';
                                echo '<li', count($c) ? ' class="' . join(' ', $c) . '"' : '', '>';

                                echo '<a href="', $item['link'], '">';
                                if ($item['icon']) {
                                    echo '<i class="icon-' . $item['icon'] . '"></i> ';
                                }
                                echo '<span class="title">' . $item['name'] . '</span>';
                                echo '</a>';

                                echo '</li>';
                            }
                            echo '</ul>';
                        }
                        echo '</li>';
                    }
                    echo '</ul>';
                }
                echo '</li>';
            }
            ?>

        </ul>

        <!-- END SIDEBAR MENU -->
    </div>
</div>