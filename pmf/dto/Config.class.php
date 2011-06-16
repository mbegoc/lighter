<?php
namespace dto;


class Config extends DataObject {
    private static $instance = NULL;

    public function __construct(array $doc = array('class' => __CLASS__)){
        parent::__construct($doc);
    }


    /**
     * singleton getInstance method
     * @return Config
     */
    public static function getInstance(){
        if(!isset(self::$instance)){
            $dba = new DBAccessor("config");
            $dba->search();
            self::$instance = $dba->next();
            if(!isset(self::$instance)){
                self::$instance = new self();
            }
        }

        return self::$instance;
    }


    public function setTemplateData($path, $extension){
        $this->doc['template']['path'] = $path;
        $this->doc['template']['extension'] = $extension;
    }


    public function setSessionData($timeout, $autoclean){
        $this->doc['session']['timeout'] = (int)$timeout;
        $this->doc['session']['autoclean'] = (boolean)$autoclean;
    }


    public function setApplicationPath($root, $relative){
        $this->doc['path']['full'] = $root.$relative;
        $this->doc['path']['relative'] = $relative;
    }


    public function setDefaultController($class, $method){
        $this->doc['controller']['class'] = $class;
        $this->doc['controller']['method'] = $method;
    }


    public function setDefaultView($class, $method){
        $this->doc['view']['class'] = $class;
        $this->doc['view']['method'] = $method;
    }


    public function setDebugData($configPath, $active){
        $this->doc['debug']['configPath'] = $configPath;
        $this->doc['debug']['active'] = (boolean)$active;
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


    public function validate(){
        unset($this->doc['_id']);
        $this->doc['date'] = time();
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
}


class ConfigException extends \Exception {}