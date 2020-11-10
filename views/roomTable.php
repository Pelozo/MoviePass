<!--Totally stolen from https://www.tutorialrepublic.com/codelab.php?topic=bootstrap&file=table-with-add-and-delete-row-feature -->
<?php include(VIEWS_PATH."header.php"); ?>
<div class="table-responsive">
    <div class="table-wrapper">
        <div class="table-title">
            <div class="row">
                <div class="col-sm-8"><h2>Administración <b>Salas: <?=$cinema->getName()?></b></h2></div>
                <div class="col-sm-4">
                    
                    <!--<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#defaultModal">Agregar Nuevo</button> -->
                    <form action="<?=FRONT_ROOT?>room/add" method="post">
                    
                        <input type="hidden" name="idCinema" value="<?=$id?>">
                        <button type="submit" class="btn btn-info add-new"><i class="fa fa-plus"></i> Agregar nuevo</button>
                    </form>
                    </a>
                    
                </div>
            </div>
        </div>
        <?php if(empty($rooms)){echo "No hay salas";}else{?>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Capacidad</th>
                    <th>Precio de entrada</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>

                <?php 
                foreach($rooms as $room){?>
                <tr>
                    <td><?=$room->getName()?></td>
                    <td><?=$room->getCapacity()?></td>
                    <td><?=$room->getPrice()?></td>
                    <td>
                        <form action="<?=FRONT_ROOT?>room/modify/" method="post">                        
                            <input type="hidden" name="idCinema" value="<?=$cinema->getId()?>">
                            <input type="hidden" name="id" value="<?=$room->getId()?>">
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
<?php include(VIEWS_PATH."footer.php"); ?>