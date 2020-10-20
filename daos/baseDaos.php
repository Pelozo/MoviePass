<?php
namespace daos;

use \ReflectionClass as ReflectionClass;
use \ReflectionProperty as ReflectionProperty;
use \ReflectionMethod as ReflectionMethod;

abstract class BaseDaos{

    private $table;
    private $class;
    private $className;
    private $reflectionClass;

    protected function __construct($table, $class){
        $this->table = $table;
        $this->className = strtolower($class);
        $this->class = '\\models\\'.strtolower($class);
        $this->reflectionClass = new ReflectionClass($this->class);
    }

    protected function _getAll(){
        try {
            $result = array();
            $query = "SELECT * FROM $this->table;";
            $this->connection = Connection::getInstance();
            $resultSet = $this->connection->executeWithAssoc($query);

            foreach($resultSet as $row){
                $object = new $this->class();
                foreach($row as $column=>$value){
                    $property = explode('_', $column)[0];
                    $method = new ReflectionMethod($this->class, 'set' . ucfirst($property));
                    $method->invoke($object, $value);
                }
                array_push($result, $object);
            }        

            return $result;
        }
        catch(\Exception $ex){
            throw $ex;
        }
    }

    /** 
     * @param T $object instance  to add
     * @param boolean $withId tries to insert id if true
     */

    protected function _add($object, $withId = false){
        try{
                //start creating query
                $query = "INSERT INTO " . $this->table . " (";

                //get class properties
                $properties = $this->reflectionClass->getProperties(ReflectionProperty::IS_PRIVATE | ReflectionProperty::IS_PROTECTED);              
                
                //get column names from property names
                $columns = array_column($properties, 'name');

                //retrieve values from object instance 
                $parameters = array();
                foreach($columns as $property){
                    //call getters
                    if($property != "id" | $withId){
                        $method = new ReflectionMethod($this->class, 'get' . ucfirst($property));
                        $value = $method->invoke($object);
                        $parameters[$property ."_". $this->className] = $value;
                    }
                }

                //add "_{objectClass}" to all properties
                array_walk($properties, function(&$value, $key) { $value->name .= '_' . $this->className; } );

                //var_dump($properties);             

                //get column names from property names
                $columns = array_column($properties, 'name');

                //remove id if necessary
                if(!$withId){
                    $columns = array_diff($columns, ['id_' . $this->className]);
                }
                  
                //separate them by comma
                $formattedColumns = implode(", ",$columns);
                
                //add them to query
                $query .= $formattedColumns;

                //keep creating query
                $query .= ") VALUES (";

                //add ":" to column names
                array_walk($columns, function(&$value, $key) { $value = ":" . $value; } );
                //add them to query
                $query .= implode(", ",$columns);;
                $query .= ");";

                $this->connection = Connection::getInstance();

                $this->connection->ExecuteNonQuery($query, $parameters);

        }catch(\Exception $ex){
            throw $ex;
        }
    }

    protected function _exists($value, $property = 'id'){
        try {
            $query = "SELECT * FROM $this->table WHERE {$property}_$this->className = :{$property}_$this->className;";
            $this->connection = Connection::getInstance();
            $parameters["{$property}_$this->className"] = $value;
            $resultSet = $this->connection->executeWithAssoc($query, $parameters);
            if(!empty($resultSet)){
                return true;
            } else {
                return false;
            }
        } catch(\Exception $ex){
            throw $ex;
        }
    }

    protected function _getByProperty($value, $property){
        try{
            //get array from db
            $query = "SELECT * FROM $this->table WHERE {$property}_$this->className = :{$property}_$this->className;";
            
            $this->connection = Connection::getInstance();
            $parameters["{$property}_$this->className"] = $value;            
            $resultSet = $this->connection->executeWithAssoc($query, $parameters);
            if (!empty($resultSet)){
                foreach($resultSet as $row){
                    $object = new $this->class();                
                    foreach($row as $column=>$value){
                        $property = explode('_', $column)[0];
                        $method = new ReflectionMethod($this->class, 'set' . ucfirst($property));
                        $method->invoke($object, $value);
                    }
                    return $object;
                }
            }else{
                return false;
            }            
        } catch (\Exception $ex){
            throw $ex;
        }
    }

    protected function _remove($value, $property = 'id'){
        try {
            $query = "DELETE FROM $this->table where {$property}_$this->className = :{$property}_$this->className;";
            $this->connection = Connection::getInstance();
            $parameters["id_$this->className"] = $value;
            $result = $this->connection->executeNonQuery($query, $parameters);
            return $result;

        } catch(\Exception $ex){
            throw $ex;
        }
    }
}

?>