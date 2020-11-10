<?php include(VIEWS_PATH."header.php"); ?>
<h1 class="indexTitle">Iniciar sesión</h1>
<form action='login' method='POST'>
    <br>E-mail<input type='email' name='email' placeholder='Ingrese su e-mail' required>
    <br>Contraseña<input type='password' name='password' placeholder='Ingrese contraseña' required>
    <br><button>Confirmar</button>
</form>
<?php include(VIEWS_PATH."footer.php"); ?>