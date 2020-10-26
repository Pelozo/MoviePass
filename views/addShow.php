<main class="">
<div style="margin-top:50px" class="container-fluid">
	<div class="row">
		<div class="col-sm-1"></div>
		<div class="col-sm-2"></div>		
		<div class="col-sm-6 bg-light boxStyle">
			<form name="theform" action="" method="get">
                <?php if(isset($cinema)){?>
                    <input type="hidden" name="id" value="<?=$cinema->getId()?>">
                <?php }?>

                <div class="form-group">
                    <label >Pelicula<span class="asteriskField">*</span></label>
                    <div class="input-group mb-3" onclick="openSelectMovie()">
                      <div class="input-group-prepend">
                        <button class="btn btn-outline-secondary" type="button">Seleccionar</button>
                      </div>
                      <input id="movieTitle" type="text" class="form-control" placeholder="" aria-label="" aria-describedby="basic-addon1" disabled>
                    </div>
                    <br>
                    <div id="moviePoster"></div>

                <input type="hidden" name="cinemaId" id="cinemaId">

                <div class="form-group">
                    <label>Cine<span class="asteriskField">*</span></label>
                    <select name="cinemas" id="cinemas" onchange="getRooms()">
                    <?php foreach($cinemas as $cinema){?>
                    
                    <option value="<?=$cinema->getId()?>"><?=$cinema->getName()?></option>
                    <?php } ?>
                    </select>
                </div>
                <div class="form-group" display="none">
                    <label>Sala<span class="asteriskField">*</span></label>
                    <select name="rooms" id="rooms">
                    </select>
                </div>
                <div class="form-group">
                    <label>Fecha<span class="asteriskField">*</span></label>
                    <input type="datetime-local" id="time" name="time">
                </div>
                <input type="hidden" name="idCinema" value="<?=$idCinema?>">
                <input type="hidden" name="id" value="<?php if(isset($room)) echo $room->getId()?>">
                <?php if(isset($error)){?>
                    <?=$error?>
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
  console.log("asd");
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
  //set cinema id
  $("#cinemaId").val(id);
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

            //loop each room and add it to dropdown
            rooms.forEach(function(room){
                $('#rooms').append($('<option>',{
                    value: room['id'],
                    text : room['name'].charAt(0).toUpperCase() + room['name'].slice(1) + " (Capacidad: " + room['capacity'] + " | Precio: " + room['price'] + ")"
                }));
            });                
        }
    } 
 
  xmlhttp.open("GET","<?= FRONT_ROOT?>room/getByCinema/" + cinema, true);
  xmlhttp.send();
}



//$("a").attr("href", "http://www.google.com/")



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

      var movies = JSON.parse(this.responseText);

      if(movies.length == 0){
        $('#moviesList').append('No se encontraron resultados');
      }else{
        for(var index in movies) {
          console.log(index, movies[index]);
          $('#moviesList').append('<a href="#" onclick="selectMovie(' + movies[index]['id_movie'] + ',\'' + movies[index]['title_movie'] + '\', \'' + movies[index]['img_movie'] + '\')"><img class="img-responsive" style="max-width: 20%" src="' + movies[index]['img_movie'] + '" alt="' + movies[index]['title_movie'] + '" ></a>');
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