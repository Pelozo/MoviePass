<?php 
namespace config;

class Request{
    private $controller;
    private $method;
    private $parameters = array();
    
    public function __construct()
    {
        //remueve simbolos raros
        $url = filter_input(INPUT_GET, "url", FILTER_SANITIZE_URL);

        //index.php?url=movie/show/

        //convierte el string en un array, separado por "/"
        $urlArray = explode("/", $url);// ["movie", "show"]

        //magia negra
        $urlArray = array_filter($urlArray);

        //devuelve el metodo (GET, POST, DELETE, etc);
        $methodRequest = $this->getMethodRequest();

        //si el array está vacio (porque se ingresó a http://localhost) setea el controlador a "home"
        if(empty($urlArray)){
            $this->controller = "home";            
        }else{//si hay algo en el array, agarrar el primer elemento [movie], lo pasa a minuscula todo menos el primer caracter -> [Movie]. Después lo saca del array
            $this->controller = array_shift($urlArray);
        }
        //si el array está vacio (después de sacarle el controller) entonces vamos al metodo "index", sino guardamos el metodo [show]
        if(empty($urlArray)){
            $this->method = "index";
        }else{
            $this->method = array_shift($urlArray);
        }

        //si el metodo es get 
        if($methodRequest == "GET"){
            //le saca el parametro "url", que es el que se manda siempre (porque es index.php?url=movie/show) -> index.php
            unset($_GET["url"]);
            //si no está vacio despues de sacarle eso, guarda los otros parametros en un arreglo $parameters 
            if(!empty($_GET)){                    
                foreach($_GET as $key => $value)                    
                    array_push($this->parameters, $value);
            }else{
                $this->parameters = $urlArray;
            }
        //si el metodo es post guarda los parametros de post en $parameters
        }elseif ($_POST){
            $this->parameters = $_POST;
        }

        //si hay archivos, lo agregamos a la lista de parametros.
        if($_FILES){
            unset($this->parameters["button"]);
            
            foreach($_FILES as $file){
                array_push($this->parameters, $file);
            }
        }
    }

    //devuelve el metodo (GET, POST, DELETE, MODIFY, etc)
    private static function getMethodRequest(){
        return $_SERVER["REQUEST_METHOD"];
    }            

    //getters de todas las variables que seteamos en el constructor

    public function getController(){//Show
        return $this->controller;
    }

    public function getMethod(){//index
        return $this->method;
    }

    public function getparameters(){//[]
        return $this->parameters;
    }
}
?>