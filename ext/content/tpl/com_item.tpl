<?php if(empty($content['params']['content_main']['hide_title'])) :?>
<h1><?php echo $content['title']?></h1>
<?php endif;?>

<div class="editor-content">
<?php echo $content['text']?>
</div>

<?php if(count($children)) :?>
  <?php foreach($children as $row) :?>
    <div>
      <a href="<?php echo $row['path']?>"><?php echo $row['title']?></a>
      <p><?php echo $row['short']?></p>
    </div>
  <?php endforeach;?>
<?php endif; ?>
