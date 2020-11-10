<!--Totally stolen from https://www.tutorialrepublic.com/codelab.php?topic=bootstrap&file=table-with-add-and-delete-row-feature -->
<?php include(VIEWS_PATH."header.php"); ?>
<script>
$(document).ready(function() {
    $('#cinemaTable').DataTable();
} );
</script>
<div class="table-responsive">
    <div class="table-wrapper">
        <div class="table-title">
            <div class="row">
                <div class="col-sm-8"><h2>Administración <b>Cines</b></h2></div>
                <div class="col-sm-4">
                    
                    <!--<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#defaultModal">Agregar Nuevo</button> -->
                    
                    <a href="<?=FRONT_ROOT?>cinema/add">
                    <button type="submit" class="btn btn-info add-new"><i class="fa fa-plus"></i> Agregar nuevo</button>
                    </a>
                    
                </div>
            </div>
        </div>
        <table id="cinemaTable" class="table table-bordered">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Dirección</th>
                    <th>Ciudad y Provincia</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($cinemas as $cinema){?>
                <tr>
                    <td><?=$cinema->getName()?></td>
                    <td><?=$cinema->getAddress()?></td>
                    <td><?=$cinema->getCity(). ", " . $cinema->getProvince()?></td>
                    <td>
                        <form action="<?=FRONT_ROOT?>cinema/modify/" method="post">
                            <input type="hidden" name="id" value="<?=$cinema->getId()?>">
                            <button class="material-icons" onclick="this.form.submit()">&#xE254;</i>                             
                        </form>
                        <form action="<?=FRONT_ROOT?>cinema/remove/" method="post">
                            <input type="hidden" name="id" value="<?=$cinema->getId()?>">
                            <button class="material-icons" onclick="this.form.submit()">&#xE872;</i> 
                        </form>
                        <form action="<?=FRONT_ROOT?>room/show/<?=$cinema->getId()?>" method="get">
                        <!--<form action="<?=FRONT_ROOT?>room" method="post">
                            <input type="hidden" name="id" value="<?=$cinema->getId()?>">
                        -->
                            <button class="material-icons" onclick="this.form.submit()">&#xE254;</i>                             
                        </form>
                    </td>
                </tr>
                <?php }?>    
            </tbody>
        </table>
    </div>
</div>
<?php include(VIEWS_PATH."footer.php"); ?>