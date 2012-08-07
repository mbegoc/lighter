<?php
namespace lighter\models;


abstract class Persistence implements \Iterator {
    /**
     * a list of errors
     * @var array
     */
    private $errors = array();


   /**
    * create a model from its id
    *
    * @param string $id
    * @return lighter\models\Model
    */
    abstract public function get($id);


    /**
     * intialize this object to access the whole set of entries of the database
     *
     * @return lighter\models\Persistence
     */
    abstract public function loadAll();


    /**
     * intialize this object to access a slice of entries of the database
     *
     * @return lighter\models\Persistence
     */
    abstract public function load($qty, $from = 0);


    /**
     * slice the dataset
     *
     * @param int $qty
     * @param int $from
     * @return lighter\models\Persistence
     */
    abstract protected function slice($qty, $from = 0);


    /**
     * sort the data
     *
     * @param array $sort - key = field to sort, value = order (1, -1)
     * @return lighter\models\Persistence
     */
    abstract protected function sort(array $sort);


    /**
     * save the current document to the DB
     *
     * @param lighter\models\Model $model
     */
    abstract public function save(Model $model);


    /**
     * delete an object from the DB
     *
     * @param lighter\models\Model | string $id
     */
    abstract public function remove($id);


    /**
     * return the errors list
     * @return array
     */
    public function getErrors() {
        return $this->errors;
    }


    /**
     * add an error to the errors list so getErrors could expose it
     *
     * @param string $message
     */
    protected function addError($message) {
        $this->errors[] = $message;
    }


    /**
     * empty the errors list
     */
    protected function resetErrors() {
        $this->errors = array();
    }

}

