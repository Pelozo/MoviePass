<?php include(VIEWS_PATH."header.php"); ?>
<main class="">

<div style="margin-top:50px" class="container">
	<div class="row">
		<div class="col-sm-1"></div>
		<div class="col-sm-2"></div>		
		<div class="col-sm-6 bg-light boxStyle">
			<form name="theform" action="" method="POST">
                <input type="hidden" name="idCinema" value="<?=$idCinema?>">
                <input type="hidden" name="id" value="<?php if(isset($room)) echo $room->getId()?>">

                <div class="form-group">
                
                    <label >Nombre<span class="asteriskField">*</span></label>
                    <input class="form-control" name="name" type="text" value="<?php if(isset($room)) echo $room->getName()?>" size="20"><br>
                <div class="form-group">
                    <label>Capacidad<span class="asteriskField">*</span></label>
                    <input class="form-control" name="capacity" type="number" value="<?php if(isset($room)) echo $room->getCapacity()?>" min="0" size="20">
                </div>
                <div class="form-group">
                    <label>Precio de entrada<span class="asteriskField">*</span></label>
                    <input class="form-control" name="ticket" type="number" value="<?php if(isset($room)) echo $room->getPrice()?>"  min="0" size="20">
                </div>


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

</main>
<?php include(VIEWS_PATH."footer.php"); ?>