<table>
  <tr>
    <?php foreach($rows as $i => $row) :?>
      <?php if($i) :?>
      <td style="width:35px"></td>
      <?php endif;?>
      <td>
        <div class="item">
          <?php if(!empty($this->params['date'])) :?>
          <span class="date"><?php echo $row['date'];?></span>
          <?php endif;?>
          <p><a href="<?php echo $row['path'];?>"><?php echo $row['title'];?></a></p>
          <?php if(!empty($this->params['short'])) :?>
          <div><?php echo $row['short'];?></div>
          <?php endif;?>
        </div>
      </td>
    <?php endforeach;?>
  </tr>
</table>