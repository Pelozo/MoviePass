<?php
namespace daos;
use daos\BaseDaos as BaseDaos;


class UserDaos extends BaseDaos{

    const TABLE_NAME = "users";

    public function __construct(){
        parent::__construct(self::TABLE_NAME, 'User');        
    }

    public function getAll(){
        return parent::_getAll();
    }

    public function exists($email){
        return parent::_exists($email, 'email');
    }

    public function getById($id){
        return parent::_getByProperty($id, 'id');
    }

    public function getByEmail($email){
        return parent::_getByProperty($email, 'email');
    }

    public function add($user){
        return parent::_add($user);
    }

    public function modify($user){
        return parent::_modify($user, $user->getId(), 'id');
    }

}

?>