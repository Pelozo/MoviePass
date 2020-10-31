<?php
namespace models;


class Show{
    private $id;
    private $idMovie;
    private $idRoom;
    private $datetime;

    public function __construct($idMovie = '', $idRoom = '', $datetime = ''){

        $this->idMovie = $idMovie;
        $this->idRoom = $idRoom;
        $this->datetime = $datetime;
    }

    public function getId(){
        return $this->id;
    }

    public function setId($id){
        $this->id = $id;
    }

    public function getIdMovie(){
        return $this->idMovie;
    }

    public function setIdMovie($idMovie){
        $this->idMovie = $idMovie;
    }

    public function getIdRoom(){
        return $this->idRoom;
    }

    public function setIdRoom($idRoom){
        $this->idRoom = $idRoom;
    }

    public function getDatetime(){
        return $this->datetime;
    }

    public function setDatetime($datetime){
        $this->datetime = $datetime;
    }
}



?>