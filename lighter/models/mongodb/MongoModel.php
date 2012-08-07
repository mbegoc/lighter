<?php
namespace lighter\models\mongodb;


use lighter\models\Model;


/**
 * The mongodb implementation of the lighter\models\Model class.
 *
 * @name MongoModel
 * @package lighter
 * @subpackage models\mongodb
 * @see lighter\models\Model
 * @since 0.1.1
 * @version 0.1.1
 * @author Michel Begoc
 * @copyright (c) 2011 Michel Begoc
 * @license MIT - see http://www.opensource.org/licenses/mit-license.php
 *
 */
class MongoModel extends Model {


    /**
     * just initialize a new MongoModel
     */
    public function __construct() {
        $this->data['__CLASS__'] = get_class($this);
        $this->setFilter(array('_id', '__CLASS__'));
    }


    /**
     * @see lighter\models.Model::getId()
     */
    public function getId() {
        return (string)$this->data['_id'];
    }


    /**
     * @see lighter\models.Model::prepareToStore()
     */
    public function prepareToStore() {}


    /**
     * @see lighter\models.Model::validate()
     */
    public function validate() {
        return true;
    }


    /**
     * @see lighter\models.Model::exists()
     */
    public function exists() {
        return isset($this->data['_id']);
    }


    /**
     * @return string
     */
    public function __toString() {
        return json_encode($this->data);
    }

}

