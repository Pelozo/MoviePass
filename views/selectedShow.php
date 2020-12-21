<?php include(VIEWS_PATH."header.php"); ?>
<div class="table-responsive">
    <div class="table-wrapper">
        <div class="table-title">
            <div class="row">
                <div class="col-sm-8"><h1><?=$movie->getTitle()?></h1></div>
                <div class="col-sm-8"><img src="<?=$movie->getImg()?>" style="margin:2em 0 2em 0" width="200px" height="300px"></img></div>
            </div>
        </div>
        <table id="showTable" class="table table-bordered">
        <col style="width:50%">
	    <col style="width:10%">
        <col style="width:20%">
        <col style="width:10%">
	    <col style="width:10%">
            <thead>
                <tr>
                    <th>Sinopsis</th>
                    <th>Idioma</th>
                    <th>Generos</th>
                    <th>Fecha de Lanzamiento</th>
                    <th>Duracion</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><?=$movie->getOverview()?></td>
                    <td><?=$movie->getLanguage()?></td>
                    <?php 
                        $genres = $movie->getGenres();
                        $names = array();
                        foreach($genres as $genre){
                            array_push($names, $genre->getName());
                        }
                        $genreString = implode(", ", $names);
                    ?>
                    <td><?=$genreString?></td>
                    <td><?=$movie->getReleaseDate()?></td>
                    <td><?=$movie->getDuration()?> minutos</td>

                </tr>  
            </tbody>
        </table>
        <div class="col-sm-8"><h1>Funciones</h1></div>
        <table id="showTable" class="table table-bordered">
        <col style="width:20%">
	    <col style="width:20%">
        <col style="width:10%">
        <col style="width:10%">
        <col style="width:20%">
        <col style="width:20%">
            <thead>
                <tr>
                    <th>Cine</th>
                    <th>Sala</th>
                    <th>Precio</th>
                    <th>Capacidad de la Sala</th>
                    <th>Fecha</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($availableShows as $show){ ?>
                <tr>
                    <td><?=$show->getRoom()->getCinema()->getName()?></td>
                    <td><?=$show->getRoom()->getName()?></td>
                    <td><?=$show->getRoom()->getPrice()?></td>
                    <td><?=$show->getRoom()->getCapacity()?></td>
                    <td><?=$show->getDatetime()?></td>
                    <td><a href="<?=FRONT_ROOT?>purchase/purchaseDetails/<?=$show->getId()?>">
                    <button type="submit" class="btn btn-info add-new" style="width:340px"><i class="fa fa-plus"></i>Comprar</button>
                    </a></td>
                </tr>
                <?php }
                foreach($notAvailableShows as $show){ ?>
                <tr>
                    <td><?=$show->getRoom()->getCinema()->getName()?></td>
                    <td><?=$show->getRoom()->getName()?></td>
                    <td><?=$show->getRoom()->getPrice()?></td>
                    <td><?=$show->getRoom()->getCapacity()?></td>
                    <td><?=$show->getDatetime()?></td>                    
                    <td><a>
                    <button type="submit" class="btn btn-info add-new" style="width:340px;background-color:rebeccapurple;border-color:rebeccapurple" disabled><i class="fa fa-plus"></i>Agotado</button></a></td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>
<?php include(VIEWS_PATH."footer.php"); ?>