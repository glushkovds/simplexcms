<div class="ext-gallery-albums">
<?php foreach($albums as $row) :?>
  <div class="item">
    <a class="img" href="<?php echo $this->link('/'.$row['alias'].'/');?>"><img src="/uf/images/gallery/albums/300x225x1/<?php echo $row['image'];?>" alt="" /></a>
    <span><a href="<?php echo $this->link('/'.$row['alias'].'/');?>"><?php echo $row['name'];?></a></span>
  </div>
<?php endforeach;?>
</div>
<div style="clear:both"></div>
