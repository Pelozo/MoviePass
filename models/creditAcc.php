<?php
namespace models;

class CreditAcc{
    private $id;
    private $company;

    public function __construct($company = ''){
        $this->company = $company;
    }

    public function getId(){
        return $this->id;
    }

    public function setId($id){
        $this->id = $id;
    }

    public function getCompany(){
		return $this->company;
	}

	public function setCompany($company){
		$this->company = $company;
	}
}
?>