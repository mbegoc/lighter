<?php
namespace lighter\models\sql;


use lighter\exceptions\AlreadyInUseException;

use lighter\handlers\Config;

use \PDO;


/**
 * handle a SQL database through the pdo PHP extension. Encapsulate this extension
 * so the SQLManager can easily access database.
 *
 * @name Database
 * @package lighter
 * @subpackage models\sql
 * @see http://php.net/manual/en/book.pdo.php
 * @since 0.1.1
 * @version 0.1.1
 * @author Michel Begoc
 * @copyright (c) 2011 Michel Begoc
 * @license MIT - see http://www.opensource.org/licenses/mit-license.php
 *
 */
class Database {
    /**
     *  pool of PDO objects - one instance per opened database connexion
     * @var array
     */
    protected static $pdos = array();
    /**
     * cache of tables fields
     * @var array
     */
    protected static $fields = array();
    /**
     * the current PDO instance
     * @var PDO
     */
    protected $pdo = null;
    /**
     * the statement prepared from a SQL query
     * @var PDOStatement
     */
    protected $statement = null;
    /**
     * the default table prefix for this connection
     * @var string
     */
    protected $tablePrefix = '';
    /**
     * the character to use to protect the sql fields
     * @var string
     */
    protected $fieldProtector = '`';
    /**
     * the last error
     * @var string
     */
    protected $lastError = null;


    /**
     * create a new connection to a db identified by $dbName in configuration file
     * @param string $dbName - optional - default to first entry in config file
     *     the db name
     */
    public function __construct($dbName) {
        $config = Config::getInstance()->getValue('mysql', $dbName);

        if (!isset(self::$pdos[$dbName])) {
            self::$pdos[$dbName] = new PDO($config['dsn'], $config['user'], $config['pass'], $config['params']);
        }
        if (isset($config['prefix']))
            $this->tablePrefix = $config['prefix'];
        if (isset($config['fieldProtector']))
            $this->fieldProtector = $config['field_protector'];
        $this->pdo = self::$pdos[$dbName];
    }


    /**
     * properly close statement if needed
     */
    public function __destruct() {
        if (is_object($this->statement)) {
            $this->statement->closeCursor();
        }
    }


    /**
     * execute a sql query
     *
     * @param string $sql
     *     the query
     * @param array $values
     *     the values to use
     * @return boolean
     * @throws lighter\exceptions\AlreadyInUseException
     */
    public function execute($sql, array $values = array()) {
        if (!is_object($this->statement)) {
            $this->statement = $this->pdo->prepare($sql);
            if (!$this->statement->execute($values)) {
                $this->lastError = $this->statement->errorInfo();
                return false;
            } else {
                return true;
            }
        } else {
            throw new AlreadyInUseException('Database link already in use.');
        }
    }


    /**
     * return the next result of a query or false if no more results
     *
     * @return array | boolean
     */
    public function next() {
        return $this->statement->fetch(PDO::FETCH_ASSOC);
    }


    /**
     * return all the results of a query in one shot
     * FIXME ensure we want to keep that. We didn't for MongoManager
     *
     * @return array
     */
    public function results() {
        return $this->statement->fetchAll(PDO::FETCH_ASSOC);
    }


    /**
     *
     * @return string
     */
    public function getInsertId() {
        return $this->pdo->lastInsertId();
    }


    /**
     * return the number of affected rows by a query
     *
     * @return int
     */
    public function getRowCount() {
        if (is_object($this->statement)) {
            return $this->statement->rowCount();
        } else {
            return 0;
        }
    }


    /**
     * begin a sql transaction
     */
    public function beginTransation() {
        $this->pdo->beginTransaction();
    }


    /**
     * commit a sql transaction
     */
    public function commit() {
        $this->pdo->commit();
    }


    /**
     * rollback a sql transaction
     */
    public function rollback() {
        $this->pdo->rollback();
    }


    /**
     * reset this object to its initial state
     */
    public function reset() {
        if (is_object($this->statement)) {
            $this->statement->closeCursor();
            $this->statement = null;
            $this->lastError = null;
        }
    }


    /**
     * check if this object is free to be used
     *
     * @return boolean
     */
    public function isFree() {
        return is_object($this->statement);
    }


    /**
     * return the default table prefix for this database
     *
     * @return string
     */
    public function getPrefix() {
        return $this->tablePrefix;
    }


    public function getFieldProtector() {
        return $this->fieldProtector;
    }


    /**
     * get the fields list of a table of this database
     *
     * @param string $tableName
     * @return array
     */
    public function getFields($tableName) {
        if (isset(self::$fields[$tableName])) {
            return self::$fields[$tableName];
        } else {
            // TODO integrate a cache system to lighter to be able to manage this cache
//             $config = Configuration::getValue('cache', 'tableFields', null);
//             if ($config != null) {
//                 $cacheKey = 'pepper:tableFields:'.$tableName;
//                 $cache = SimpleCache::loadFromConfig($config);
//                 if ($cache->isCached($cacheKey)) {
//                     return $cache->retrieve($cacheKey);
//                 } else {
                    $fields = $this->queryFields($tableName);
//                     $cache->cache($cacheKey, $fields);
                    return $fields;
//                 }
//             } else {
//                 return $this->queryFields($tableName);
//             }
        }
    }


    /**
     * return last error
     *
     * @return string
     */
    public function getLastError() {
        return $this->lastError;
    }


    /**
     * return a properly escaped string
     *
     * @param string $param
     */
    public function escape($param) {
        return $this->pdo->quote($param);
    }


    /**
     * properly clone this object
     */
    public function __clone() {
        $this->statement = clone $this->statement;
    }


    /**
     * retrieve the fields of a table from the database metadata system
     *
     * @param string $tableName
     */
    protected function queryFields($tableName) {
        $this->execute('SELECT column_name AS name, '.
            'data_type AS type, '.
            "IF(is_nullable='YES', 0, 1) AS notnull ".
            'FROM information_schema.columns '.
            'WHERE table_name = ? '.
            'AND table_schema = (SELECT DATABASE()) ', array($tableName));

        $fields = $this->statement->fetchAll(PDO::FETCH_ASSOC);
        foreach ($fields as $key=> $field) {
            $fields[$key]['notnull'] = (boolean)$field['notnull'];
        }
        $this->reset();
        return $fields;
    }

}
