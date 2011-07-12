<?php
namespace helpers;


use dto\Config;


class Path {
    /**
     * instance
     * @var Path
     */
    private static $instance = NULL;
    /**
     * Config object
     * @var dto\Config
     */
    protected $config;
    /**
     * the index file if needed
     * @var string
     */
    protected $indexFile = '';


    private function __construct(){
        $this->config = Config::getInstance();
        if($this->config->needIndexFile()){
            $this->indexFile = 'index.php/';
        }
    }


    public function getInstance(){
        if(!isset(self::$instance)){
            self::$instance = new self();
        }
        return self::$instance;
    }




    /**
     *
     * Enter description here ...
     * @param string $name
     */
    public function getImgPath($name){
        return $this->config->getApplicationRelativePath().'include/img/'.$name;
    }


    public function prefixURI($uri){
        return $this->config->getApplicationRelativePath().$this->indexFile.$uri;
    }
}