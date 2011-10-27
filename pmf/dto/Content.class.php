<?php
namespace dto;


class Content extends DataAccessor {


    public function __construct(){
        parent::__construct('content');
    }

    public function setTitle($title){
        $this->doc['title'] = $title;
    }

    public function getTitle(){
        return $this->doc['title'];
    }

    public function setContent($Content){
        $this->doc['Content'] = $Content;
    }

    public function getContent(){
        return $this->doc['Content'];
    }

    public function prepareToDB(){

    }

}

