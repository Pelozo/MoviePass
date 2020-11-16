<?php
namespace daos;
use daos\baseDaos as BaseDaos;
use models\purchase as Purchase;

class PurchaseDaos extends BaseDaos{
    
    const TABLE_NAME = 'purchases';

    public function __construct(){
        parent::__construct(self::TABLE_NAME, 'Purchase');
    }

    public function getAll(){
        return parent::_getAll();
    }

    public function exists($id){
        return parent::_exists($id);
    }

    //hacer bien este getById
    public function getById($id){
        return parent::_getByProperty($id, 'id');
    }

    public function getLastInsertId(){
        $this->connection = Connection::getInstance();
        return $this->connection->getLastId();
    }

    public function add($purchase){
        try{
            $query = "INSERT INTO " . self::TABLE_NAME . " (idUser_purchase, idShow_purchase, date_purchase, ticketsQuantity_purchase, discount_purchase, total_purchase)
                                                            values(:idUser_purchase, :idShow_purchase, :date_purchase, :ticketsQuantity_purchase, :discount_purchase, :total_purchase);";
            
            $params['idUser_purchase'] = $purchase->getUser()->getId();
            $params['idShow_purchase'] = $purchase->getShow()->getId();
            $params['date_purchase'] = $purchase->getDate();
            $params['ticketsQuantity_purchase'] = $purchase->getTicketsQuantity();
            $params['discount_purchase'] = $purchase->getDiscount();
            $params['total_purchase'] = $purchase->getTotal();
    
            $this->connection = Connection::getInstance();
            return $this->connection->executeNonQuery($query, $params);
        }catch(\Exception $ex){
            throw $ex;
            
        }

    }

    public function remove($id){
        return parent::_remove($id, 'id');
    }

    public function getSoldTicketsByShow($id){
        $query = "SELECT SUM(p.ticketsQuantity_purchase) AS sold
        FROM purchases p
        WHERE p.idShow_purchase = :id_show";

        $connection = Connection::getInstance();
        $parameters['id_show'] = $id; 
        $resultSet = $connection->executeWithAssoc($query, $parameters)[0];

        //como soluciono esto??
        return (isset($resultSet['sold']))? $resultSet['sold'] : 0;
    }


    public function getSoldTickets($idMovie = null, $idCinema = null, $idRoom = null){
        /*
        $query = "SELECT SUM(p.ticketsQuantity_purchase) AS sold, SUM(r.capacity_room) - SUM(p.ticketsQuantity_purchase) AS NotSold FROM purchases p
        INNER JOIN shows s ON p.idShow_purchase = s.id_show
        INNER JOIN movies m ON s.idMovie_show = m.id_movie
        INNER JOIN rooms r ON s.idRoom_show = r.id_room
        INNER JOIN cinemas c ON r.idCinema_room = c.id_cinema
        WHERE m.id_movie = " . (($idMovie)? ":id_movie" : "m.id_movie") .
        " AND c.id_cinema = " . (($idCinema)? ":id_cinema" : "c.id_cinema") .
        " AND r.id_room = " . (($idRoom)? ":id_room" : "r.id_room");*/

        $query = "SELECT 
                        m.title_movie, 
                        IFNULL(SUM(p.ticketsQuantity_purchase), 0)  AS sold, 
                        ifNULL((SELECT (SUM(r.capacity_room)) FROM shows s
                            INNER JOIN rooms r ON s.idRoom_show = r.id_room
                            INNER JOIN movies m ON s.idMovie_show = m.id_movie
                            INNER JOIN cinemas c ON r.idCinema_room = c.id_cinema
                            WHERE m.id_movie = " . (($idMovie)? ":id_movie" : "m.id_movie") .
                        " AND c.id_cinema = " . (($idCinema)? ":id_cinema" : "c.id_cinema") .
                        " AND r.id_room = " . (($idRoom)? ":id_room" : "r.id_room") . "), 0)
                        - IFNULL(SUM(p.ticketsQuantity_purchase), 0) AS notSold
                        FROM purchases p
                            INNER JOIN shows s ON p.idShow_purchase = s.id_show
                            INNER JOIN movies m ON s.idMovie_show = m.id_movie
                            INNER JOIN rooms r ON s.idRoom_show = r.id_room
                            INNER JOIN cinemas c ON r.idCinema_room = c.id_cinema
                            WHERE m.id_movie = " . (($idMovie)? ":id_movie" : "m.id_movie") .
                        " AND c.id_cinema = " . (($idCinema)? ":id_cinema" : "c.id_cinema") .
                        " AND r.id_room = " . (($idRoom)? ":id_room" : "r.id_room");

        /*
        echo "<pre>";
        echo $query;
        echo "</pre>";


        echo "asd: $idMovie, $idCinema, $idRoom";
        */

        $params = array();

        if($idMovie) $params['id_movie'] = $idMovie;

        if($idCinema) $params['id_cinema'] = $idCinema;

        if($idRoom) $params['id_room'] = $idRoom;

        //echo $query;


        try{
            $connection = Connection::getInstance();
            return $resultSet = $connection->executeWithAssoc($query, $params)[0];
            
            
            echo "<pre>";
            var_dump($resultSet);
            echo "</pre>";
            


        }catch(\Exception $ex){
            throw $ex;
            
        }

    }



}

?>