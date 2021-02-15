<?php
namespace daos;
use daos\BaseDaos as BaseDaos;
use models\Province as Province;
use models\Address as Address;

class AddressDaos extends BaseDaos{

    const TABLE_NAME = "addresses";

    public function __construct(){
        parent::__construct(self::TABLE_NAME, 'Address');        
    }

    public function exists($id){
        return parent::_exists($id);
    }
    
    public function remove($id){
        return parent::_remove($id);
    }

    public function getByCinema($idCinema){
        $query = "SELECT a.*, p.*
        FROM addresses a 
        LEFT JOIN provinces p ON p.id_province = a.province_address
        WHERE id_address = (SELECT address_cinema FROM cinemas WHERE id_cinema = :id_cinema)";

        $parameters['id_cinema'] = $idCinema; 

        try{
            $connection = Connection::getInstance();
            $address = $connection->executeWithAssoc($query, $parameters)[0];

            $object = new Address($address['block_address'],$address['city_address'],$address['zip_address'],
                                new Province($address['id_province'], $address['name_province'])
            );
            $object->setId($address['id_address']);
            return $object;
        }catch(\Exception $ex){
            throw $ex;
        }     


    }


    public function add($address){
        $query = "INSERT INTO " . self::TABLE_NAME . " (block_address, city_address, province_address, zip_address) values(:block_address, :city_address, :province_address, :zip_address);";
        
        $params['block_address'] = $address->getBlock();
        $params['city_address'] = $address->getCity();
        $params['province_address'] = $address->getProvince()->getId();
        $params['zip_address'] = $address->getZip();

        try{            
            $this->connection = Connection::getInstance();
            return $this->connection->executeNonQuery($query, $params);
        }catch(\Exception $ex){
            throw $ex;
        }
    }

    public function modify($address){
        $query = "UPDATE addresses
        SET block_address = :block_address,
            city_address = :city_address,
            province_address = :province_address,
            zip_address = :zip_address
        WHERE id_address = :id_address";


        $params['block_address'] = $address->getBlock();
        $params['city_address'] = $address->getCity();
        $params['province_address'] = $address->getProvince()->getId();
        $params['zip_address'] = $address->getZip();
        $params['id_address'] = $address->getId();

        try{            
            $this->connection = Connection::getInstance();
            return $this->connection->executeNonQuery($query, $params);
        }catch(\Exception $ex){
            throw $ex;
        }


    }

}
