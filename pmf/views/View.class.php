<?php
namespace views;


use handlers\HttpResponse;
use handlers\TemplateEngine;

use \Exception;


/**
 * The very basic View class. Instantiate the TemplateEngine and display a content,
 * regarding the providing template.
 *
 * @author michel
 *
 */
abstract class View {
    /**
     * the template engine
     * @var handlers\TemplateEngine
     */
	protected static $tplEngine;
	/**
	 * the template to display
	 * @var string
	 */
	protected $template;
    /**
     * a messages list to display
     * @var string
     */
    private $messages = "";


	/**
	 * constructor
	 * @param string $template
	 */
	public function __construct($template){
	    $this->template = $template;

	    if(!isset(self::$tplEngine)){
	        self::$tplEngine = new TemplateEngine($template);
	    }
	}


	/**
	 * display the template content and return a boolean saying if the method is supported
	 * @return boolean
	 */
	public function display(){
	    self::$tplEngine->display($this->template);
	    return true;
	}


    /**
     * add a message to the page
     * @param string $message
     * @param string $class
     */
    public function addMessage($message, $class = NULL){
        if(isset($class)){
            $class = " class='$class'";
        }
        $this->messages.= "<p$class>$message</p>";
    }


    /**
     * return the messages
     * @return string
     */
    public function getMessages(){
        return $this->messages;
    }


    /**
     * display the content in XML format and return a boolean saying if the method is supported
     * @return boolean
     */
    public function displayXml(){
        return false;
    }


    /**
     * display the content in JSON format and return a boolean saying if the method is supported
     * @return boolean
     */
    public function displayJson(){
        return false;
    }

}


class ViewException extends Exception {}

