<div class="ext-gallery-photos">
<?php foreach($photos as $row) :?>
  <div class="item">
    <a class="img" href="/uf/images/gallery/photos/<?php echo $row['image'];?>" title="<?php echo htmlspecialchars($row['name']);?>" rel="ext-gallery-photos-1"><img src="/uf/images/gallery/photos/200x150x1/<?php echo $row['image'];?>" alt="" /></a>
    <span><a href="/uf/images/gallery/photos/<?php echo $row['image'];?>" title="<?php echo htmlspecialchars($row['name']);?>" rel="ext-gallery-photos-2"><?php echo $row['name'];?></a></span>
  </div>
<?php endforeach;?>
</div>
<div style="clear:both"></div>
