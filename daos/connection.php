<?php
    namespace daos;

    use \PDO as PDO;
    use \Exception as Exception;
    use daos\QueryType as QueryType;

    class Connection{
        //variable para guardar la instancia de pdo
        private $pdo = null;
        //variable para guardar la instancia de pdoStatement
        private $pdoStatement = null;

        //variable para guardiar la instancia del singleton
        private static $instance = null;

        private function __construct(){
            try{
                //instancia un nuevo PDO con los datos de la db (tipo de db, nombre, usuario, pass, etc)
                $this->pdo = new PDO("mysql:host=".DB_HOST."; dbname=".DB_NAME, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
                //setea para que el pdo nos tire los errores de la db si los hay.
                $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            }
            catch(Exception $ex){
                //tira excepción 
                throw $ex;
            }
        }

        //metodo para obtener la instancia del singleton //en lugar de "new Connection" usamos "Connection::getInstance()"
        public static function getInstance(){
            if(self::$instance == null)
                self::$instance = new Connection();

            return self::$instance;
        }

        //ejecuta query y devuelve el resultado de fetchALl
        public function execute($query, $parameters = array(), $queryType = QueryType::Query){
            try{
                //prepara la query
                $this->prepare($query);
                
                //bindea los parametros (reemplaza los :name con la variable que pasamos por parametro)
                $this->bindParameters($parameters, $queryType);
                
                //lo ejecuta
                $this->pdoStatement->execute();

                //devuelve el resultado
                return $this->pdoStatement->fetchAll();
            }
            catch(Exception $ex)            {
                throw $ex;
            }
        }

        /*
        0 => [],
        1 => [],
        2 => []

        */

        public function executeWithAssoc($query, $parameters = array()){
            try{
                $this->prepare($query);
                
                $this->bindParameters($parameters);
          
                $this->pdoStatement->execute();

                return $this->pdoStatement->fetchAll(PDO::FETCH_ASSOC);
                
            }catch(Exception $ex){
                throw $ex;
            }
        }

        /*
        [], [], []
        */
        
        //ejecuta una query y devuelve la cantidad de filas afectadas
        public function executeNonQuery($query, $parameters = array(), $queryType = QueryType::Query){            
            try{
                $this->prepare($query);
                
                $this->bindParameters($parameters, $queryType);

                $this->pdoStatement->execute();

                return $this->pdoStatement->rowCount();
            }
            catch(Exception $ex){
                throw $ex;
            }        	    	
        }
        
        //llama al metodo Prepare de PDO, el cual devuelve un pdoStatement (y lo guardamos)
        private function prepare($query){
            try{
                $this->pdoStatement = $this->pdo->prepare($query);
            }catch(Exception $ex){
                throw $ex;
            }
        }

        //bindea los parametros (reemplaza los :name con la variable que pasamos por parametro)
        //usa el pdoStatement que preparamos en el metodo de arriba
        private function bindParameters($parameters = array(), $queryType = QueryType::Query){
            $i = 0;

            foreach($parameters as $parameterName => $value){                
                $i++;
                if($queryType == QueryType::Query)
                    $this->pdoStatement->bindParam(":".$parameterName, $parameters[$parameterName]);
                else
                    $this->pdoStatement->bindParam($i, $parameters[$parameterName]);
            }
        }

        public function getLastId(){
            return $this->pdo->lastInsertId();
        }
    }
?>