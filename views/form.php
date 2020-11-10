<?php include(VIEWS_PATH."header.php"); ?>
<form action='signup' method="POST">
<h1>Registrarse</h1>
    <input type='text' placeholder='name' name='name'>
    <input type='text' placeholder='email' name='email'>
    <input type='password' placeholder='password' name='password'>
    <button type='submit'>Send</button>
</form>
<form action='login' method='POST'>
<h1>Log in</h1>
    <input type='text' placeholder='email' name='email'>
    <input type='password' placeholder='password' name='password'>
    <button type='submit'>Send</button>
</form>
<?php include(VIEWS_PATH."footer.php"); ?>