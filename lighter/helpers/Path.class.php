<?php
namespace lighter\helpers;


use lighter\models\Config;

/**
 * Class Path: provide helper method for handling paths to resources
 *
 * @name Config
 * @package lighter
 * @subpackage models
 * @see lighter\models\DataAccessor
 * @since 0.1
 * @version 0.1
 * @author Michel Begoc
 * @copyright (c) 2011 Michel Begoc
 * @license MIT - see http://www.opensource.org/licenses/mit-license.php
 *
 */
class Path {
    /**
     * instance
     *
     * @var Path
     */
    private static $instance = NULL;
    /**
     * Config object
     *
     * @var models\Config
     */
    protected $config;
    /**
     * the index file if needed
     *
     * @var string
     */
    protected $indexFile = '';


    /**
     * private singleton constructor
     */
    private function __construct(){
        $this->config = Config::getInstance();
        if($this->config->needIndexFile()){
            $this->indexFile = 'index.php/';
        }
    }


    /**
     * singleton getInstance method
     *
     * @static
     */
    public function getInstance(){
        if(!isset(self::$instance)){
            self::$instance = new self();
        }
        return self::$instance;
    }


    /**
     * provide the path for a static image
     * 
     * @param string $name
     */
    public function getImgPath($name){
        return $this->config->getApplicationRelativePath().'include/img/'.$name;
    }


    /**
     * correctly prefix a relative uri
     *
     * @param string uri
     */
    public function prefixURI($uri){
        return $this->config->getApplicationRelativePath().$this->indexFile.$uri;
    }

}


