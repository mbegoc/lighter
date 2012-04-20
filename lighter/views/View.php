<?php
namespace lighter\views;


use lighter\handlers\HttpResponse;
use lighter\handlers\TemplateEngine;

use lighter\helpers\html\HtmlHeader;

use \Exception;


/**
 * The very basic View class. Instantiate the TemplateEngine and display a content,
 * regarding the providing template.
 *
 * @name View
 * @abstract
 * @package lighter
 * @subpackage views
 * @since 0.1
 * @version 0.1
 * @author Michel Begoc
 * @copyright (c) 2011 Michel Begoc
 * @license MIT - see http://www.opensource.org/licenses/mit-license.php
 *
 */
abstract class View {
    /**
     * the template engine
     *
     * @var handlers\TemplateEngine
     */
    protected static $tplEngine;
    /**
     * the template to display
     *
     * @var string
     */
    protected $mainTemplate;
    /**
     * a messages list to display
     *
     * @var string
     */
    protected $messages = "";


    /**
     * constructor
     *
     * @param string $template
     */
    public function __construct($mainTemplate) {
        $this->mainTemplate = $mainTemplate;

        if (!isset(self::$tplEngine)) {
            self::$tplEngine = new TemplateEngine();
        }
    }


    /**
     * display the main template of the page and return a boolean saying
     * if the method is supported
     *
     * @return boolean
     */
    public function displayHtml() {
        self::$tplEngine->addVar("view", $this);
        self::$tplEngine->display($this->mainTemplate);
        return true;
    }


    /**
     * display the content in XML format and return a boolean saying if the
     * method is supported
     *
     * @return boolean
     */
    public function displayXml() {
        return false;
    }


    /**
     * display the content in JSON format and return a boolean saying if
     * the method is supported
     *
     * @return boolean
     */
    public function displayJson() {
        return false;
    }


    /**
     * add a message to the page
     *
     * @param string $message
     * @param string $class
     */
    public function addMessage($message, $class = NULL) {
        if (isset($class)) {
            $class = " class='$class'";
        }
        $this->messages.= "<p$class>$message</p>";
    }


    /**
     * return the messages
     *
     * @return string
     */
    public function getMessages() {
        return $this->messages;
    }

}


/**
 * The exception thrown by the Views classes
 *
 * @name ViewException
 * @package lighter
 * @subpackage views
 * @since 0.1
 * @version 0.1
 * @author Michel Begoc
 * @copyright (c) 2011 Michel Begoc
 * @license MIT - see http://www.opensource.org/licenses/mit-license.php
 *
 */
class ViewException extends Exception {}


