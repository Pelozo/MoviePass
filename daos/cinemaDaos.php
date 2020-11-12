<?php
namespace daos;
use daos\baseDaos as BaseDaos;
use models\cinema as Cinema;

class CinemaDaos extends BaseDaos{

    const TABLE_NAME = "cinemas";

    public function __construct(){
        parent::__construct(self::TABLE_NAME, 'Cinema');        
    }

    public function getAll(){
        return parent::_getAll();
    }

    public function exists($id){
        return parent::_exists($id);
    }

    public function getById($id){
        return parent::_getByProperty($id, 'id');
    }

    public function add($cinema){
        return parent::_add($cinema);
    }

    public function remove($id){
        return parent::_remove($id, 'id');
    }

    public function modify($cinema){
        return parent::_modify($cinema, $cinema->getId(), "id");
    }

    public function getAllWithRooms(){

        $query = "SELECT * FROM ". self::TABLE_NAME ." c INNER JOIN rooms r ON c.id_cinema = r.idCinema_room GROUP BY c.id_cinema";        

        $connection = Connection::getInstance();

        $result = array();
        try{
            $cinemas = $connection->executeWithAssoc($query);
    
            foreach($cinemas as $cinema){
                $object = new Cinema($cinema['name_cinema'], $cinema['address_cinema'],$cinema['city_cinema'],$cinema['zip_cinema'],$cinema['province_cinema']);
                $object->setId($cinema['id_cinema']);
                $result[] = $object;
            }
            
            return $result;
        }catch(\Exception $ex){
            throw $ex;
        }
    }
}
?>