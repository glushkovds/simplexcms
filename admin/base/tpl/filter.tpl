<div id="admin-filter">
  <form id="admin-filter-form" method="post" action="./">
    <table><tr>
      <td>
        <?php
          foreach($this->fields as $field) {
            $field->filter($_SESSION[$this->table]['filter'][$field->name]);
          }
        ?>
        <!--<button name="filter[submit]" onfocus="blur()">→</button>-->
      </td>
    </tr></table>
  </form>
</div>
