<!--Totally stolen from https://www.tutorialrepublic.com/codelab.php?topic=bootstrap&file=table-with-add-and-delete-row-feature -->
<div class="table-responsive">
    <div class="table-wrapper">
        <div class="table-title">
            <div class="row">
                <div class="col-sm-8"><h2>Administración <b>Funciones</b></h2></div>
                <div class="col-sm-4">
                    
                    <!--<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#defaultModal">Agregar Nuevo</button> -->
                    <form action="<?=FRONT_ROOT?>show/add" method="get">                    
                        <button type="submit" class="btn btn-info add-new"><i class="fa fa-plus"></i> Agregar nuevo</button>
                    </form>
                    </a>
                    
                </div>
            </div>
        </div>
        <?php if(empty($shows)){echo "No hay funciones";}else{?>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Pelicula</th>
                    <th>Sala</th>
                    <th>Cine</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>

                <?php 
                foreach($shows as $show){?>
                <tr>
                    <td><?=$room->getDate()?></td>
                    <td><?=$room->getMovie()->getName()?></td>
                    <td><?=$room->getRoom()->getName()?></td>
                    
                    <td>
                        <form action="<?=FRONT_ROOT?>room/modify/" method="post">
                            <input type="hidden" name="id" value="<?=$room->getId()?>">
                            <input type="hidden" name="idCinema" value="<?=$cinema->getId()?>">
                            <button class="material-icons" onclick="this.form.submit()">&#xE254;</i>                             
                        </form>
                        
                        <form action="<?=FRONT_ROOT?>room/remove/" method="post">
                            <input type="hidden" name="id" value="<?=$room->getId()?>">
                            <input type="hidden" name="idCinema" value="<?=$cinema->getId()?>">
                            <button class="material-icons" onclick="this.form.submit()">&#xE872;</i> 
                        </form>
                    </td>
                </tr>
                <?php }}?>    
            </tbody>
        </table>
    </div>
</div>