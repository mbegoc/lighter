<?php
namespace views;

use handlers\TemplateEngine;


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
	 * display the template content
	 */
	public function display(){
	    self::$tplEngine->display($this->template);
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
     * return the content in XML format
     */
    public function displayXml(){
        //TODO have to return the right http code
        throw new Exception("No XML content.");
    }


    /**
     * return the content in JSON format
     * @throws Exception
     */
    public function displayJson(){
        //TODO have to return the right http code
        throw new Exception("No JSON content.");
    }

}

