<?php 
namespace config;

define("ROOT", dirname(__DIR__));
define("DS", "/");

define("FRONT_ROOT", "/facu/MoviePass/");

define("VIEWS_PATH", "views/");
define("CSS_PATH", FRONT_ROOT.VIEWS_PATH . "css/");
define("JS_PATH", FRONT_ROOT.VIEWS_PATH . "js/");

define('DATABASE_ERR', 'Hubo un error en la base de datos');

define("DEFAULT_POSTER", FRONT_ROOT . "views/img/default_poster.png");


include_once('auth.php');
?>