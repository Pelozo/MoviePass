<?php
namespace models;

class Cinema implements \JsonSerializable{
    private $id;
    private $name;
    private $address;

    public function __construct($name = "vacio", $address = "vacio"){
        $this->name = $name;
        $this->address = $address;
    }

    public function getId(){
        return $this->id;
    }

    public function setId($id){
        $this->id = $id;
    }

    public function getName(){
        return $this->name;
    }

    public function setName($name){
        $this->name = $name;
    }

    public function getAddress(){
        return $this->address;
    }    

    public function setAddress($address){
        $this->address = $address;
    }

    public function jsonSerialize(){
        return get_object_vars($this);
    }

}



?>