<?php
namespace daos;
use daos\baseDaos as BaseDaos;
use models\ticket as Ticket;

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

        $this->connection = Connection::getInstance();
        return $this->connection->executeNonQuery($query, $params);

    }

    public function remove($id){
        return parent::_remove($id, 'id');
    }

}

?>