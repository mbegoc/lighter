<?php
namespace views;


use handlers\HttpResponse;
use handlers\TemplateEngine;

use html\HtmlHeader;

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
	protected $mainTemplate;
	/**
	 * the content template
	 * @var string
	 */
	protected $contentTemplate;
	/**
     * a messages list to display
     * @var string
     */
    private $messages = "";


	/**
	 * constructor
	 * @param string $template
	 */
	public function __construct($mainTemplate, $contentTemplate){
	    $this->mainTemplate = $mainTemplate;
	    $this->contentTemplate = $contentTemplate;

	    if(!isset(self::$tplEngine)){
	        self::$tplEngine = new TemplateEngine();
	    }
	}


	/**
	 * display the main template of the page and return a boolean saying if the method is supported
	 * @return boolean
	 */
	public final function display(){
        self::$tplEngine->addObject("view", $this);
        self::$tplEngine->addObject("htmlHeader", HtmlHeader::getInstance());
        self::$tplEngine->display($this->mainTemplate);
	    return true;
	}


	/**
	 * return the HTML actual content of the page
	 * @return string
	 */
	public function getContent(){
	    return self::$tplEngine->get($this->contentTemplate);
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

