<?php
namespace lighter\models\sql;


use lighter\models\sql\SQLModel;
use lighter\models\sql\Database;

use lighter\models\Persistence;
use lighter\models\Model;


/**
 * Manage SQL generation to access to database through Database object.
 * Offers SQLManager service to the Model objects.
 *
 * @author Michel Begoc
 *
 */
class SQLManager extends Persistence {
    /**
     * provides database access
     * @var Database
     */
    protected $database = null;
    /**
     * table name
     * @var string
     */
    protected $table = null;
    /**
     * Model name to use
     * @var string
     */
    protected $modelName = null;
    /**
     * the current model object
     * @var lighter\models\sql\SQLModel
     */
    protected $currentModel = null;
    /**
     * the sql statement to execute
     * @var string | array
     */
    protected $sql = array();
    /**
     * the last executed sql statement
     * @var string
     */
    protected $lastSql = null;
    /**
     * the values to use to execute the query
     * @var array
     */
    private $values = array();
    /**
     * the last values used to execute a query
     * @var array
     */
    private $lastValues = array();
    /**
     * the fields list of the table
     * @var array
     */
    protected $fields;
    /**
     * the joins meta data to manage results
     * @var array
     */
    protected $joins = array();
    /**
     * the current row from the sql resultset
     * @var array
     */
    protected $currentData = null;
    /**
     * say if query have to be actually executed
     * @var boolean
     */
    protected $sendQuery = true;
    /**
     * the field prefix separator to use to construct fields aliases
     * @var string
     */
    protected $fieldPrefixSeparator = '__';
    /**
     * the database object escape character to avoid conflict with sql keywords
     * @var string
     */
    protected $fieldQuote = '';
    /**
     * flag if this object is ready to be iterate
     * @var boolean
     */
    protected $ready = true;


    /**
     * intialize an instance of this object
     *
     * @param string $table
     *     the table name associated with this instance
     * @param string $model - optional - default to Model
     *     the model class name to use to return data
     * @param string $prefix - optional - default to configuration value
     *     the prefix to use to construct table names
     * @param string $databaseName - optional - default to null
     *     the database config name to use to initialize the Database object
     */
    public function __construct($table, $modelName = 'lighter\models\sql\SQLModel', $prefix = null, $databaseName = 'default') {
        $this->database = new Database($databaseName);
        if ($prefix === null) {
            $prefix = $this->database->getPrefix();
        }
        $this->fieldQuote = $this->database->getFieldProtector();
        $this->table = $prefix.$table;
        $this->modelName = $modelName;
        $this->fields = $this->database->getFields($this->table);
    }


    /**
     * save a model to DB
     *
     * @param lighter\models\Model $model
     * @return boolean
     */
    public function save(Model $model) {
        if ($model->validate()) {
            $model->prepareToStore();
            if ($model->exists()) {
                return $this->updateModel($model);
            } else {
                return $this->insertModel($model);
            }
        }
    }


    /**
     * insert a model to database
     *
     * @param lighter\models\Model $model
     */
    protected function insertModel(Model $model) {
        $success  = false;
        foreach ($this->fields as $field) {
            $value = $model->getValue($field['name'], null);
            if ($field['name'] != 'id' && $value != null) {
                $this->insert($field['name']);
                $this->addValue($value);
            }
        }
        if ($this->execute()) {
            $model->setInt('id', $this->database->getInsertId());
            $success = true;
        }
        $this->reset();
        return $success;
    }


    /**
     * update a model
     *
     * @param Model $model
     * @return boolean
     */
    protected function updateModel(Model $model) {
        $success = false;
        foreach ($this->fields as $field) {
            $value = $model->getValue($field['name'], null);
            if ($field['name'] != 'id' && $value != null) {
                $this->update($field['name']);
                $this->addValue($value);
            }
        }
        $this->where($this->field('id').' = ?');
        $this->addValue($model->getId());
        $success = $this->execute();
        $this->reset();
        return $success;
    }


    /**
     * delete an entry against its id
     *
     * @param int | Model $id
     * @return boolean
     */
    public function remove($id) {
        if ($id instanceof Model) {
            $id = $id->getId();
        }
        $success = $this->delete()
            ->where($this->field('id').' = ?')
            ->addValue($id)
            ->execute();
        $this->reset();
        return $success;
    }


    /**
     * retrieve a row from a table against its id
     *
     * @param int $id
     * @return Model
     */
    public function get($id) {
        $this->select()
            ->where($this->field('id').' = ?')
            ->addValue($id)
            ->execute();
        return $this->next();
    }


    /**
     * return last errors
     *
     * @return array
     */
    public function getLastError() {
        return $this->database->getLastError();
    }


    /**
     * load the whole data or a part of the data of a table, without restriction criteria.
     *
     * @param int $limit - optional - default to none
     * @param int $offset - optional - default to none
     */
    public function loadAll() {
        return $this->select()->execute();
    }


    /**
     * load the whole data or a part of the data of a table, without restriction criteria.
     *
     * @param int $limit - optional - default to none
     * @param int $offset - optional - default to none
     */
    public function load($limit = null, $offset = null) {
        return $this->select()
            ->slice($limit, $offset)
            ->execute();
    }


    /**
     * return the next model object if any or false otherwise.
     *
     * @param boolean $quick - option - default to true
     *     decide which algorithm to use to populate models.
     *     if true, quick algorithm is used: the whole data will be passed to the models
     *     in case of joined table. Thus, potentially large amount of data could be
     *     duplicated.
     *     if false, only the good values are passed to the model. In some case, this
     *     could avoid large memory consumption, at the cost of rapidity.
     *     In most cases, quick algorithm should work better
     * @return Model|boolean
     *
     * @see Iterator::next()
     */
    public function next($quick = true) {
        $this->ready = false;
        // to be able to handle properly the joined data row, we
        if ($this->currentData === null) {
            $this->currentData = $this->database->next();
        }
        if ($this->currentData !== false) {
            $this->currentModel = new $this->modelName();
            $this->currentModel->setPrefix($this->table.'__');
            $this->currentModel->setValues($this->currentData, $quick);
            $db = clone $this->database;
            $db->reset();
            $models = array($this->table => array($this->currentModel->getId() => $this->currentModel));
            while ($this->currentData[$this->fieldAlias('id', $this->table)] == $this->currentModel->getId()) {
                foreach ($this->joins as $joinId => $join) {
                    if ($this->currentData[$this->fieldAlias($join['field'], $joinId)] != null) {
                        $subModel = new $join['model']();
                        $subModel->setPrefix($joinId.$this->fieldPrefixSeparator);
                        $subModel->setValues($this->currentData, $quick);
                        if (!isset($models[$joinId])) {
                            $models[$joinId] = array();
                        }
                        $models[$joinId][$this->currentData[$this->fieldAlias('id', $joinId)]] = $subModel;
                        if (isset($models[$join['from']])) {
                            if (isset($models[$join['from']][$this->currentData[$this->fieldAlias('id', $join['from'])]])) {
                                $models[$join['from']][$this->currentData[$this->fieldAlias('id', $join['from'])]]->link($subModel, $joinId);
                            }
                        }
                    }
                }
                $this->currentData = $this->database->next();
            }
            return $this->currentModel;
        } else {
            $this->reset();
            return false;
        }
    }


    /**
     * @see Iterator::current()
     */
    public function current() {
        return $this->currentModel;
    }


    /**
     * @see Iterator::rewind()
     */
    public function rewind() {
        if (!$this->ready) {
            $this->reset();
            $this->ready = true;
        }
        $this->next();
    }


    /**
     * @see Iterator::key()
     */
    public function key() {
        return $this->currentModel->getId();
    }


    /**
     * @see Iterator::valid()
     */
    public function valid() {
        return is_object($this->currentModel);
    }


    /**
     * return the last executed query
     *
     * @return string
     */
    public function getSql() {
        return $this->lastSql;
    }


    /**
     * return the last executed query with the parameters place holders replaced by their
     * actual values
     *
     * @return string
     */
    public function getReadableSql() {
        $nb = count($this->values);
        if ($nb > 0) {
            $placeHolders = array_fill(0, count($this->values), '/\?/');
            foreach ($this->values as $key => $value) {
                $this->values[$key] = $this->database->escape($value);
            }
            return preg_replace($placeHolders, $this->values, $this->lastSql, 1);
        } else {
            return $this->lastSql;
        }
    }


    /**
     * return the values set for the last query
     *
     * @return array
     */
    public function getValues() {
        return $this->lastValues;
    }


    /**
     * reset this object to its initial state
     */
    public function reset() {
        parent::resetErrors();
        $this->database->reset();
        $this->sql = array();
        $this->currentData = null;
        $this->currentModel = null;
        $this->values = array();
        $this->joins = array();
    }


    /**
     * say wether this object is free to execute a query or not
     * @return boolean
     */
    public function isFree() {
        return $this->database->isFree();
    }


    /**
     * say wether this object is set to execute query or not
     * @return boolean
     */
    public function isSendingQuery() {
        return $this->sendQuery;
    }


    /**
     * deactivate query execution, permit to simulate a query execution, to get the sql
     * statement for example.
     */
    public function doNotSendQuery() {
        $this->sendQuery = false;
    }


    /**
     * activate query execution
     */
    public function doSendQuery() {
        $this->sendQuery = true;
    }


    /**
     * produce a sql insert sentence. This function have to be called one time for
     * each field which have to be inserted. This method will automatically
     * produce a complete well formatted sql insert sentence however how many fields
     * have been added.
     *
     * @param string $field
     *		the field name
     * @param SQLManager | string $value - optional - default to `?`
     * 		the value to use. The default is a placeholder of prepared statement
     * 		but you can use any valid sql statement, such as 10, '2012-12-12',
     * 		UPPER(?) or NOW()...
     * 		You should only be careful to use the prepared statement placeholder for
     * 		user data to properly secure you query.
     * 		If a SQLManager is provided as the value, it will be used to get sql and
     * 		produce a query of the form INSERT INTO `table` SELECT ...
     * 		This kind
     * @return SQLManager
     * @throws Exception
     */
    protected function insert($field, $value = '?') {
        if (count($this->sql) == 0) {
            $this->sql['insert'] = 'INSERT INTO '.$this->fieldQuote.$this->table.$this->fieldQuote.'(';
            $this->sql['insert'].= $this->field($field);
            $this->sql['values'] = ') VALUES (';
            $this->sql['end'] = ')';
        } elseif(isset($this->sql['insert'])) {
            $this->sql['insert'].= ', '.$this->field($field);
            $this->sql['values'].= ', ';
        } else {
            throw new Exception('Cannot apply a select clause on this query.');
        }
        if ($value instanceof SQLManager) {
            $this->sql['values'] = ') '.$value->getSql();
        } else {
            $this->sql['values'].= $value;
        }
        return $this;
    }


    /**
     * produce a sql update sentence. This function have to be called one time for
     * each field which have to be inserted. This method will automatically
     * produce a complete well formatted sql update sentence however how many fields
     * have been added.
     *
     * @param string $field
     *		the field name
     * @param string $value - optional - default to `?`
     * 		the value to use. The default is a placeholder of prepared statement
     * 		but you can use any valid sql statement, such as 10, '2012-12-12',
     * 		UPPER(?) or NOW()...
     * 		You should only be careful to use the prepared statement placeholder for
     * 		user data to properly secure you query.
     *
     * @param string $field
     * @param string $value
     * @return SQLManager
     * @throws Exception
     */
    protected function update($field, $value = '?') {
        if (count($this->sql) == 0) {
            $this->sql['update'] = 'UPDATE '.$this->fieldQuote.$this->table.$this->fieldQuote;
            $this->sql['values'] = ' SET '.$this->field($field).' = '.$value;
            $this->sql['where'] = '';
            $this->sql['limit'] = '';
        } elseif(isset($this->sql['update'])) {
            $this->sql['values'].= ', '.$this->field($field).' = '.$value;
        } else {
            throw new Exception('Cannot apply a select clause on this query.');
        }
        return $this;
    }


    /**
     * initiate a delete query. This should be completed at least with where clause
     * and limit clase as needed.
     *
     * @return SQLManager
     * @throws Exception
     */
    protected function delete() {
        if (count($this->sql) == 0) {
            $this->sql['delete'] = 'DELETE FROM '.$this->fieldQuote.$this->table.$this->fieldQuote;
            $this->sql['where'] = '';
            $this->sql['limit'] = '';
        } else {
            throw new Exception('Cannot apply a delete clause on this query.');
        }
        return $this;
    }


    /**
     * add a select statement to the query and format the fiedls to select.
     *
     * @param array $fields - optional - default to the fields attribute content
     *     the fields to select
     * @param string $joinId - optional - default to table attribute
     *     the joinId which the fields are related too
     * @return SQLManager
     */
    protected function select(array $fields = null, $joinId = null) {
        if (count($this->sql) == 0 || isset($this->sql['select'])) {
            if ($fields === null) {
                $fields = $this->fields;
            }
            if ($joinId == null) {
                $joinId = $this->table;
            }
            if (!isset($this->sql['select'])) {
                $this->sql = array(
                    'select' => 'SELECT ',
                    'from' => ' FROM '.$this->fieldQuote.$this->table.$this->fieldQuote,
                    'join' => '',
                    'where' => '',
                    'order' => '',
                	'limit' => '',
                );
                $sep = '';
            } else {
                $sep = ', ';
            }
            foreach ($fields as $field) {
                if (is_array($field) && isset($field['name'])) {
                    $name = $field['name'];
                } else {
                    $name = $field;
                }
                $this->sql['select'].= $sep.$this->field($name, $joinId).' AS '.$this->fieldAlias($name, $joinId);
                $sep = ', ';
            }
        } else {
            throw new Exception('Cannot apply a select clause on this query.');
        }
        return $this;
    }


    /**
     * convenience method to perform leftJoin
     *
     * @see SQLManager::join()
     */
    protected function leftJoin($rightTable, $rightField, $leftField, $modelName = 'lighter\models\sql\SQLModel', $leftTable = null, $joinId = null, array $fields = null) {
        return $this->join($rightTable, $rightField, $leftField, $modelName, $leftTable, $joinId, $fields, 'LEFT');
    }


    /**
     * convenience method to perform innerJoin
     *
     * @see SQLManager::join()
     */
    protected function innerJoin($rightTable, $rightField, $leftField, $modelName = 'lighter\models\sql\SQLModel', $leftTable = null, $joinId = null, array $fields = null) {
        return $this->join($rightTable, $rightField, $leftField, $modelName, $leftTable, $joinId, $fields, 'INNER');
    }


    /**
     * convenience method to perform outerJoin
     *
     * @see SQLManager::join()
     */
    protected function outerJoin($rightTable, $rightField, $leftField, $modelName = 'lighter\models\sql\SQLModel', $leftTable = null, $joinId = null, array $fields = null) {
        return $this->join($rightTable, $rightField, $leftField, $modelName, $leftTable, $joinId, $fields, 'OUTER');
    }


    /**
     * convenience method to perform rightJoin
     *
     * @see SQLManager::join()
     */
    protected function rightJoin($rightTable, $rightField, $leftField, $modelName = 'lighter\models\sql\SQLModel', $leftTable = null, $joinId = null, array $fields = null) {
        return $this->join($rightTable, $rightField, $leftField, $modelName, $leftTable, $joinId, $fields, 'RIGHT');
    }


    /**
     * join a table into the current sql query
     *
     * @param string $rightTable
     *     the target table of the join
     * @param string $rightField
     *     the field of the target table to use to perform the join
     * @param string $leftField
     *     the field of the main table to use to perform the join
     * @param string $modelName - optional - default to Model
     *     the model class name to use to manage the data of the join
     * @param string $leftTable - optional - default to table attribute -
     *     the left hand table to use in the join
     * @param string $joinId - optional - default to toTable parameter
     *     a unique name to identify this join operation - usefull if a table is involved
     *     into joins more than once
     * @param array $fields - optional - default to the field list of the toTable
     *     the list of fields to extract from the right table
     * @param string $type - optional - default to empty string
     *     the join mode - possible values are: INNER, OUTER, LEFT, RIGHT, depending
     *     on the underlying sql database
     * @return SQLManager
     * @throws Exception
     */
    protected function join($rightTable, $rightField, $leftField, $modelName = 'lighter\models\sql\SQLModel', $leftTable = null, $joinId = null, array $fields = null, $type = '') {
        if (isset($this->sql['join'])) {
            if ($leftTable == null) {
                $leftTable = $this->table;
            }
            if ($joinId != null) {
                $alias = ' AS '.$joinId;
            } else {
                $joinId = $rightTable;
                $alias = '';
            }
            if ($fields === null) {
                $fields = $this->database->getFields($rightTable);
            }
            if (!isset($this->joins[$joinId])) {
                $this->joins[$joinId] = array('from' => $leftTable, 'to' => $rightTable, 'model' => $modelName, 'field' => $rightField);//$rightTable;
            } else {
                throw new Exception('Join id already in use.');
            }
            $this->select($fields, $joinId);
            $this->sql['join'].= ' '.$type.' JOIN '.$rightTable.$alias.' ON '.$this->field($rightField, $joinId).' = '.$this->field($leftField, $leftTable);
        } else {
            throw new Exception('Cannot apply a join clause on this query.');
        }
        return $this;
    }


    /**
     * add a where clause to the current query.
     *
     * @param string $where
     * @param string $logicalOperator - optional - `AND` or `OR`
     */
    protected function where($where, $logicalOperator = 'AND') {
        if (isset($this->sql['where'])) {
            if ($this->sql['where'] == '') {
                $this->sql['where'] = ' WHERE '.$where;
            } else {
                $this->sql['where'].= ' '.$logicalOperator.' '.$where;
            }
        } else {
            throw new Exception('Cannot apply a where clause on this query.');
        }
        return $this;
    }


    /**
     * specify a limit clause for this query. Can be called only once.
     *
     * @param int $limit
     *     the max number of entries to return
     * @param int $offset - optional - default to null (none)
     *     the offset to apply to the query
     * @return SQLManager
     */
    protected function slice($limit, $offset = null) {
        if (isset($this->sql['limit'])) {
            $this->sql['limit'] = ' LIMIT ';
            if ($offset != null) {
                $this->sql['limit'].= $offset.', ';
            }
            $this->sql['limit'].= $limit;
        } else {
            throw new Exception('Cannot apply a limit clause on this query.');
        }
        return $this;
    }


    /**
     * specify an order clause for this query. Can be called more than once.
     *
     * @param string $field
     *     the field name to use, not formatted
     * @param string $order - optional - default to `ASC`
     *     the sort direction - possible values are `ASC` or `DESC`
     * @param string $joinId - optional - default to table attribute
     *     the joinId to use to identify the right field
     * @return SQLManager
     */
    protected function order($field, $order = 'ASC', $joinId = null) {
        if (isset($this->sql['order'])) {
            if ($joinId == null) {
                $joinId = $this->table;
            }
            $clause = $this->fieldAlias($field, $joinId).' '.$order;
            if ($this->sql['order'] == '') {
                $this->sql['order'] = ' ORDER BY '.$clause;
            } else {
                $this->sql['order'].= ', '.$clause;
            }
        } else {
            throw new Exception('Cannot apply an order clause on this query.');
        }
        return $this;
    }


    /**
     * @see lighter\models.Persistence::sort()
     */
    protected function sort(array $sort) {
        foreach ($sort as $order) {
            $this->order($order['field'], $order['order']);
        }
    }


    /**
     * return a correctly formatted complete field name to use in a query, based on the
     *    joinId (table name or alias) and the field name.
     *
     * @param string $field - field name
     * @param string $joinId - optional - default to table attribute
     *     table name or alias
     * @return string
     */
    protected function field($field, $joinId = null) {
        if ($joinId == null) {
            $joinId = $this->table;
        }
        $quote = $this->fieldQuote;
        return $quote.$joinId.$quote.'.'.$quote.$field.$quote;
    }


    /**
     * return a correctly formatted field alias to alias fields of joined query
     *
     * @param string $field - field name
     * @param string $joinId - optional - default to table attribute
     *     table name or alias
     * @return string
     */
    protected function fieldAlias($field, $joinId = null) {
        if ($joinId == null) {
            $joinId = $this->table;
        }
        return $joinId.$this->fieldPrefixSeparator.$field;
    }


    /**
     * add a value to use to execute the query
     *
     * @param mixed $value
     * @return SQLManager
     */
    protected function addValue($value) {
        $this->values[] = $value;
        return $this;
    }


    /**
     * add many values in one call
     *
     * @param array $values
     * @return SQLManager
     */
    protected function addValues(array $values) {
        $this->values = array_merge($this->values, $values);
        return $this;
    }


    /**
     * this add the values to the sql manager instance and generate a comma
     * separated serie of ?. This is for prepared statements series of values,
     * for use in IN statement for example or in insert statement.
     *
     * @param array $values
     * @return string
     */
    protected function generateSerie(array $values) {
        $this->addValues($values);
        return implode(', ', array_fill(0, count($values), '?'));
    }


    /**
     * execute a query
     *
     * @param array $values - optional - the values to use to execute the prepared statement
     */
    protected function execute() {
        $this->lastSql = implode('', $this->sql);
        $this->lastValues = $this->values;
        $result = true;
        if ($this->sendQuery) {
            // TODO find a way to do that
//             if (Configuration::getValue('main', 'debug', false)) {
//                 $startTimer = Debug::microtimeFloat();
//             }
            $result = $this->database->execute($this->lastSql, $this->values);
            if (!$result) {
                $this->addError($this->database->getLastError());
            }
//             if (Configuration::getValue('main', 'debug', false)) {
//                 Debug::addQuery($this->lastSql, 'SQLManager::', Debug::microtimeFloat() - $startTimer);
//             }
        }
        return $result;
    }

}
