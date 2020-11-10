<?php include(VIEWS_PATH."header.php"); ?>
<main>
  <br>
    Buscar por: 
    <form method="GET" >
        <label for="genres">Genero:</label>
        <select name="genre" id="genres" onchange="showResult()">
            <option value="all">Todos</option>
            <?php foreach($genres as $genre){?>
            <option value="<?=$genre->getId();?>"><?=$genre->getName();?></option> 
        <?php } ?>
        </select>

        <label for="year">Año:</label>
        <select name="year" id="year" onchange="showResult()">
            <option value="all">Todos</option>
            <?php foreach($years as $year){?>
            
            <option value="<?=$year?>"><?=$year?></option> 
        <?php } ?>
        </select>
        <input type="text" id="name" size="30" onkeyup="showResult()">
    </form>

    <div class="moviesList" id="moviesList">

    </div>
    <br>
    <!--
    <nav>
      <ul class="pagination justify-content-center">
  
        <li class="page-item">
          <a class="page-link" href="#" >Previous</a>
        </li>
        <li class="page-item">
          <a class="page-link" href="#">Next</a>
        </li>
    </ul>
    </nav>
    -->
</main>
<script>

//load movies when page is ready
$(document).ready(function(){
  showResult();
});

function showResult(page = 1) {
  var xmlhttp=new XMLHttpRequest();
  xmlhttp.onreadystatechange=function() {
    if (this.readyState==4 && this.status==200) {
      //Clear all movies
      $('#moviesList').html("")

      var movies = JSON.parse(this.responseText);

      if(movies.length == 0){
        $('#moviesList').append('No se encontraron resultados');
      }else{
        for(var index in movies) {
          console.log(index, movies[index]);
          $('#moviesList').append('<img class="img-responsive" style="max-width: 6%" src="' + movies[index]['img_movie'] + '" alt="' + movies[index]['title_movie'] + '" >');
        }
      }    
      
    }
  }


  var genre = document.getElementById("genres").value;
  var year = document.getElementById("year").value;
  var name = document.getElementById("name").value;
  if(!name){
    name = "all";
  }
 
  xmlhttp.open("GET","<?= FRONT_ROOT?>movie/getMovies/" + genre + "/" + year + "/" + name + "/" + page, true);
  xmlhttp.send();
}
</script>
<?php include(VIEWS_PATH."footer.php"); ?>