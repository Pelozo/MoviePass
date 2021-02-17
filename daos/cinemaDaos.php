<?php
namespace daos;
use daos\BaseDaos as BaseDaos;
use models\Cinema as Cinema;
use models\Address as Address;
use models\Province as Province;

class CinemaDaos extends BaseDaos{

    const TABLE_NAME = "cinemas";
    private $addressDaos;
    private $provinceDaos;

    public function __construct(){
        parent::__construct(self::TABLE_NAME, 'Cinema');
        $this->addressDaos = new AddressDaos();
        $this->provinceDaos = new ProvinceDaos();
    }

    public function getAll(){

        $query = "SELECT c.id_cinema, c.name_cinema, a.block_address, a.city_address, p.id_province, p.name_province, a.zip_address
        FROM " . self::TABLE_NAME . " c
        LEFT JOIN addresses a ON c.address_cinema = a.id_address
        LEFT JOIN provinces p ON a.province_address = p.id_province
        ";


        $result = array();
        try{
            $connection = Connection::getInstance();
            $cinemas = $connection->executeWithAssoc($query);
    
            foreach($cinemas as $cinema){
                $object = new Cinema($cinema['name_cinema'],
                                            new Address($cinema['block_address'],$cinema['city_address'],$cinema['zip_address'],
                                                new Province($cinema['id_province'], $cinema['name_province'])
                ));
                $object->setId($cinema['id_cinema']);
                $result[] = $object;
            }
            
            return $result;
        }catch(\Exception $ex){
            throw $ex;
        }
    }

    public function exists($id){
        return parent::_exists($id);
    }

    public function getById($id){

        $query = "SELECT c.id_cinema, c.name_cinema, a.block_address, a.city_address, p.id_province, p.name_province, a.zip_address, a.id_address
        FROM " . self::TABLE_NAME . " c
        LEFT JOIN addresses a ON c.address_cinema = a.id_address
        LEFT JOIN provinces p ON a.province_address = p.id_province
        WHERE c.id_cinema = :id_cinema";

       
        $parameters['id_cinema'] = $id; 

        try{
            $connection = Connection::getInstance();
            $cinema = $connection->executeWithAssoc($query, $parameters)[0];

            $object = new Cinema($cinema['name_cinema'],
                                        new Address($cinema['block_address'],$cinema['city_address'],$cinema['zip_address'],
                                            new Province($cinema['id_province'], $cinema['name_province'])
            ));
            $object->setId($cinema['id_cinema']);
            $object->getAddress()->setId($cinema['id_address']);
            $object->getAddress()->getProvince()->setId($cinema['id_province']);
            return $object;
        }
        catch(\Exception $ex){
            throw $ex;
        }
    }

    public function add($cinema){
        //check province
        try{
            if(!$this->provinceDaos->exists($cinema->getAddress()->getProvince()->getId())){
                throw new \Exception("Invalid province");
            }else{
                //add address
                $this->addressDaos->add($cinema->getAddress());
                $this->connection = Connection::getInstance();
                $addressId = $this->connection->getLastId();

                $query = "INSERT INTO " . SELF::TABLE_NAME . "(name_cinema, address_cinema) VALUES (:name_cinema, :address_cinema);";
                $params['name_cinema'] = $cinema->getName();
                $params['address_cinema'] = $addressId;                
                
                return $this->connection->executeNonQuery($query, $params);
            }
        }catch(\Exception $ex){
            throw $ex;
        }
    }

    public function remove($id){
        try{
            //remove address
            $sql = "SELECT id_address FROM addresses WHERE id_address = (SELECT address_cinema FROM cinemas WHERE id_cinema = :id)";
            $params['id'] = $id;
            $connection = Connection::getInstance();
            $id = $connection->executeWithAssoc($sql, $params)[0]['id_address'];
            $this->addressDaos->remove($id);
            //remove cinema
            return parent::_remove($id, 'id');
        }catch(\Exception $ex){
            throw $ex;
        }
    }

    public function modify($cinema){
        //check province
        try{
            if(!$this->provinceDaos->exists($cinema->getAddress()->getProvince()->getId())){
                throw new \Exception("Invalid province");
            }else{
                //modify address
                $addressId = $this->addressDaos->getByCinema($cinema->getId())->getId();
                $cinema->getAddress()->setId($addressId);
                $this->addressDaos->modify($cinema->getAddress());
                //modify cinema
                
                $query = "UPDATE cinemas
                SET name_cinema = :name_cinema
                WHERE id_cinema = :id_cinema";

                $params['name_cinema'] = $cinema->getName();
                $params['id_cinema'] = $cinema->getId(); 

                $connection = Connection::getInstance();
                
                return $connection->executeNonQuery($query, $params);
            }
        }catch(\Exception $ex){
            throw $ex;
        }
    }

    public function getAllWithRooms(){

        $query = "SELECT c.*, a.*, p.*, r.*
        FROM ". self::TABLE_NAME ." c 
        LEFT JOIN addresses a ON a.id_address = c.address_cinema
        LEFT JOIN provinces p ON p.id_province = a.province_address
        INNER JOIN rooms r ON c.id_cinema = r.idCinema_room GROUP BY c.id_cinema 
       ";        

        

        $result = array();
        try{
            $connection = Connection::getInstance();
            $cinemas = $connection->executeWithAssoc($query);
    
            foreach($cinemas as $cinema){
                $object = new Cinema($cinema['name_cinema'],
                                new Address($cinema['block_address'],$cinema['city_address'],$cinema['zip_address'],
                                    new Province($cinema['id_province'], $cinema['name_province'])
                            ));
                $object->setId($cinema['id_cinema']);
                $object->getAddress()->setId($cinema['id_address']);
                $object->getAddress()->getProvince()->setId($cinema['id_province']);
                $result[] = $object;
            }
            
            return $result;
        }catch(\Exception $ex){
            throw $ex;
        }
    }


}
?>