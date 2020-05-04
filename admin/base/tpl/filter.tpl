<div id="admin-filter">
    <form id="admin-filter-form" method="post" action="./">
        <table>
            <tr>
                <td>
                    <?php foreach ($this->fields as $field): ?>
                        <?= $this->filterField($field) ?>
                    <?php endforeach ?>
                </td>
            </tr>
        </table>
    </form>
</div>
