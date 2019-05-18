<table style="margin-top:<?php echo $cur_id ? 0 : 14 ?>px;table-layout:auto;width:auto">
    <tr>
        <?php
        foreach ($menu[$cur_id] as $id => $item) {
            if ($item['hidden']) {
                continue;
            }
            echo '<td style="padding-right:70px;white-space:nowrap; vertical-align: top">';
            echo '<a class="black" href="', $item['link'], '">', $item['name'], '</a>';
            if (isset($menu[$id])) {
                foreach ($menu[$id] as $item) {
                    if ($item['hidden']) {
                        continue;
                    }
                    echo '<p><a href="', $item['link'], '">', $item['name'], '</a></p>';
                }
            }
            echo '</td>';
        }
        ?>
    </tr>
</table>
