<?php include(VIEWS_PATH."header.php"); ?>
<?php
foreach($movies as $movie){
    ?> <a class="movieButton" href="movie/getMovie/<?= $movie->getId()?>"><img src="<?= $movie->getImg(); ?>" alt="<?= $movie->getTitle();?>"  width="200px"></a>
<?php } 
?>
<?php include(VIEWS_PATH.'footer.php');?>