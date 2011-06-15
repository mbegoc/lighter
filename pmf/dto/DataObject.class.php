<?php
namespace dto;

abstract class DataObject {
    protected $doc;

    public function __construct(array $doc){
        if(isset($doc["class"])){
            $this->doc = $doc;
        }else{
            throw new DataObjectException("The class element must be present.", 1);
        }
    }

    public function getDoc(){
        return $this->doc;
    }

    public function getId(){
        return $this->doc["_id"];
    }

    abstract public function validate();
}

class DataObjectException extends \Exception {}