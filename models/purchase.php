<?php
namespace models;

class Purchase{
	private $id;
	private $user;
	private $show;
    private $ticketsQuantity;
    private $discount;
    private $date;
    private $total;

    public function __construct($user = '', $show = '', $ticketsQuantity = '', $discount = '', $date = ''){
		$this->user = $user;
        $this->show = $show;
        $this->ticketsQuantity = $ticketsQuantity;
        $this->discount = $discount;
        $this->date = $date;
    }

    public function getId(){
        return $this->id;
    }

    public function setId($id){
        $this->id = $id;
	}
	
	public function getUser(){
        return $this->user;
    }

    public function setUser($user){
        $this->user = $user;
	}
	
	public function getShow(){
        return $this->show;
    }

    public function setShow($show){
        $this->show = $show;
    }

    public function getTicketsQuantity(){
		return $this->ticketsQuantity;
	}

	public function setTicketsQuantity($ticketsQuantity){
		$this->ticketsQuantity = $ticketsQuantity;
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