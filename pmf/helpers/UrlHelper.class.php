<?php
class UrlHelper {
	private $controller;
	private $function;
	private $resource;
	
	public function __construct($controller, $function, $resourceId, $resourceTitle = NULL){
		$this->controller = $controller;
		$this->function = $function;
		$this->resource = array($resourceId, $resourceTitle)
	}
	
	public function getUrl(){
		$config = Config::getInstance();
		$url = $config->xml->global->path["relative"];
		$url.= $this->controller."/";
		$url.= $this->function."/";
		if($this->resource[1] != NULL){
			$url.= self::prepareString($this->resource[1]);
		}
		$url.= "-".$this->resource[0]."/";
		
		return $url;
	}
	
	public static function prepareString($string){
		$string = strtolower($string);
		$string = trim($string);
		$string = preg_replace("#\s+#", "-", $string);
		
		return $string;
	}
}