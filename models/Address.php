<?php
namespace models;

class Address implements \JsonSerializable{
    private $id;
    private $block;
    private $city;
    private $zip;
    private $province;

    public function __construct($block = "vacio", $city = "vacio", $zip = "vacio", $province = -1){
        $this->block = $block;
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

	public function getBlock(){
		return $this->block;
	}

	public function setBlock($block){
		$this->block = $block;
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

	public function __toString(){
		return $this->block. ", " . $this->city . " (" . $this->zip . "), " .  $this->province->getName();
	}

}


?>