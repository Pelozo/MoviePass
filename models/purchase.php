<?php
namespace models;

class Purchase{
    private $id;
    private $tickets_quantity;
    private $discount;
    private $date;
    private $total;

    public function __construct($tickets_quantity = '', $discount = '', $date = '', $total = ''){
        $this->tickets_quantity = $tickets_quantity;
        $this->discount = $discount;
        $this->date = $date;
        $this->total = $total;
    }

    public function getId(){
        return $this->id;
    }

    public function setId($id){
        $this->id = $id;
    }

    public function getTickets_quantity(){
		return $this->tickets_quantity;
	}

	public function setTickets_quantity($tickets_quantity){
		$this->tickets_quantity = $tickets_quantity;
	}

	public function getDiscount(){
		return $this->discount;
	}

	public function setDiscount($discount){
		$this->discount = $discount;
	}

	public function getDate(){
		return $this->date;
	}

	public function setDate($date){
		$this->date = $date;
	}

	public function getTotal(){
		return $this->total;
	}

	public function setTotal($total){
		$this->total = $total;
	}
}
?>