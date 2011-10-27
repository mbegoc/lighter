<?php
namespace dto;


class User extends DataAccessor {


    public function __construct(){
        parent::__construct('group');
    }

    public function setName($name){
        $this->doc['name'] = $name;
    }

    public function getName(){
        return $this->doc['name'];
    }

    public function setDescription($description){
        $this->doc['description'] = $description;
    }

    public function getDescription(){
        return $this->doc['description'];
    }

    public function prepareToDB(){

    }

}

