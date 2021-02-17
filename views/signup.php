<?php include(VIEWS_PATH."header.php"); ?>
<h1 class="text-center">Registrarse</h1>

<div  class="container">

	<div class="row mt-sm-4">
		<div class="col-sm-1"></div>
		<div class="col-sm-2"></div>	
		<div class="col-sm-6 bg-light boxStyle border">
            <form action='signup' method='POST'>
                <h2>Datos de la cuenta: </h2>
                <div class="form-group">
                    E-mail*<input class="form-control" type='email' name='email' placeholder='Ingrese su e-mail' required>
                </div>
                <div class="form-group">
                    Contraseña*<input class="form-control" type='password' name='password' placeholder='Ingrese contraseña' required>
                </div>
                <h2>Datos del perfil: </h2>
                <div class="form-group">
                    Nombre<input class="form-control" type='text' name='firstName' placeholder='Ingrese su nombre'>
                </div>
                <div class="form-group">
                    Apellido<input class="form-control" type='text' name='lastName' placeholder='Ingrese su apellido'>
                </div>
                <div class="form-group">
                    DNI<input class="form-control" type='text' name='dni' placeholder='Ingrese su DNI'>
                </div>
                
                <div class="row">
                    <div class ="col-sm-3">
                        <button class="btn btn-primary">Registrarse</button>
                    </div>

                    <div class ="col-sm-3"></div>

                    <div class="col-sm-6">
                        <a href="<?=FRONT_ROOT?>user/signupFacebook" class="btn btn-block btn-social btn-facebook" style="color:white">
                            <span class="fa fa-facebook" style="margin:2px;"></span> Registrarse con facebook
                        </a>
                    </div>
                </div>
                


            </form>
                <?php if (isset($err)){
                    echo $err;
                }?>
        </div>
        <div class="col-sm-1"></div>
        <div class="col-sm-2"></div>
<?php include(VIEWS_PATH."footer.php"); ?>
