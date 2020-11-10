<?php
namespace models;

class Ticket{
    private $id;
    private $ticket_number;
    private $qr;

    public function __construct($ticket_number = '', $qr = ''){
        $this->ticket_number = $ticket_number;
        $this->qr = $qr;
    }

    public function getId(){
        return $this->id;
    }

    public function setId($id){
        $this->id = $id;
    }

    public function getTicket_number(){
		return $this->ticket_number;
	}

	public function setTicket_number($ticket_number){
		$this->ticket_number = $ticket_number;
	}

	public function getQr(){
		return $this->qr;
	}

	public function setQr($qr){
		$this->qr = $qr;
	}
}
?>