<?php
namespace models;


class Show{
    private $id;
    private $movie;
    private $room;
    private $date;

    public function __construct($movie, $room, $date){

        $this->movie = $movie;
        $this->room = $room;
        $this->date = $date;
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
        return $this->date;
    }

    public function setDatetime($date){
        $this->date = $date;
    }
}



?>