
<?php foreach ($rows as $row): ?>
    <<?php echo $row['href'] ? 'a href="'.$row['href'].'"' : 'div' ?> class="slide">
    <span class="img" style="background-image: url('/uf/images/slider/large/<?php echo $row['photo'] ?>')"></span>
    <span class="text">
        <span class="inner">
            <?php echo $row['text'] ?>
        </span>
    </span>
    </<?php echo $row['href'] ? 'a' : 'div' ?>>
<?php endforeach ?>
