<?php
namespace lighter\models\mongodb;


use lighter\exceptions\BadTypeException;

use lighter\handlers\Config;

use \MongoId;
use \Mongo;

use lighter\models\Persistence;
use lighter\models\Model;

use lighter\models\mongodb\MongoModel;

use lighter\exceptions\AlreadyInUseException;


/**
 * a class to access a mongo database
 *
 * @name DataAccessor
 * @abstract
 * @package lighter
 * @subpackage models
 * @see lighter\models\Persistence
 * @since 0.1
 * @version 0.1.1
 * @author Michel Begoc
 * @copyright (c) 2011 Michel Begoc
 * @license MIT - see http://www.opensource.org/licenses/mit-license.php
 *
 */
class MongoManager extends Persistence {
    /**
     * the collection name in which to search the documents
     * @var string
     */
    private $collection = null;
    /**
     * the cursor resulting of a search
     * @var MongoCursor
     */
    private $cursor = null;
    /**
     * the connection data
     * @var array
     */
    private $connexionData = null;
    /**
     * the current model
     * @var lighter\models\mongodb\MongoModel
     */
    protected $model = null;


    /**
     * initialize the DB connection
     *
     * @param string $collection
     */
    public function __construct($collection, $database = 'default') {
        $this->connexionData = Config::getInstance()->getValue('mongodb', $database);

        $connexionString = 'mongodb://'.$this->connexionData['host'].':'.$this->connexionData['port'];
        $mongo = new Mongo($connexionString, array("persistent" => $this->connexionData['database']));
        $this->collection = $mongo->selectCollection($this->connexionData['database'], $collection);
    }


    /**
     * @see lighter\models.Persistence::get()
     */
    public function get($id) {
        $this->setModel($this->collection->findOne(array('_id' => new MongoId($id))));
        return $this->model;
    }


    /**
     * search in the db a set of documents regarding the provided criterias
     * this method is protected and is a convenient method for specific search method
     *  which could be implemented in subsclasses
     *
     * @param array $criterias
     * @throws lighter\exceptions\AlreadyInUseException
     */
    protected function search(array $criterias = array()) {
        if ($this->cursor === null) {
            $this->cursor = $this->collection->find($criterias);
            return $this;
        }else{
            throw new AlreadyInUseException('This MongoManager object is already initialized.', 1);
        }
    }


    /**
     * @see lighter\models.Persistence::loadAll()
     */
    public function loadAll() {
        return $this->search();
    }


    /**
     * @see lighter\models.Persistence::sort()
     */
    public function sort(array $sort) {
        $this->cursor->sort($sort);
        return $this;
    }


    /**
     * @see lighter\models.Persistence::slice()
     */
    public function slice($qty, $from = 0) {
        $this->cursor->skip($from)->limit($qty);
        return $this;
    }


    /**
     * count the number of entries in a collection, regarding the provided criterias
     *
     * @param array $criterias
     * @return int
     */
    public function count(array $criterias = array()) {
        return $this->collection->count($criterias);
    }


    /**
     * @see lighter\models.Persistence::save()
     */
    public function save(Model $model) {
        if ($model->validate()) {
            $model->prepareToStore();
            $this->collection->save($model->getValues(false));
        }
    }


    /**
     * @see lighter\models.Persistence::remove()
     */
    public function remove($id) {
        if ($id instanceof Model) {
            $this->collection->remove(array("_id" => $id->getId()));
        } elseif (is_string($id)) {
            $this->collection->remove(array("_id" => new MongoId($id)));
        } else {
            throw new BadTypeException('The id parameter is not of one of the expected types.');
        }
    }


    /**
     * @see Iterator::next()
     */
    public function next() {
        if ($this->cursor !== null) {
            if ($this->cursor->hasNext()) {
                $this->setModel($this->cursor->getNext());
                return $this->model;
            } else {
                $this->reset();
                return false;
            }
        }else{
            throw new UninitializedException("No search has been performed.", 2);
        }
    }


    /**
     * @see Iterator::current()
     */
    public function current() {
        return $this->model;
    }


    /**
     * @see Iterator::rewind()
     */
    public function rewind() {
        $this->reset();
        $this->next();
    }


    /**
     * @see Iterator::key()
     */
    public function key() {
        return $this->model->getId();
    }


    /**
     * @see Iterator::valid()
     */
    public function valid() {
        return is_object($this->model);
    }


    /**
     * reset this object to its original state
     */
    public function reset() {
        parent::resetErrors();
        $this->cursor->reset();
        $this->cursor = null;
        $this->model = null;
    }


    /**
     * set the model attribute with a model object initialized with the provided data
     *
     * @param array $doc
     */
    protected function setModel(array $doc) {
        $class = $doc['__CLASS__'];
        $this->model = new $class();
        $this->model->setValues($doc);
    }

}

