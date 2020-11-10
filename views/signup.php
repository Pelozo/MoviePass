<?php include(VIEWS_PATH."header.php"); ?>
<h1 class="indexTitle">Registrarse</h1>
<form action='signup' method='POST'>
    <h2>Datos de la cuenta: </h2>
    <br>E-mail*<input type='email' name='email' placeholder='Ingrese su e-mail' required>
    <br>Contraseña*<input type='password' name='password' placeholder='Ingrese contraseña' required>
    <h2>Datos del perfil: </h2>
    <br>Nombre<input type='text' name='firstName' placeholder='Ingrese su nombre'>
    <br>Apellido<input type='text' name='lastName' placeholder='Ingrese su apellido'>
    <br>DNI<input type='text' name='dni' placeholder='Ingrese su DNI'>
    <br><button>Confirmar</button>
</form>
<?php include(VIEWS_PATH."footer.php"); ?>
