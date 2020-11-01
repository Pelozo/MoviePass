<?php
namespace models;


class UserProfile{

    private $idUser;
    private $firstName;
    private $lastName;
    private $dni;


    public function __construct($firstName = '', $lastName = '', $dni = ''){
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->dni = $dni;
    }

    public function getIdUser(){
		return $this->idUser;
	}

	public function setIdUser($idUser){
		$this->idUser = $idUser;
	}

    public function getFirstName(){
		return $this->firstName;
	}

	public function setFirstName($firstName){
		$this->firstName = $firstName;
	}

	public function getLastName(){
		return $this->lastName;
	}

	public function setLastName($lastName){
		$this->lastName = $lastName;
    }
    
    public function setDni($dni){
		$this->dni = $dni;
	}

	public function getDni(){
		return $this->dni;
	}



}



?>