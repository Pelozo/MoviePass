<?php
namespace models;

class Payment{
    private $id;
    private $cod_aut;
    private $date;
    private $total;

    public function __construct($cod_aut = '', $date = '', $total = ''){
        $this->cod_aut = $cod_aut;
        $this->date = $date;
        $this->total = $total;
    }

    public function getId(){
        return $this->id;
    }

    public function setId($id){
        $this->id = $id;
    }

    public function getCod_aut(){
		return $this->cod_aut;
	}

	public function setCod_aut($cod_aut){
		$this->cod_aut = $cod_aut;
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