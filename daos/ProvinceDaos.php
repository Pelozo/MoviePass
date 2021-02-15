<?php
namespace daos;
use daos\BaseDaos as BaseDaos;
use models\Province as Province;

class ProvinceDaos extends BaseDaos{

    const TABLE_NAME = "provinces";

    public function __construct(){
        parent::__construct(self::TABLE_NAME, 'Province');        
    }

    public function exists($id){
        return parent::_exists($id);
    }

    public function getAll(){
        return parent::_getAll();
    }


}