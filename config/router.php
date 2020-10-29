<?php 
    namespace config;

    use config\request as Request;

    class Router{
        public static function route(Request $request){
            //agarra el nombre del controlador y le concatena "Controller" -> MovieController
            $controllerName = $request->getcontroller() . 'Controller';

            //agarra el metodo (get, post, etc)
            $methodName = $request->getmethod();

            //agarra los parametros
            $methodParameters = $request->getparameters();          

            //le concatena el namespace al nombre de controlador -> "controllers\MovieController"
            $controllerClassName = "controllers\\". $controllerName;

            //instancia el controlador de la linea anterior
            $controller = new $controllerClassName;

            //si no hay parametros llama a la controladora sin parametros
            if(!isset($methodParameters))            
                call_user_func(array($controller, $methodName));
            else
            //si tiene parametro llama a la controladora y le pasa los parametros
                call_user_func_array(array($controller, $methodName), $methodParameters);
        }
    }
?>