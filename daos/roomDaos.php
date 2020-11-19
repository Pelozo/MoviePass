<?php
namespace daos;
use daos\baseDaos as BaseDaos;
use models\cinema as Cinema;
use models\room as Room;

class RoomDaos extends BaseDaos{

    const TABLE_NAME = "rooms";

    public function __construct(){
        parent::__construct(self::TABLE_NAME, 'Room');        
    }

    public function exists($id){
        return parent::_exists($id);
    }

    public function getById($id){

        $query = "SELECT r.*, c.* FROM rooms r
        INNER JOIN cinemas c on r.idCinema_room = c.id_cinema
        WHERE id_room = :id_room;";
       
        $parameters['id_room'] = $id; 

        try{
            $connection = Connection::getInstance();
            $resultSet = $connection->executeWithAssoc($query, $parameters)[0];

            $object = new Room(                                
                            $resultSet['name_room'],
                            $resultSet['price_room'], 
                            $resultSet['capacity_room'],
                            new Cinema(
                                $resultSet['name_cinema'],
                                $resultSet['address_cinema'],
                                $resultSet['city_cinema'],
                                $resultSet['province_cinema'],
                                $resultSet['zip_cinema']
                            )
                        );
            $object->getCinema()->setId($resultSet['id_cinema']);
            $object->setId($resultSet['id_room']); 

            return $object;
        }
        catch(\Exception $ex){
            throw $ex;
        }
    }

    public function getByCinema($idCinema){
        
        $query = "SELECT r.*, c.* FROM rooms r
        INNER JOIN cinemas c on r.idCinema_room = c.id_cinema
        WHERE idCinema_room = :idCinema_room;";
       
        $parameters['idCinema_room'] = $idCinema; 

        try{
            $connection = Connection::getInstance();
            $resultSet = $connection->executeWithAssoc($query, $parameters);

            $results = array();
            foreach ($resultSet as $room){
                $object = new Room(                                
                                $room['name_room'],
                                $room['price_room'], 
                                $room['capacity_room'],
                                new Cinema(
                                    $room['name_cinema'],
                                    $room['address_cinema'],
                                    $room['city_cinema'],
                                    $room['province_cinema'],
                                    $room['zip_cinema']
                                )
                            );
                $object->getCinema()->setId($room['id_cinema']);
                $object->setId($room['id_room']); 

                $result[] = $object;
            }
            return $result;
        }
        catch(\Exception $ex){
            throw $ex;
        }
    }

    public function add($room){
        $query = "INSERT INTO " . self::TABLE_NAME . " (id_room, name_room, price_room, capacity_room, idCinema_room) values(:id_room, :name_room, :price_room, :capacity_room, :idCinema_room);";
        $params['id_room'] = $room->getId();
        $params['name_room'] = $room->getName();
        $params['price_room'] = $room->getPrice();
        $params['capacity_room'] = $room->getCapacity();
        $params['idCinema_room'] = $room->getCinema();

        try{            
            $this->connection = Connection::getInstance();
            return $this->connection->executeNonQuery($query, $params);
        }
        catch(\Exception $ex){
            throw $ex;
        }
    }

    public function remove($id){
        return parent::_remove($id, 'id');
    }

    public function modify($room){
        $query = "UPDATE " . self::TABLE_NAME . " SET name_room = :name_room,
        price_room = :price_room,
        capacity_room = :capacity_room
        WHERE id_room = :id_room";

        $parameters['name_room'] = $room->getName();
        $parameters['price_room'] = $room->getPrice();
        $parameters['capacity_room'] = $room->getCapacity();
        $parameters['id_room'] = $room->getId();

        try{
            $this->connection = Connection::getInstance();
            $this->connection->ExecuteNonQuery($query, $parameters);

        } catch(\Exception $e) {
            throw $e;
        }
    }
}
?>