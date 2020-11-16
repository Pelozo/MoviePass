<?php
namespace models;


class Room implements \JsonSerializable{

    private $id;
    private $name;
    private $price;
    private $capacity;
    private $cinema;

    public function __construct($name = '', $price= -1, $capacity= -1, $cinema = null){
        $this->name = $name;
        $this->price = $price;
        $this->capacity = $capacity;
        $this->cinema = $cinema;
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

	public function getPrice(){
		return $this->price;
	}

	public function setPrice($price){
		$this->price = $price;
	}

	public function getCapacity(){
		return $this->capacity;
	}

	public function setCapacity($capacity){
		$this->capacity = $capacity;
	}

	public function getCinema(){
		return $this->cinema;
	}

	public function setCinema($cinema){
		$this->cinema = $cinema;
	}

	public function jsonSerialize(){
        return get_object_vars($this);
    }

}