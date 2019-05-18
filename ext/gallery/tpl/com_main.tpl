<h1><?php echo $title;?></h1>
<?php if($content) :?>
<p><?php echo $content['short'];?></p>
<?php endif; ?>

<?php $this->albums($albums);?>

<?php if($content) :?>
<div><?php echo $content['text'];?></div>
<?php endif; ?>