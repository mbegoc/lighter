<?php
namespace lighter\handlers;


use \Exception;


/**
 * This object represent the HttpRequest receiveid by the serveur.
 * It can provide the parameters of the request (GET or POST) and it extract a lot of
 * useful information such as HTTP method used, accepted data format, etc.
 *
 * This class has been writed in regard of the HTTP RFC 2616.
 * See http://www.w3.org/Protocols/rfc2616/rfc2616.html
 *
 * @name HttpRequest
 * @package lighter
 * @subpackage handlers
 * @since 0.1
 * @version 0.1
 * @author Michel Begoc
 * @copyright (c) 2011 Michel Begoc
 * @license MIT - see http://www.opensource.org/licenses/mit-license.php
 *
 */
class HttpRequest {
    /**
     * the singlton instance
     *
     * @staticvar lighter\handlers\HttpRequest
     */
    private static $instance = NULL;
    /**
     * the parameters list passed to the page
     *
     * @var array
     */
    private $parameters;
    /**
     * The HTTP method used to call the resource.
     *
     * @var string
     */
    private $method;
    /**
     * The accepted data type.
     *
     * @var string
     */
    private $accept;
    /**
     * The expected charset of the response.
     *
     * @var string
     */
    private $charset;
    /**
     * The expected language of the resource.
     *
     * @var string
     */
    private $language;


    /**
     * private singleton constructor
     */
    private function __construct(){
        $this->method = strtoupper($_SERVER['REQUEST_METHOD']);
        //some browser can't send other method but GET and POST: we accept a method parameter to be able to handle this
        if(isset($_REQUEST['method']) && ($_REQUEST['method'] == 'PUT' || $_REQUEST['method'] == 'DELETE')){
            $this->method = $_REQUEST['method'];
        }
        $this->accept = explode(',', $_SERVER['HTTP_ACCEPT']);
        $this->charset = $_SERVER['HTTP_ACCEPT_CHARSET'];
        $this->language = $_SERVER['HTTP_ACCEPT_LANGUAGE'];

        if($this->method == 'PUT' || $this->method == 'DELETE'){
            $params = parse_str(file_get_contents('php://input'), $this->_put_args);
            $this->parameters = array_merge($_REQUEST, $params);
        }else{
            $this->parameters = $_REQUEST;
        }

    }


    /**
     * singleton getInstance method
     *
     * @static
     * @return lighter\handlers\HttpRequest
     */
    public static function getInstance(){
        if(!isset(self::$instance)){
            self::$instance = new self();
        }

        return self::$instance;
    }


    /**
     * method getter
     *
     * @return string
     */
    public function getMethod(){
        return $this->method;
    }


    /**
     * return first accepted data format.
     *
     * @todo offer the possibility to retrieve the next accept if first can't be
     * returned
     * @return string
     */
    public function getAccept(){
        return $this->accept[0];
    }


    /**
     * charset getter
     *
     * @return string
     */
    public function getCharset(){
        return $this->charset;
    }


    /**
     * language getter
     *
     * @return string
     */
    public function getLanguage(){
        return $this->language;
    }


    /**
     * return the value of the string parameter provided by $name
     * return NULL if parameter doesn't exist
     *
     * @param string $name
     * @return string
     */
    public function getString($name){
        if(isset($this->parameters[$name])){
            $string = trim($this->parameters[$name]);
            if($string != ""){
                return $string;
            }
        }
        return NULL;
    }


    /**
     * return the value of the int parameter provided by $name
     * return NULL if parameter doesn't exist
     *
     * @param int $name
     */
    public function getInt($name){
        if(isset($this->parameters[$name]) && trim($this->parameters[$name]) != ""){
            return (int)$this->parameters[$name];
        }else{
            return NULL;
        }
    }


    /**
     * return the value of the float parameter provided by $name
     * return NULL if parameter doesn't exist
     *
     * @param float $name
     */
    public function getFloat($name){
        if(isset($this->parameters[$name]) && trim($this->parameters[$name]) != ""){
            return (float)$this->parameters[$name];
        }else{
            return NULL;
        }
    }


    /**
     * return the value of the boolean parameter provided by $name
     * return NULL if parameter doesn't exist
     *
     * @param boolean $name
     */
    public function getBool($name){
        if(isset($this->parameters[$name]) && trim($this->parameters[$name]) != ""){
            return (bool)$this->parameters[$name];
        }else{
            return NULL;
        }
    }

}


/**
 *
 *
 * @name HttpException
 * @package lighter
 * @subpackage handlers
 * @since 0.1
 * @version 0.1
 * @author Michel Begoc
 * @copyright (c) 2011 Michel Begoc
 * @license MIT - see http://www.opensource.org/licenses/mit-license.php
 *
 */
class HttpException extends Exception {}

