<?php
namespace config;
	
    class Autoload {
        
        public static function start(){

            spl_autoload_register(function($className){
                $classPath = ucwords(str_replace("\\", DS, ROOT . DS . $className).".php");                
				include_once($classPath);
            });
            
        }
    }
?>