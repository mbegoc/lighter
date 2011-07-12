<?php
namespace dto;


/**
 * The mother class for the MongoDB Documents
 *
 */
abstract class DataObject {
    /**
     * the document itself
     * @var array
     */
    protected $doc;


    /**
     *
     * @param $doc
     */
    public function __construct(array $doc){
        if(isset($doc["class"])){
            $this->doc = $doc;
        }else{
            throw new DataObjectException("The class element must be present.", 1);
        }
    }


    /**
     * return the raw document - usefull for inserting in Database, should not be called in other context
     */
    public function getDoc(){
        return $this->doc;
    }


    /**
     * return the document id
     */
    public function getId(){
        return $this->doc["_id"];
    }


    /**
     * a method to be called just before inserting the data in the DB.
     * Here you can check data validity, remove, add or change information
     * $param string $class
     */
    public function prepareToDB($class){
        $this->doc['class'] = $class;
    }


    /**
     * @return string
     */
    public function __toString(){
        return json_encode($this->doc);
    }

}


class DataObjectException extends \Exception {}

