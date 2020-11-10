<?php include(VIEWS_PATH."header.php"); ?>
<main class="">

<div style="margin-top:50px" class="container">
	<div class="row">
		<div class="col-sm-1"></div>
		<div class="col-sm-2"></div>	
		<div class="col-sm-6 bg-light boxStyle">
			<form name="theform" action="" method="POST">
                <h2>Perfil</h2>
                <div class="form-group">
                    <label >Email</label>
                    <input class="form-control" type="text" disabled value="<?= $_SESSION['user']->getEmail()?>">
                </div>	
                <div class="form-group">
                    <label >Nombre</label>
                    <input class="form-control" name="firstName" type="text" value="<?php if(isset($profile)) echo $profile->getFirstName()?>" size="20">
                </div>
                <div class="form-group">
                    <label>Apellido</label>
                    <input class="form-control" name="lastName" type="text" value="<?php if(isset($profile)) echo $profile->getLastName()?>" size="20">
                </div>
                <div class="form-group">
                    <label>DNI</label>
                    <input class="form-control" name="dni" type="text" value="<?php if(isset($profile)) echo $profile->getDni()?>" size="20">                        
                </div>

                <?php if(isset($error)){?>
                    <?=$error?>
                <?php }?>

                <div class="form-group">
                    <div class="row">
                       <input class="btn btn-primary" type="submit"  value="Modificar" style="font-weight: bold">
                    </div>
                </div>
             </form>
             <?php if(isset($message)){
                 echo $message;
             }?>      
		</div>
		<div class="col-sm-1"></div>
		<div class="col-sm-2"></div>
    </div> 
</div> 
</main>
<?php include(VIEWS_PATH."footer.php"); ?>