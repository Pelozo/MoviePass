<?php include(VIEWS_PATH."header.php"); ?>
<main class="">
<div style="margin-top:50px" class="container-fluid">
	<div class="row">
		<div class="col-sm-1"></div>
		<div class="col-sm-2"></div>		
		<div class="col-sm-6 bg-light boxStyle">
			<form name="theform" action="" method="POST">
                <?php if(isset($show)){?>
                    <input type="hidden" name="id" value="<?=$show->getId()?>">
                <?php }?>

                <div class="form-group">
                    <label>Pelicula<span class="asteriskField">*</span></label>
                    <div class="input-group mb-3" onclick="openSelectMovie()">
                      <div class="input-group-prepend">
                        <button class="btn btn-outline-secondary" type="button">Seleccionar</button>
                      </div>
                      <input id="movieTitle" type="text" class="form-control" placeholder="" aria-label="" aria-describedby="basic-addon1" disabled value="<?php if(isset($movie)) echo $movie->getTitle()?>">
                      <input id='movieId' type='hidden' name='movieId' value="<?php if(isset($movie)) echo $movie->getId()?>">
                    </div>
                    <br>
                    <div id="moviePoster"><img src="<?php if(isset($movie)) echo $movie->getImg()?>" width="150px"></div>

                <div class="form-group">
                    <label>Cine<span class="asteriskField">*</span></label>
                    <select name="cinemaId" id="cinemas" onchange="getRooms()">
                    <?php foreach($cinemas as $cinema){?>
                    
                    <option value="<?=$cinema->getId()?>"
                    <?php
                    if(isset($cinemaShow)){
                      if($cinema->getId() == $cinemaShow->getId()){
                        ?> selected
                      <?php } ?>
                    <?php } ?>><?=$cinema->getName()?></option>
                    <?php } ?>
                    </select>
                </div>
                <div class="form-group" display="none">
                    <label>Sala<span class="asteriskField">*</span></label>
                    <select name="roomId" id="rooms">
                    </select>
                </div>
                <div class="form-group">
                    <label>Fecha<span class="asteriskField">*</span></label>
                    <input type="datetime-local" id="time" name="time" value="<?php if(isset($date)) echo $date?>">
                </div>
            
                <?php if(isset($err)){?>
                    <?=$err?>
                <?php }?>

                <div class="form-group">
                    <div class="row">
                       <input class="btn btn-primary" type="submit"   value="Enviar" style="font-weight: bold">
	
                    </div>
                </div>
             </form>      
		</div>
		<div class="col-sm-1"></div>
		<div class="col-sm-2"></div>
    </div> 
</div> 

<!--modal-->
<div id="myModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title">Seleccionar pelicula</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
        </div> 
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

        <div class="moviesList" id="moviesList"></div>        
    </div>
  </div>
</div>



</main>
<script>
//load rooms when page is ready
$(document).ready(function(){
  getRooms();
});


function openSelectMovie(){
  //console.log("asd");
  $('#myModal').modal('show');
}

function selectMovie(id, title, image){
  console.log("select movie called" + id + ", " + title + ", " + image);
  movie = id;
  //close modal
  $('#myModal').modal('toggle');

  //display movie title on input
  $("#movieTitle").val(title);

  //remove previous poster if exists
  $("#moviePoster").empty();
  
  //display poster
  $("#moviePoster").append("<img src=\"" + image + "\" width=\"150px\" >");
  
  //set movie id
  $("#movieId").val(id);
}




function getRooms() {

    var cinema = document.getElementById("cinemas").value;

    //remove all options
    $('#rooms').empty()
    

    var xmlhttp=new XMLHttpRequest();
    xmlhttp.onreadystatechange=function() {
        if (this.readyState==4 && this.status==200) {

            //get all rooms from json
            var rooms = JSON.parse(this.responseText);
            <?php if(isset($room)){
             echo "var idRoom = " . $room->getId() . ";";
            } else {
              echo "var idRoom = -1";
            }?>

            //loop each room and add it to dropdown
            rooms.forEach(function(room){
              if(idRoom == room['id']){
                $('#rooms').append($('<option>',{
                    value: room['id'],
                    text : room['name'].charAt(0).toUpperCase() + room['name'].slice(1) + " (Capacidad: " + room['capacity'] + " | Precio: " + room['price'] + ")",
                    selected : true
                }));
              } else {
                $('#rooms').append($('<option>',{
                    value: room['id'],
                    text : room['name'].charAt(0).toUpperCase() + room['name'].slice(1) + " (Capacidad: " + room['capacity'] + " | Precio: " + room['price'] + ")"
                }));
              }
            });                
        }
    } 
  xmlhttp.open("GET","<?= FRONT_ROOT?>room/getByCinema/" + cinema, true);
  xmlhttp.send();
}



$(".movieButton").attr("href", "#");

$(".movieButton").on('click', function(event){
    console.log(event);    
});






</script>
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

      //parse movies from json
      var movies = JSON.parse(this.responseText);
      
      if(movies.length == 0){
        $('#moviesList').append('No se encontraron resultados');
      }else{
        //loop all movies
        for(var index in movies) {
          //if movie doesnt have a poster add a default one
          if(movies[index]['img'] == null || movies[index]['img'] == 'http://image.tmdb.org/t/p/w500'){
            movies[index]['img'] = "<?=DEFAULT_POSTER?>"; 
          }

          //add movie to list
          $('#moviesList').append('<a href="#" onclick="selectMovie(' + movies[index]['id'] + ',\'' + movies[index]['title'] + '\', \'' + movies[index]['img'] + '\')"><img class="img-responsive" style="max-width: 20%" src="' + movies[index]['img'] + '" alt="' + movies[index]['title'] + '" ></a>');
          
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