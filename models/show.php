<?php
namespace models;


class Show implements \JsonSerializable{
    private $id;
    private $movie;
    private $room;
    private $datetime;

    public function __construct($movie = '', $room = '', $datetime = ''){

        $this->movie = $movie;
        $this->room = $room;
        $this->datetime = $datetime;
    }

    public function getId(){
        return $this->id;
    }

    public function setId($id){
        $this->id = $id;
    }

    public function getMovie(){
        return $this->movie;
    }

    public function setMovie($movie){
        $this->movie = $movie;
    }

    public function getRoom(){
        return $this->room;
    }

    public function setRoom($room){
        $this->room = $room;
    }

    public function getDatetime(){
        return $this->datetime;
    }

    public function setDatetime($datetime){
        $this->datetime = $datetime;
    }

    public function jsonSerialize(){
        return get_object_vars($this);
    }
}



?>