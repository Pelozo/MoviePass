<?php
 
	//muestra todos los errores que puedan surgir en php
	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);
	
	//requiere estos dos archivos
	require "config/autoload.php";
	require "config/constants.php";

	use config\autoload as Autoload;
	use config\router 	as Router;
	use config\request 	as Request;
	
	//inicia el autoload
	Autoload::start();

	//inicia session
	session_start();
	

	Router::route(new Request());


?>