
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

</main>
