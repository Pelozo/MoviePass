<?php
namespace models;

class User implements \JsonSerializable{
	private $id;
    private $email;
    private $password;    
    private $idRol;

    public function __construct($email = '', $password = '', $idRol = -1){
		$this->email = $email;
		$this->password = $password;
        $this->idRol = $idRol;
    }

    public function getId(){
		return $this->id;
	}

	public function setId($id){
		$this->id = $id;
	}

	public function getEmail(){
		return $this->email;
	}

	public function setEmail($email){
		$this->email = $email;
	}

	public function getPassword(){
		return $this->password;
	}

	public function setPassword($password){
		$this->password = $password;
	}


	public function getIdRol(){
		return $this->idRol;
	}

	public function setIdRol($idRol){
		$this->idRol = $idRol;
	}

	public function jsonSerialize(){
        return get_object_vars($this);
    }
}



?>