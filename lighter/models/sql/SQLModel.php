<?php
namespace lighter\models\sql;


use lighter\models\Model;


/**
*
* @author Michel Begoc
*
*/
class SQLModel extends Model {
    /**
     * validity flag
     * @var boolean
     */
    protected $valid = true;
    /**
     * a prefix to prepend to values keys
     * @var string
     */
    protected $fieldPrefix = '';
    /**
     * the setValues mode
     * see the setValues method description for more details
     * @var boolean
     */
    protected $quickMode = true;


    /**
     * @param string $prefix
     */
    public function __construct($prefix = '')
    {
        $this->setPrefix($prefix);
        $this->setFilter(array('id'));
    }


    /**
     * set a value
     *
     * @param string $name
     * @param string $value
     */
    public function setValue($name, $value)
    {
        $this->data[$this->fieldPrefix.$name] = $value;
    }


    /**
     * set the prefix to use to access data.
     * It should be set only once before the object starts to be used. If not, the already
     * existing values will be updated, but it could come at the price of a preformance
     * overhead.
     *
     * @param string $prefix
     */
    public function setPrefix($prefix)
    {
        foreach ($this->data as $key => $value) {
            $name = preg_replace('/^'.$this->fieldPrefix.'/', $prefix, $value);
            $this->data[$name] = $value;
            unset($this->data[$key]);
        }
        $this->fieldPrefix = $prefix;
    }


    /**
     * set the values in one shot
     * QuickMode:
     *     determine the way the values are handled
     *     in quick mode, all the values are set, if extra values are given, they will
     *     be added.
     *     not in quick mode, only the data with the field prefix will be kept. It comes
     *     with a time overhead but can avoid a useless duplication of data. The cases in
     *     which the data duplication can occur depend on the way PHP handle fucntion
     *     parameters: a value passed parameter is actually duplicated only when it's
     *     changed, so if the data is not modified, the use of the quick mode doesn't
     *     imply an extra memory consumption.
     *
     * @param array $values
     */
    public function setValues($values)
    {
        if ($this->quickMode) {
            $this->data = $values;
        } else {
            foreach ($values as $key => $value) {
                if (strpos($key, $this->fieldPrefix) === 0) {
                    $this->data[$key] = $value;
                }
            }
        }
    }


    /**
     * turn the quick mode on
     */
    public function quickModeOn() {
        $this->quickMode = true;
    }


    /**
     * turn the quick mode off
     */
    public function quickModeOff() {
        $this->quickMode = true;
    }


    /**
     * return a value, or the provided default if none
     *
     * @param string $name
     *     the key to acceed to the value, i.e. a table field name
     * @param mixed $default - optional - default to null
     *     the default value to return if no value exists
     * @return mixed
     */
    public function getValue($name, $default = null)
    {
        if (isset($this->data[$this->fieldPrefix.$name])) {
            return $this->data[$this->fieldPrefix. $name];
        } else {
            return $default;
        }
    }


    /**
     * @see lighter\models.Model::getId()
     */
    public function getId() {
        if ($this->exists()) {
            return (int)$this->getValue('id');
        } else {
            return null;
        }
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
     * validate the data and return a boolean saying if its valid or not.
     * If not, problems list can be retrieve from the getErrors() method.
     *
     * @return boolean
     */
    public function validateData()
    {
        return $this->valid;
    }


    /**
     * say wether the data already exists in database or not.
     *
     * @return boolean
     */
    public function exists()
    {
        $id = $this->getValue('id', null);
        return $id != null;
    }


    public function setString($field, $string, $length = 0, $errorMessage = null)
    {
        $length = (int)$length;
        $string = trim($string);
        if ($length != 0) {
            if (mb_strlen($string) > $length) {
                $this->valid = false;
                if ($errorMessage != null) {
                    $errorMessage = 'The value set to '.$field.' is larger than the max length than '.$length.'.';
                }
                $this->addError($errorMessage, $field);
            } else {
                $this->setValue($field, $string);
            }
        } else {
            $this->setValue($field, $string);
        }
    }


    public function setInt($field, $value) {
        $this->setValue($field, (int)$value);
    }

}
