<?php include(VIEWS_PATH."header.php"); ?>

<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />

<main class="">
<div style="margin-top:50px" class="container-fluid">
	<div class="row ">		
		<div class="col-sm-12 bg-light justify-content-center text-center">
                   
            <label>Pelicula</label>

                <button class="btn btn-outline-secondary" type="button" onclick="openSelectMovie()">Seleccionar</button>

                <input id="movieTitle" type="text" disabled>
                <input id='movieId' type='hidden'>
                <button class="btn btn-outline-secondary" type="button" onclick="restore()">X</button>

            <br>
            <div id="moviePoster"><img width="150px"></div>

            <label>Cine</label>
            <select name="cinemaId" id="cinemas">
            </select>
            <br>


            <label for="amount">Date range:</label>
            <input id="dates" name="dateRange" value=""/><br>
            <input type="button" class="btn btn-primary"  value="Ver vendidos" style="font-weight: bold" onClick="calculate()" >	


            <div id="stats" class="card"> </div>
        </div>
    </div>
</div>
</main>




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


<script>

function calculate(){

var movie = document.getElementById("movieId").value;
var cinema = document.getElementById("cinemas").value;
var date = document.getElementById("dates").value;
//var date = encodeURIComponent(document.getElementById("dates").value);

if(movie == "")movie="all";
if(cinema == "")cinema="all";
if(date == "")date="all";


$("#stats").empty();

var xmlhttp=new XMLHttpRequest();
xmlhttp.onreadystatechange=function() {
    if (this.readyState==4 && this.status==200) {

        //get stats
        var stats = JSON.parse(this.responseText);

        console.log(stats);

        $("#stats").append('<p class="card-text">Vendido: $' + stats['sold'] + '.</p>');



                       
    }
    $("#stats").removeClass("invisible");
} 
xmlhttp.open("GET","<?= FRONT_ROOT?>purchase/getStatsTotal/" + movie + "/" + cinema + "/" + date , true);
xmlhttp.send();
}





function restore(){      
    $("#movieTitle").val("");
    $("#moviePoster").empty();
    $("#movieId").val("");
}

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
            //loop each cinema and add it to dropdown
            cinemas.forEach(function(cinema){
                $('#cinemas').append($('<option>',{
                    value: cinema['id'],
                    text : cinema['name']
                }));
            });
                                     
        }
    } 
    xmlhttp.open("GET","<?= FRONT_ROOT?>cinema/getAllWithRooms/", true);
    xmlhttp.send();

}
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



<script>
$('input[name="dateRange"]').daterangepicker({
      autoUpdateInput: false,
      locale: {
          cancelLabel: 'Clear'
      }
  });

  $('input[name="dateRange"]').on('apply.daterangepicker', function(ev, picker) {
      $(this).val(picker.startDate.format('YYYY-MM-DD') + ' a ' + picker.endDate.format('YYYY-MM-DD'));
  });

  $('input[name="dateRange"]').on('cancel.daterangepicker', function(ev, picker) {
      $(this).val('');
  });

</script>

<?php include(VIEWS_PATH."footer.php"); ?>