<?php
namespace dto;


use handlers\Debug;

class Config extends DataObject {
    private static $instance = NULL;

    public function __construct(array $doc = NULL){
        if($doc != NULL){
            parent::__construct($doc);
        }
    }


    /**
     * singleton getInstance method
     * @return Config
     */
    public static function getInstance(){
        if(!isset(self::$instance)){
            $dba = new DBAccessor("config");
            $dba->search(array(), 1, 0, array('date' => -1));
            self::$instance = $dba->next();
            if(!isset(self::$instance)){
                self::$instance = new self();
            }
        }

        return self::$instance;
    }


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


    public function setSessionData($timeout, $autoclean){
        $this->doc['session']['timeout'] = (int)$timeout;
        $this->doc['session']['autoclean'] = (boolean)$autoclean;
    }


    public function setApplicationPath($root, $relative){
        if($root == ''){
            throw new ConfigException('Absolute path of the application can\'t be empty', 1);
        }
        $this->doc['path']['root'] = $root;
        $this->doc['path']['relative'] = $relative;
        $this->doc['path']['full'] = $root.$relative;
    }


    public function setDefaultController($class, $method){
        if($class == '' || $method == ''){
            throw new ConfigException('Default controller class and method can\'t be empty', 2);
        }
        $this->doc['controller']['class'] = $class;
        $this->doc['controller']['method'] = $method;
    }


    public function setDefaultView($class){
        $this->doc['view']['class'] = $class;
    }


    public function setDebugData($configPath, $active){
        $this->doc['debug']['configPath'] = $configPath;
        $this->doc['debug']['active'] = (boolean)$active;
    }


    public function setIndexFile($isIndexFileNeeded){
        $this->doc['path']['needIndexFile'] = (boolean)$isIndexFileNeeded;
    }


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


    public function prepareToDb(){
        parent::prepareToDb(__CLASS__);
        unset($this->doc['_id']);
        $this->doc['date'] = time();
        \handlers\Debug::getInstance()->dump($this->doc);
    }


    public function getTemplatePath(){
        return $this->doc['template']['path'];
    }


    public function getTemplateExt(){
        return $this->doc['template']['extension'];
    }


    public function getApplicationFullPath(){
        return $this->doc['path']['full'];
    }


    public function getApplicationRootPath(){
        return $this->doc['path']['root'];
    }


    public function getApplicationRelativePath(){
        return $this->doc['path']['relative'];
    }


    public function getControllerClass(){
        return $this->doc['controller']['class'];
    }


    public function getControllerMethod(){
        return $this->doc['controller']['method'];
    }


    public function getMainViewName(){
        return $this->doc['view']['class'];
    }

    public function getDebugConfigPath(){
        return $this->doc['debug']['configPath'];
    }

    public function isDebugActive(){
        return $this->doc['debug']['active'];
    }

    public function needIndexFile(){
        return $this->doc['path']['needIndexFile'];
    }
}


class ConfigException extends \Exception {}