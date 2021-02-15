<?php
namespace controllers;
use daos\ProvinceDaos as ProvinceDaos;

class ProvinceController{

    private $provinceDaos;

    public function __construct(){
        $this->provinceDaos = new ProvinceDaos();
    }

    //and this is where I'd put my "only usable from same package" access modifier IF I HAD ONE.
    public function getAll(){
        return $this->provinceDaos->getAll();
    }

    public function isValid($id){
        return $this->provinceDaos->exists($id);
    }

}


?>