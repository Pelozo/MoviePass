<?php include(VIEWS_PATH."header.php"); ?>
<main>
    <h1 class="indexTitle" style="margin-top:1em;">Cartelera</h1>
    <h2 style="margin:1em;">Ver Por: </h2>
    <form style="margin:2em;">
        <label for="genres">Genero:</label>
        <select name="genre" id="genres" onChange="showResult()">
            <option value="all">Todos</option>
            <?php foreach($genres as $genre){?>
            <option value="<?=$genre->getId();?>"><?=$genre->getName();?></option> 
        <?php } ?>
        </select>
    <input type="date" id="date" name="date" onChange="showResult()">

    <input type="button" id="reset" value="Reestablecer" onClick="clear()">
    </form>

    <div class="moviesList" id="moviesList">

    </div>
    <br>

</main>

<script>

//esto hace que no se puede elegir una fecha que ya pasó en el calendario
var now = new Date(),
minDate = now.toISOString().substring(0,10);
$('#date').prop('min', minDate);

//load movies when page is ready
$(document).ready(function(){
  showResult();
});

//button to clear inputs
$('#reset').click(function(){
  $('#genres').prop('selectedIndex', 0);
  $('#date').val('');
  showResult();
});

//function to retrieve movies from backend
function showResult(page = 1) {
  var xmlhttp=new XMLHttpRequest();
  xmlhttp.onreadystatechange=function() {
    if (this.readyState==4 && this.status==200) {
      //Clear all movies
      $('#moviesList').html("")

      var movies = JSON.parse(this.responseText);
      //console.log(movies);

      if(movies.length == 0){
        $('#moviesList').append('No se encontraron resultados');
      }else{
        //loop all movies
        for(var index in movies) {
          //if movie doesnt have a poster add a default one
          if(movies[index]['img'] == null){
            movies[index]['img'] = "<?=DEFAULT_POSTER?>"; 
          }

          //add movie to list
          $('#moviesList').append('<a href="<?=FRONT_ROOT?>show/showDetails/' + movies[index]['id'] + '" ><img class="img-responsive" style="margin:2em" width="200px" height="300px" src="' + movies[index]['img'] + '" alt="' + movies[index]['title'] + '" ></a>');


        }
      }    
      
    }
  }

  

  var genre = document.getElementById("genres").value;
  var date = document.getElementById("date").value


  xmlhttp.open("GET","<?= FRONT_ROOT?>movie/getShows/" + genre + "/" + date, true);
  xmlhttp.send();
}
</script>
<?php include(VIEWS_PATH."footer.php"); ?>
