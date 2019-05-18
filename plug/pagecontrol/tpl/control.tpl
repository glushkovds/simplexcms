<div class="plug-pagecontrol">
  <?php if($this->p==0) :?>
  <span class="plug-pagecontrol-left">←</span>
  <?php else :?>
  <a class="plug-pagecontrol-left" href="<?php echo str_replace('{p}', $this->p-1, $this->link)?>">←</a>
  <?php endif;?>

  <?php
  if($this->p_count > $this->p_count_max) {

    $i=0;
    echo $this->p==$i ? '<span>'.($i+1).'</span>' : '<a href="'.str_replace('{p}', $i, $this->link).'">'.($i+1).'</a>';
    if($since>1) {
      echo '<i>...</i>';
    }
    for($i=$since;$i<=$till;$i++) {
      echo $i==$this->p ? '<span>'.($i+1).'</span>' : '<a href="'.str_replace('{p}', $i, $this->link).'">'.($i+1).'</a>';
    }
    if($till<$this->p_count-2) {
      echo '<i>...</i>';
    }

    $i=$this->p_count-1;
    echo $this->p==$i ? '<span>'.($i+1).'</span>' : '<a href="'.str_replace('{p}', $i, $this->link).'">'.($i+1).'</a>';
  } else {
    for($i=0;$i<$this->p_count;$i++) {
      echo $i==$this->p ? '<span>'.($i+1).'</span>' : '<a href="'.str_replace('{p}', $i, $this->link).'">'.($i+1).'</a>';
    }
  }
  ?>
  
  <?php if($this->p >= $this->p_count-1) :?>
  <span class="plug-pagecontrol-left">→</span>
  <?php else :?>
  <a class="plug-pagecontrol-right" href="<?php echo str_replace('{p}', $this->p+1, $this->link)?>">→</a>
  <?php endif;?>
</div>

