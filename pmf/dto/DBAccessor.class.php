<?php
namespace dto;


use \MongoId;
use \Mongo;
use \Exception;

/**
 * a class to access a mongo database
 * @author michel
 *
 */
class DBAccessor {
    /**
     * the collection name in which to search for the documents
     * @var string
     */
    private $collection = NULL;
    /**
     * the cursor resulting of a search
     * @var MongoCursor
     */
    private $cursor = NULL;
    /**
     * the connection data
     * @var array
     */
    private static $connexionData = NULL;


    /**
     * initialize the DB connection
     * @param string $collection
     */
    public function __construct($collection){
        if(!isset($this->connexionData)){
            $this->connexionData = parse_ini_file("pmf/config/db.ini", true);
            $this->connexionData = $this->connexionData['database'];
        }

        $connexionString = 'mongodb://'.$this->connexionData['host'].':'.$this->connexionData['port'];
        $mongo = new Mongo($connexionString, array("persistent" => $this->connexionData['database']));
        $this->collection = $mongo->selectCollection($this->connexionData['database'], $collection);
    }


    /**
     * get a document from its id
     * @param string $id
     */
    public function get($id){
        $doc = $this->collection->findOne(array('_id' => new MongoId($id)));
        $class = $doc["_class_"];
        return new $class($doc);
    }


    /**
     * search in the db a set of documents regarding the provided criterias
     * this method is protected and is a convenient method for specific search method which could be implemented in subsclasses
     * such classes aren't mandatory, but if we need specific searches method to be implemented for an object type, we should create subclasses
     * @param array $criterias
     * @param int $qty
     * @param int $from
     * @throws DataAccessorException
     */
    public function search(array $criterias = array(), $qty = 200, $from = 0, $sort = array('$natural' => 1)){
        if($this->cursor === NULL){
            $this->cursor = $this->collection->find($criterias)->sort($sort)->skip($from)->limit($qty);
        }else{
            throw new DataAccessorException("This DataAccessor object is already initialized. Reset it or fetch all the data before calling this function.", 1);
        }
    }


    /**
     * sort the data
     * @param array $sort - key = field to sort, value = order (1, -1)
     */
    public function sort(array $sort){
        $this->cursor->sort($sort);
    }


    /**
     * return the next object from a previous search
     * @throws DataAccessorException
     */
    public function next(){
        if($this->cursor !== NULL){
            if($this->cursor->hasNext()){
                $doc = $this->cursor->getNext();
                $class = $doc["_class_"];
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


    /**
     * count the number of entries in a collection, regarding the provided criterias
     * @param array $criterias
     */
    public function count(array $criterias = array()){
        return $this->collection->count($criterias);
    }


    /**
     * save a DataObject to the DB
     * @param DataObject $object
     */
    public function save(DataObject $object){
        $object->prepareToDB();
        $this->collection->save($object->getDoc());
    }


    /**
     * delete an object from the DB
     * @param string $id
     */
    public function delete($id){
        $this->collection->remove(array("_id" => new MongoId($id)));
    }
}


class DataAccessorException extends Exception {}
