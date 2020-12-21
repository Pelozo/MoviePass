<?php include(VIEWS_PATH."header.php"); ?>


<main class="">
<div style="margin-top:50px" class="container-fluid">
	<div class="row ">		
		<div class="col-sm-12 bg-light justify-content-center text-center">
                   
                    <label>Pelicula</label>
                    <div class="" >

                        <button class="btn btn-outline-secondary" type="button" onclick="openSelectMovie()">Seleccionar</button>

                      <input id="movieTitle" type="text" disabled>
                      <input id='movieId' type='hidden'>
                      <button class="btn btn-outline-secondary" type="button" onclick="restore()">X</button>
                    </div>
                    <br>
                    <div id="moviePoster"><img width="150px"></div>

                    <label>Cine</label>
                    <select name="cinemaId" id="cinemas" onchange="getRooms()">
                    </select>

                    <label>Sala</label>
                    <select name="roomId" id="rooms" class="invent">
                    </select>


                <div class="form-group">

                       <input type="button" class="btn btn-primary"  value="Ver entradas" style="font-weight: bold" onClick="calculate()" >
	
                </div>
        
            <div id="stats" class="card"> </div>


    </div> 
</div> 

<!--modal-->
<div id="myModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title">Peliculas en cartelera</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
        </div> 
        <div class="moviesList" id="moviesList"></div>   <br>     
    </div>
  </div>
</div>



</main>
<script>

function restore(){      
    $("#movieTitle").val("");
    $("#moviePoster").empty();
    $("#movieId").val("");
}


function calculate(){

    var movie = document.getElementById("movieId").value;
    var cinema = document.getElementById("cinemas").value;
    var room = document.getElementById("rooms").value;

    if(movie == "")movie="all";
    if(cinema == "")cinema="all";
    if(room == "")room="all";


    $("#stats").empty();

    var xmlhttp=new XMLHttpRequest();
    xmlhttp.onreadystatechange=function() {
        if (this.readyState==4 && this.status==200) {

            //get stats
            var stats = JSON.parse(this.responseText);

            console.log(stats);

            $("#stats").append('<p class="card-text">Vendidas:  ' + stats['sold'] + '.</p>');
 
            $("#stats").append('<p class="card-text"> No vendidas: ' + stats['notSold'] + '.</p>');


                           
        }
        $("#stats").removeClass("invisible");
    } 
    xmlhttp.open("GET","<?= FRONT_ROOT?>purchase/getStats/" + movie + "/" + cinema + "/" + room , true);
    xmlhttp.send();
}

//load stuff when page is ready
$(document).ready(function(){
    $("#stats").addClass("invisible");
   getCinemas();
});


function openSelectMovie(){
  $('#myModal').modal('show');
}

function selectMovie(id, title, image){
  movie = id;
  //close modal
  $('#myModal').modal('toggle');

  //display movie title on input
  $("#movieTitle").val(title);

  
  //set movie id
  $("#movieId").val(id);
}

function getCinemas(){

    var xmlhttp=new XMLHttpRequest();
    xmlhttp.onreadystatechange=function() {
        if (this.readyState==4 && this.status==200) {

            //get all cinemas from json
            var cinemas = JSON.parse(this.responseText);


            $('#cinemas').append($('<option>',{
                    value: "all",
                    text : "Todos"
                }));
            //loop each room and add it to dropdown
            cinemas.forEach(function(cinema){
                $('#cinemas').append($('<option>',{
                    value: cinema['id'],
                    text : cinema['name']
                }));
            });
            getRooms();                            
        }
    } 
    xmlhttp.open("GET","<?= FRONT_ROOT?>cinema/getAllWithRooms/", true);
    xmlhttp.send();

}




function getRooms() {    


    //remove all options
    $('#rooms').empty()

    $('#rooms').append($('<option>',{
                    value: "all",
                    text : "Todas"
                }));

    var cinema = document.getElementById("cinemas").value;
    if(cinema == "all"){
       
        return;
    } 

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



$(".movieButton").attr("href", "#");





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

 
  xmlhttp.open("GET","<?= FRONT_ROOT?>movie/getAllAdded/", true);
  xmlhttp.send();
}
</script>
<?php include(VIEWS_PATH."footer.php"); ?>