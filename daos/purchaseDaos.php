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
        }catch(Exception $ex){
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
        return $resultSet['sold'];
    }
}

?>