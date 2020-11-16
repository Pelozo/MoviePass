<?php
namespace models;

class Ticket implements \JsonSerializable{
    private $purchase;
    private $ticket_number;
    private $qr;

    public function __construct($purchase = '', $ticket_number = ''){
        $this->purchase = $purchase;
        $this->ticket_number = $ticket_number;
        $this->qr = $this->generateQr();
    }

    public function getPurchase(){
        return $this->purchase;
    }

    public function setPurchase($purchase){
        $this->purchase = $purchase;
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

    public function jsonSerialize(){
        return get_object_vars($this);
    }

    private function generateQr(){
        $toEncode['ticketNumber'] = $this->ticket_number;
        $toEncode['purchaseId'] = $this->purchase->getId();
        return QR_URL . urlencode(json_encode($toEncode));
    }
}
?>