<?php
namespace daos;
use daos\baseDaos as BaseDaos;
use models\ticket as Ticket;
use models\Purchase as Purchase;
use models\user as User;
class TicketDaos extends BaseDaos{
    
    const TABLE_NAME = 'tickets';

    public function __construct(){
        parent::__construct(self::TABLE_NAME, 'Ticket');
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

    public function add($ticket){

        $query = "INSERT INTO " . self::TABLE_NAME . " (idPurchase_ticket, ticketNumber_ticket, qr_ticket)
                                                        values(:idPurchase_ticket, :ticketNumber_ticket, :qr_ticket);";
        
        $params['idPurchase_ticket'] = $ticket->getPurchase()->getId();
        $params['ticketNumber_ticket'] = $ticket->getTicket_number();
        $params['qr_ticket'] = $ticket->getQr();

        try{
            $this->connection = Connection::getInstance();
            return $this->connection->executeNonQuery($query, $params);
        }catch(\Exception $ex){
            throw $ex;
        }

    }

    public function remove($id){
        return parent::_remove($id, 'id');
    }


    public function getByUser($idUser){
        $query = "select t.*, p.*, u.*, s.* from tickets t
        LEFT JOIN purchases p on t.idPurchase_ticket = p.id_purchase
        LEFT JOIN users u ON p.idUser_purchase = u.id_user
        LEFT JOIN shows s ON s.id_show = p.idShow_purchase
        WHERE u.id_user = :idUser
        AND s.datetime_show > NOW()
        ORDER BY s.datetime_show";

        $parameters['idUser'] = $idUser;

        try{
            $showDaos = new ShowDaos();
            $connection = Connection::getInstance();
            $resultSet = $connection->executeWithAssoc($query, $parameters);

            $results = array();
            foreach ($resultSet as $ticket){
                $object = new Ticket(
                                new Purchase(
                                    null,
                                    $showDaos->getById($ticket['idShow_purchase']),
                                    $ticket['ticketsQuantity_purchase'],
                                    $ticket['discount_purchase'],
                                    $ticket['date_purchase']
                                ),
                                $ticket['ticketNumber_ticket']
                            );
                $object->getPurchase()->setId($ticket['id_purchase']);
                //$object->getPurchase()->getUser()->setId($ticket['id_user']);

                $results[] = $object;
            }
            return $results;
        }
        catch(\Exception $ex){
            throw $ex;
        }


    }

    



}

?>