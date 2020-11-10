
<main>
    <h1 class="indexTitle">Cartelera</h1>
    <h2>Ver Por: </h2>
    <form method="GET" >
        <label for="genres">Genero:</label>
        <select name="genre" id="genres" onchange="showResult()">
            <option value="all">Todos</option>
            <?php foreach($genres as $genre){?>
            <option value="<?=$genre->getId();?>"><?=$genre->getName();?></option> 
        <?php } ?>
        </select>
    </form>

    <input type="date" id="time" name="time" onChange="updateMovies()">

    <div class="moviesList" id="moviesList">

    </div>
    <br>
    <nav>
      <ul class="pagination justify-content-center">
        <?php if($offset != 0){?>
        <li class="page-item">
          <a class="page-link" href="<?= FRONT_ROOT . "movie/displayBillboard/" . $genreRequired. "/" . $yearRequired . "/" . ($page-1)?>" >Previous</a>
        </li>
        <?php } ?>
        <?php //this could probably be optimized 
        for($i=$page-3; $i < $page;$i++){
          if($i > 0 ){?>
          <li class="page-item">
            <a class="page-link" href="<?= FRONT_ROOT . "movie/displayBillboard/" . $genreRequired. "/" . $yearRequired . "/" . $i?>"><?=$i?></a>
          </li>
        <?php } }?>

          <li class="page-item active">
            <a class="page-link" href=""><?=$page?></a>
          </li>

          <?php for($i=$page+1; $i < $page+3;$i++){
          if($i <= $totalPages ){?>
          <li class="page-item">
            <a class="page-link" href="<?= FRONT_ROOT . "movie/displayBillboard/" . $genreRequired. "/" . $yearRequired . "/" . $i?>"><?=$i?></a>
          </li>
        <?php } }?>


        <?php if($page < $totalPages){?>
        <li class="page-item">
          <a class="page-link" href="<?= FRONT_ROOT . "movie/displayBillboard/" . $genreRequired. "/" . $yearRequired . "/" . ($page+1)?>">Next</a>
        </li>
        <?php }?>
    </ul>
    </nav>
</main>
<script>
function showResult() {

  var xmlhttp=new XMLHttpRequest();
  xmlhttp.onreadystatechange=function() {
    if (this.readyState==4 && this.status==200) {
      //Clear all movies
      $('#moviesList').html("")

      var movies = JSON.parse(this.responseText);

      for(var index in movies) {
        console.log(index, movies[index]);
        $('#moviesList').append('<a class="movieButton" href="<?=FRONT_ROOT?>movie/details/' + movies[index]['id'] + '"><img class="img-responsive" style="max-width: 10%" src="' + movies[index]['img'] + '" alt="' + movies[index]['title'] + '" ></a>');
      }

      
    }
  }

  var genre = document.getElementById("genres").value;
  var year = document.getElementById("year").value;
  var name = document.getElementById("name").value;
 
  xmlhttp.open("GET","<?= FRONT_ROOT?>movie/getMovies/" + genre + "/" + year + "/" + name,true);
  xmlhttp.send();
}
</script>