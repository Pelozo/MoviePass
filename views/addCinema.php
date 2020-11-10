<?php include(VIEWS_PATH."header.php"); ?>
<main class="">

<div style="margin-top:50px" class="container">
	<div class="row">
		<div class="col-sm-1"></div>
		<div class="col-sm-2"></div>		
		<div class="col-sm-6 bg-light boxStyle">
			<form name="theform" action="" method="POST">
                <?php if(isset($cinema)){?>
                    <input type="hidden" name="id" value="<?=$cinema->getId()?>">
                <?php }?>

                <div class="form-group">
                    <label >Nombre<span class="asteriskField">*</span></label>
                    <input class="form-control" name="name" type="text" value="<?php if(isset($cinema)) echo $cinema->getName()?>" size="20"><br>
                <div class="form-group">
                    <label>Dirección<span class="asteriskField">*</span></label>
                    <input class="form-control" name="address" type="text" value="<?php if(isset($cinema)) echo $cinema->getAddress()?>" size="20">
                </div>

                <div class="form-group">
                    <label>Ciudad<span class="asteriskField">*</span></label>
                    <input class="form-control" name="city" type="text" value="<?php if(isset($cinema)) echo $cinema->getCity()?>" size="20">                        
                </div>
                
                <div class="form-group">
                    <label>Código postal<span class="asteriskField">*</span></label>
                    <input class="form-control" name="postal" type="number"  min="0" value="<?php if(isset($cinema)) echo $cinema->getZip()?>" size="20" >
                </div>
                <div class="form-group">
                    <label>Provincia<span class="asteriskField">*</span></label>
                    <input class="form-control" name="province" type="text" value="<?php if(isset($cinema)) echo $cinema->getProvince()?>" size="20">
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
