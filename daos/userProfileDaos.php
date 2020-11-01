<?php
namespace daos;
use daos\baseDaos as BaseDaos;


class UserProfileDaos extends BaseDaos{

    const TABLE_NAME = "userprofiles";

    public function __construct(){
        parent::__construct(self::TABLE_NAME, 'UserProfile');        
    }

    public function getById($id){
        return parent::_getByProperty($id, 'idUser');
    }

    public function add($user){
        return parent::_add($user);
    }

    public function modify($user){
        return parent::_modify($user, $user->getIdUser(), 'idUser');
    }

}

?>