<?php
namespace dto;


class DBAccessor {
    private $collection = NULL;
    private $cursor = NULL;
    private static $connexionData = NULL;

    public function __construct($collection){
        if(!isset($this->connexionData)){
            $this->connexionData = parse_ini_file("pmf/config/db.ini", true);
            $this->connexionData = $this->connexionData['database'];
        }

        $connexionString = 'mongodb://'.$this->connexionData['host'].':'.$this->connexionData['port'];
        $mongo = new \Mongo($connexionString, array("persistent" => $this->connexionData['database']));
        $this->collection = $mongo->selectCollection($this->connexionData['database'], $collection);
    }

    public function get($id){
        $doc = $this->collection->findOne(array('_id' => new \MongoId($id)));
        $class = $doc["class"];
        return new $class($doc);
    }

    public function search($criterias = array()){
        if($this->cursor === NULL){
            $this->cursor = $this->collection->find($criterias);
        }else{
            throw new DataAccessorException("This DataAccessor object is already initialized. Reset it or fetch all the data before calling this function.", 1);
        }
    }

    public function next(){
        if($this->cursor !== NULL){
            if($this->cursor->hasNext()){
                $doc = $this->cursor->getNext();
                $class = $doc["class"];
                return new $class($doc);
            }else{
                $this->cursor->reset();
                $this->cursor = NULL;
                return NULL;
            }
        }else{
            throw new DataAccessorException("No search has been performed.", 2);
        }
    }

    public function count($criterias = array()){
        return $this->collection->count($criterias);
    }

    public function save(DataObject $object){
        $this->collection->save($object->getDoc());
    }
}

class DataAccessorException extends \Exception {}