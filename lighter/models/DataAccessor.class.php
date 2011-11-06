<?php
namespace lighter\models;


use \MongoId;
use \Mongo;
use \Exception;


/**
 * a class to access a mongo database
 *
 * @name DataAccessor
 * @abstract
 * @package lighter
 * @subpackage models
 * @since 0.1
 * @version 0.1
 * @author Michel Begoc
 * @copyright (c) 2011 Michel Begoc
 * @license MIT - see http://www.opensource.org/licenses/mit-license.php
 *
 */
abstract class DataAccessor {
    /**
     * the collection name in which to search the documents
     *
     * @var string
     */
    private $collection = NULL;
    /**
     * the cursor resulting of a search
     *
     * @var MongoCursor
     */
    private $cursor = NULL;
    /**
     * the connection data
     *
     * @var array
     */
    private static $connexionData = NULL;
    /**
     * the mongo document
     *
     * @var array
     */
    protected $doc;


    /**
     * initialize the DB connection
     * constructor is protected by default because of the potential singletons (such
     * as Config) which could inheritate of this class. The singleton pattern need a
     * restricted visibility for the constructor. Other subclasses can extend the
     * default visibility of the constructor, while the opposite is impossible.
     *
     * @param string $collection
     */
    protected function __construct($collection){
        if(!isset($this->connexionData)){
            $this->connexionData = parse_ini_file("lighter/config/db.ini", true);
            $this->connexionData = $this->connexionData['database'];
        }

        $connexionString = 'mongodb://'.$this->connexionData['host'].':'.$this->connexionData['port'];
        $mongo = new Mongo($connexionString, array("persistent" => $this->connexionData['database']));
        $this->collection = $mongo->selectCollection($this->connexionData['database'], $collection);
    }


    /**
     * a method to be called just before inserting the data in the DB.
     * Here you can check data validity, remove, add or change information
     *
     * @abstract
     * @param string $class
     */
    protected abstract function prepareToDB();


    /**
     * a method which permit to initialize an object in one call.
     *
     * @param array - keys = fields names / values = values
     * @param string - a prefix which could be used to prefix each field name in the
     *         html form
     */
    public abstract function setValues(array $values, $prefix = "");


    /**
     * load document from its id
     *
     * @param string $id
     */
    public function load($id){
        $this->doc = $this->collection->findOne(array('_id' => new MongoId($id)));
    }


    /**
     * search in the db a set of documents regarding the provided criterias
     * this method is protected and is a convenient method for specific search method
     *  which could be implemented in subsclasses
     *
     * @param array $criterias
     * @throws DataAccessorException
     */
    protected function search(array $criterias = array()){
        if($this->cursor === NULL){
            $this->cursor = $this->collection->find($criterias);
        }else{
            throw new DataAccessorException("This DataAccessor object is already initialized. Reset it or fetch all the data before calling this function.", 1);
        }
    }


    /**
     * intialize this object to access the whole set of documents of the database
     *
     * @return DBAccessor
     */
    public function loadAll(){
        $this->search();
        return $this;
    }


    /**
     * sort the data
     *
     * @param array $sort - key = field to sort, value = order (1, -1)
     * @return DBAccessor
     */
    public function sort(array $sort){
        $this->cursor->sort($sort);
        return $this;
    }


    /**
     * slice the dataset
     *
     * @param int $qty
     * @param int $from
     * @return DBAccessor
     */
    public function slice($qty, $from = 0){
        $this->cursor->skip($from)->limit($qty);
        return $this;
    }


    /**
     * load the next document from a previous search
     *
     * @throws DataAccessorException
     */
    public function next(){
        if($this->cursor !== NULL){
            if($this->cursor->hasNext()){
                $this->doc = $this->cursor->getNext();
                return true;
            }else{
                $this->cursor->reset();
                $this->cursor = NULL;
                return false;
            }
        }else{
            throw new DataAccessorException("No search has been performed.", 2);
        }
    }


    /**
     * count the number of entries in a collection, regarding the provided criterias
     *
     * @param array $criterias
     */
    public function count(array $criterias = array()){
        return $this->collection->count($criterias);
    }


    /**
     * save the current document to the DB
     */
    public function save(){
        $this->prepareToDB();
        $this->collection->save($this->doc);
    }


    /**
     * delete an object from the DB
     *
     * @param string $id
     */
    public function delete(){
        $this->collection->remove(array("_id" => $this->doc['_id']));
    }


    /**
     * delete an object from its id
     *
     * @param string $id
     */
    public function deleteFromId($id){
        $this->collection->remove(array("_id" => new MongoId($id)));
    }


    /**
     * return the document id
     *
     * @return string
     */
    public function getId(){
        return (string)$this->doc["_id"];
    }


    /**
     * @return string
     */
    public function __toString(){
        return json_encode($this->doc);
    }

}


/**
 * Exception thrown by the DataAccessor
 *
 * @author michel
 *
 */
class DataAccessorException extends Exception {}
