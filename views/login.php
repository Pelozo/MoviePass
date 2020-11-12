<?php include(VIEWS_PATH."header.php"); ?>
<h1 class="indexTitle">Iniciar sesión</h1>
<div style="margin-top:50px" class="container">
	<div class="row">
		<div class="col-sm-1"></div>
		<div class="col-sm-2"></div>	
		<div class="col-sm-6 bg-light boxStyle">
            <form action='login' method='POST'>
                <div class="form-group">
                    E-mail<input class="form-control" type='email' name='email' placeholder='Ingrese su e-mail' required>
                </div>
                <div class="form-group">
                    Contraseña<input class="form-control" type='password' name='password' placeholder='Ingrese contraseña' required>
                </div>
                <button class="btn btn-primary">Confirmar</button>
            </form>
            <br>
            <?php if (isset($err)){
                echo $err;
            }?>
        </div>
        <div class="col-sm-1"></div>
        <div class="col-sm-2"></div>
<?php include(VIEWS_PATH."footer.php"); ?>