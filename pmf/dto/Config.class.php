<?php
namespace pmf\dto;


use \Exception;


/**
 * the config class. This class permit to acess the config collection and is
 * accessible from the whole application to provide its data.
 *
 * @name Config
 * @package pmf
 * @subpackage dto
 * @see pmf\dto\DataAccessor
 * @since 0.1
 * @version 0.1
 * @author Michel Begoc
 * @copyright (c) 2011 Michel Begoc
 * @license MIT - see http://www.opensource.org/licenses/mit-license.php
 *
 */
class Config extends DataAccessor {
    /**
     * singleton instance
     * @staticvar Config
     */
    private static $instance = NULL;


    /**
     * protected constructor
     * as a subclass, the Config class can't restrict the visibility of the parent
     * class.
     */
    protected function __construct(){
        parent::__construct('config');
        $this->loadAll()->sort(array('date' => -1))->slice(1);
        $this->next();
    }


    /**
     * singleton getInstance method
     *
     * @static
     * @return Config
     */
    public static function getInstance(){
        if(!isset(self::$instance)){
            self::$instance = new self();
        }

        return self::$instance;
    }


    /**
     * template data setter
     *
     * @param string $path
     * @param string $extension
     */
    public function setTemplateData($path, $extension){
        if($path == ''){
            $path = '/';
        }
        if($extension == ''){
            //FIXME ensure this default extension is correct
            $extension = '.php';
        }
        $this->doc['template']['path'] = $path;
        $this->doc['template']['extension'] = $extension;
    }


    /**
     * session data setter
     *
     * @param int $timeout
     * @param boolean $autoclean
     */
    public function setSessionData($timeout, $autoclean){
        $this->doc['session']['timeout'] = (int)$timeout;
        $this->doc['session']['autoclean'] = (boolean)$autoclean;
    }


    /**
     * application paths
     *
     * @param string $root
     * @param string $relative
     * @throws ConfigException
     */
    public function setApplicationPath($root, $relative){
        if($root == ''){
            throw new ConfigException('Absolute path of the application can\'t be empty', 1);
        }
        $this->doc['path']['root'] = $root;
        $this->doc['path']['relative'] = $relative;
        $this->doc['path']['full'] = $root.$relative;
    }


    /**
     * set the default root to use
     *
     * @param string $class
     * @param string $method
     */
    public function setDefaultController($class, $method){
        if($class == '' || $method == ''){
            throw new ConfigException('Default controller class and method can\'t be empty', 2);
        }
        $this->doc['controller']['class'] = $class;
        $this->doc['controller']['method'] = $method;
    }


    /**
     * debug data setter
     *
     * @param string $configPath
     * @param string $active
     */
    public function setDebugData($configPath, $active){
        $this->doc['debug']['configPath'] = $configPath;
        $this->doc['debug']['active'] = (boolean)$active;
    }


    /**
     * say wether the index.php file is needed or not.
     *
     * @param boolean $isIndexFileNeeded
     */
    public function setIndexFile($isIndexFileNeeded){
        $this->doc['path']['needIndexFile'] = (boolean)$isIndexFileNeeded;
    }


    /**
     * add an available language to the system.
     *
     * @param string $code
     * @param string $name
     */
    public function addLanguage($code, $name){
        if(strlen($code) != 2){
            throw new ConfigException("The language code shouldn't be longer than 2 characters.");
        }

        if(!isset($this->doc['language'])){
            $this->doc['language'] = array();
        }

        $lang['code'] = $code;
        $lang['name'] = $name;

        array_push($this->doc['language'], $lang);
    }


    /**
     * prepare the object to be save.
     */
    protected function prepareToDb(){
        unset($this->doc['_id']);
        $this->doc['date'] = time();
    }


    /**
     * @return string
     */
    public function getTemplatePath(){
        return $this->doc['template']['path'];
    }


    /**
     * @return string
     */
    public function getTemplateExt(){
        return $this->doc['template']['extension'];
    }


    /**
     * @return string
     */
    public function getApplicationFullPath(){
        return $this->doc['path']['full'];
    }


    /**
     * @return string
     */
    public function getApplicationRootPath(){
        return $this->doc['path']['root'];
    }


    /**
     * @return string
     */
    public function getApplicationRelativePath(){
        return $this->doc['path']['relative'];
    }


    /**
     * @return string
     */
    public function getControllerClass(){
        return $this->doc['controller']['class'];
    }


    /**
     * @return string
     */
    public function getControllerMethod(){
        return $this->doc['controller']['method'];
    }


    /**
     * @return string
     */
    public function getMainViewName(){
        return $this->doc['view']['class'];
    }


    /**
     * @return string
     */
    public function getDebugConfigPath(){
        return $this->doc['debug']['configPath'];
    }


    /**
     * @return boolean
     */
    public function isDebugActive(){
        return $this->doc['debug']['active'];
    }


    /**
     * @return boolean
     */
    public function needIndexFile(){
        return $this->doc['path']['needIndexFile'];
    }

}


/**
 * The Exception thrown by the Config object.
 *
 * @author michel
 *
 */
class ConfigException extends Exception {}