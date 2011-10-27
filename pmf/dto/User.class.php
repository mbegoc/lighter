<?php
namespace dto;


class User extends DataAccessor {


    public function __construct(){
        parent::__construct('user');
    }

    public function setLastname($lastname){
        $this->doc['lastname'] = $lastname;
    }

    public function getLastname(){
        return $this->doc['lastname'];
    }

    public function setFirstname($firstname){
        $this->doc['firstname'] = $firstname;
    }

    public function getFirstname(){
        return $this->doc['firstname'];
    }

    public function setNickname($nickname){
        $this->doc['nickname'] = $nickname;
    }

    public function getNickname(){
        return $this->doc['nickname'];
    }

    public function setPassword($password){
        $this->doc['password'] = $password;
    }

    public function addGroup($group){
        $this->doc['groups'][] = $group;
    }

    public function getGroups(){
        return $this->doc['groups'];
    }

    public function prepareToDB(){

    }

}

