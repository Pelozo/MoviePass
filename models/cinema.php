<?php
namespace models;



class Cinema implements \JsonSerializable{
    private $id;
    private $name;
    private $address;
    private $city;
    private $zip;
    private $province;

    public function __construct($name = "vacio", $address = "vacio", $city = "vacio", $zip = -1, $province = "vacio"){
        $this->name = $name;
        $this->address = $address;
        $this->city = $city;
        $this->zip = $zip;
        $this->province = $province;
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

    public function getCity(){
        return $this->city;
    }

    public function setCity($city){
        $this->city = $city;
    }

    public function getZip(){
        return $this->zip;
    }

    public function setZip($zip){
        $this->zip = $zip;
    }

    public function getProvince(){
        return $this->province;
    }

    public function setProvince($province){
        $this->province = $province;
    }

    public function jsonSerialize(){
        return get_object_vars($this);
    }

}



?>