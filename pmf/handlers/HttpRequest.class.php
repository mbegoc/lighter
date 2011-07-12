<?php
namespace handlers;


use \Exception;


class HttpRequest {
    private static $instance = NULL;
    private $parameters;
    private $method;
    private $accept;
    private $charset;
    private $language;


    private function __construct(){
        $this->method = strtoupper($_SERVER['REQUEST_METHOD']);
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
     *
     * @return handlers\HttpRequest
     */
    public function getInstance(){
        if(!isset(self::$instance)){
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function getMethod(){
        return $this->method;
    }

    public function getAccept(){
        return $this->accept[0];
    }

    public function getCharset(){
        return $this->charset;
    }

    public function getLanguage(){
        return $this->language;
    }

	public function getString($name){
		if(isset($this->parameters[$name])){
			$string = trim($this->parameters[$name]);
			if($string != ""){
				return $string;
			}
		}
		return NULL;
	}


	public function getInt($name){
		if(isset($this->parameters[$name]) && trim($this->parameters[$name]) != ""){
			return (int)$this->parameters[$name];
		}else{
			return NULL;
		}
	}


	public function getFloat($name){
		if(isset($this->parameters[$name]) && trim($this->parameters[$name]) != ""){
			return (float)$this->parameters[$name];
		}else{
			return NULL;
		}
	}


	public function getBool($name){
		if(isset($this->parameters[$name]) && trim($this->parameters[$name]) != ""){
			return (bool)$this->parameters[$name];
		}else{
			return NULL;
		}
	}

}


class HttpException extends Exception {}

